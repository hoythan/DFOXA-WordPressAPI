<?php

namespace media;

use account\token\verify as Verify;
use gateway\mothod as Gateway;

require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');

class upload extends file
{
    public function run()
    {
        // 验证身份
//        $user = Verify::check('', true);
//        $userdata = get_userdata($user['userid']);
//
//        $role = get_option('dfoxa_media_user');
//        $userrole = $userdata->roles[0];
//        $roles = array_keys(wp_roles()->roles);
//        $level = 99999;
//        $userlevel = 99999;
//        foreach ($roles as $i => $r) {
//            if ($r == $role) {
//                $level = $i;
//            }
//            if ($r == $userrole) {
//                $userlevel = $i;
//            }
//        }
//        if ($userlevel > $level)
//            throw new \Exception('media.error-userlevel');

        // 验证文件
        if (!isset($_FILES))
            throw new \Exception('media.empty-file');

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
                throw new \Exception('media.error-uploadfiletype');

            $result = media_handle_upload($fileKey, null);
            if (is_wp_error($result)) {
                set_AppendMsg('media.error-upload', array(
                    'sub_msg' => $result->get_error_message()
                ));
                throw new \Exception('media.error-upload');
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
}