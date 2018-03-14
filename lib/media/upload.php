<?php

namespace media;

use account\token\verify as Verify;
use account\multi\blog as Blog;
use Respect\Validation\Validator as Validator;

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
        dfoxaGateway($ret);
    }

    public function update()
    {
        /**
         * 多站点的用户组获取方式不同,在此处进行判断区分
         */
        $userid = Verify::getSignUserID();
        if (is_multisite()) {

            $blog_id = Blog::get_current_blog_id();
            $user = new \WP_User(
                $userid,
                '',
                $blog_id
            );
        } else {
            $user = get_userdata($userid);
        }

        $user_role = $user->roles[0];

        $upload_dir = wp_upload_dir();
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
                break;
        }

        // 验证文件是否上传
        if (!isset($_FILES) || empty($_FILES))
            dfoxaError('media.empty-file');

        // 文件格式
        $fileexts = apply_filters('dfoxa_media_upload_fileext', explode(',', get_option('dfoxa_media_type')));
        foreach ($fileexts as $k => $ext) {
            $fileexts[$k] = strtolower($ext);
        }

        $attachments = [];
        foreach ($_FILES as $fileKey => $file) {
            /*
             * 格式验证
             */
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (count($fileexts) > 0 && !in_array($ext, $fileexts) && !in_array('*', $fileexts)) {
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
            $maxSize = apply_filters('dfoxa_media_upload_filesize', get_option('dfoxa_media_size'));

            // 没有填写单位 默认为KB
            if ((string)$maxSize === (string)(int)$maxSize)
                $maxSize .= 'kb';

            if (Validator::size(null, $maxSize)->validate($file)) {
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