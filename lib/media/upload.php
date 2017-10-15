<?php

namespace media;

use account\token\verify as Verify;
use Respect\Validation\Validator as Validator;
use gateway\mothod as Gateway;

require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');

class upload extends file
{
    public function run()
    {
        $attachments = $this->update();
        /*
         * 如果只有上传单个文件,则清理数组
         */
        $ret = [];
        if (count($attachments) === 1 && count($_FILES) === 1) {
            $ret['file'] = $attachments[0];
        } else {
            $ret['files'] = $attachments;
        }
        Gateway::responseSuccessJSON($ret);
    }

    public function _run()
    {
        // 验证身份
        $user = Verify::check('', true);
        $userdata = get_userdata($user['userid']);

        $role = get_option('dfoxa_media_user');
        $userrole = $userdata->roles[0];
        $roles = array_keys(wp_roles()->roles);
        $level = 99999;
        $userlevel = 99999;
        foreach ($roles as $i => $r) {
            if ($r == $role) {
                $level = $i;
            }
            if ($r == $userrole) {
                $userlevel = $i;
            }
        }
        if ($userlevel > $level)
            dfoxaError('media.error-userlevel');

        // 验证文件
        if (!isset($_FILES))
            dfoxaError('media.empty-file');

        // 加载必备文件
        add_filter('sanitize_file_name', 'dfoxa_make_filename_hash', 10);

        // 后台限制
        $type = get_option('dfoxa_media_type');

        $types = array();
        if (!empty($type) && $type == '*') {
            $types = explode(',', $type);
        }
        // 回调配置
        $guids = array();
        $file_urls = array();
        $gateway = get_option('dfoxa_gateway');
        $file_url = home_url('/' . $gateway . '?method=media.get&file_id=');
        // 循环上传
        foreach ($_FILES as $fileKey => $file) {
            // 验证文件格式

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            if (count($types) > 0 && !in_array($ext, $types))
                dfoxaError('media.error-uploadfiletype');

            $result = media_handle_upload($fileKey, null);
            if (is_wp_error($result)) {
                dfoxaError('media.error-upload', array(
                    'sub_msg' => $result->get_error_message()
                ));
            }

            // 保存文件信息
            $guid = get_GUIDStr();
            update_fileMeta($guid, $result);
            $guids[] = $guid;
        }

        if (count($guids) == 1) {
            $guids = $guids[0];
            $file_urls = $file_url . $guids;
        } else {
            foreach ($guids as $guid)
                $file_urls[] = $file_url . $guid;
        }

        Gateway::responseSuccessJSON(array(
            'msg' => '文件上传成功',
            'file_id' => $guids,
            'file_url' => $file_urls
        ));
    }


    public function update()
    {
        $userid = Verify::check('', false);
        $userdata = get_userdata($userid);

        $upload_dir = wp_upload_dir();

        /*
         * 检查用户权限
         */
        $user_role = $userdata->roles[0];

        $type = get_option('dfoxa_media_user');
        $roles = get_option('dfoxa_media_user_role');

        switch ($type) {
            case "all":
                break;
            case "select":
                $check = false;
                foreach ($roles as $role) {
                    if ($user_role == $role) {
                        $check = true;
                        break;
                    }
                }

                if (!$check) {
                    dfoxaError('media.error-userlevel');
                }
                break;
            default:
                dfoxaError('media.empty-role');
        }

        // 验证文件是否上传
        if (!isset($_FILES) || empty($_FILES))
            dfoxaError('media.empty-file');


        $fileexts = explode(',', get_option('dfoxa_media_type'));
        foreach ($fileexts as $k => $ext) {
            $fileexts[$k] = strtolower($ext);
        }

        $attachments = [];
        foreach ($_FILES as $fileKey => $file) {

            /*
             * 格式验证
             */
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (count($fileexts) > 0 && !in_array($ext, $fileexts)) {
                dfoxaError('media.error-uploadfiletype');
            }

            /*
             * 类型验证
             */
            $type = wp_check_filetype($file['name']);
            $filedata = getimagesize($file['tmp_name']);

            if ($filedata['mime'] != $type['type']) {
                dfoxaError('media.error-uploadfiletype');
            }

            /*
             * 尺寸验证
             */
            $maxSize = get_option('dfoxa_media_size');
            if (Validator::size($maxSize)->validate($file['tmp_name'])) {
                dfoxaError('media.error-maxfilesize', array(
                    'sub_msg' => "请勿超过" . get_option('dfoxa_media_size')
                ));
            }

            /*
             * 重写文件名
             */
            $filename = get_MicroTimeStr();
            $_FILES[$fileKey]['name'] = $filename . ".{$ext}";
            $_FILES[$fileKey]['ext'] = $ext;

            $_FILES[$fileKey] = apply_filters('dfoxa_media_upload_file', $_FILES[$fileKey]);

            /*
             * 允许hook返回false,使用场景为需要将图片上传至其他接口,不上传至服务器.
             */
            if ($_FILES[$fileKey] === false)
                continue;

            $result = media_handle_upload($fileKey, null);
            if (is_wp_error($result)) {
                dfoxaError('media.error-upload', array(
                    'sub_msg' => $result->get_error_message()
                ));
            }

            $attachment = wp_get_attachment_metadata($result);
            $attachment['url'] = $upload_dir['baseurl'] . DFOXA_SEP . $attachment['file'];
            $attachment['dir'] = $upload_dir['basedir'] . DFOXA_SEP . $attachment['file'];
            $attachment['ext'] = $ext;

            $attachments[] = apply_filters('dfoxa_media_upload_file_attachment', $attachment);

        }

        return apply_filters('dfoxa_media_upload_files', $attachments);
    }
}