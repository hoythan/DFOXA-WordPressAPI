<?php

namespace media;

use account\token\verify as Verify;
use Respect\Validation\Validator as Validator;
use wapmorgan\FileTypeDetector\Detector as FileTypeDetector;

require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');

class upload
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

            $blog_id = get_current_blog_id();
            $user = new \WP_User(
                $userid,
                '',
                $blog_id
            );
        } else {
            $user = get_userdata($userid);
        }

        $user_roles = $user->roles;

        $upload_dir = wp_upload_dir();
        if (is_multisite()) {
            $type = get_blog_option(get_main_site_id(), 'dfoxa_media_user');
            $roles = get_blog_option(get_main_site_id(), 'dfoxa_media_user_role');
        } else {
            $type = get_option('dfoxa_media_user');
            $roles = get_option('dfoxa_media_user_role');
        }


        switch ($type) {
            case "all":
                break;
            case "select":
                $check = false;
                foreach ($roles as $role) {
                    if (in_array($role, $user_roles)) {
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
        if (is_multisite()) {
            $media_type = get_blog_option(get_main_site_id(), 'dfoxa_media_type');
        } else {
            $media_type = get_option('dfoxa_media_type');
        }

        $fileexts = apply_filters('dfoxa_media_upload_fileext', explode(',', $media_type));
        foreach ($fileexts as $k => $ext) {
            $fileexts[$k] = strtolower($ext);
        }

        $attachments = [];
        foreach ($_FILES as $fileKey => $file) {
            /*
             * 格式验证
             */
            // 过滤文件名
            $file['name'] = sanitize_file_name($file['name']);

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (count($fileexts) > 0 && !in_array($ext, $fileexts) && !in_array('*', $fileexts)) {
                dfoxaError('media.error-uploadfiletype');
            }

            /*
             * 类型验证
             */
            $fileMime = wp_check_filetype($file['name']);
            if (FileTypeDetector::getMimeType($file['tmp_name']) != false && FileTypeDetector::getMimeType($file['tmp_name']) != $fileMime['type']) {
                dfoxaError('media.error-uploadfiletype');
            }

            /*
             * 尺寸验证
             */
            $media_size = is_multisite() ? get_blog_option(get_main_site_id(), 'dfoxa_media_size') : get_option('dfoxa_media_size');
            $maxSize = apply_filters('dfoxa_media_upload_filesize', $media_size);

            // 没有填写单位 默认为KB
            if ((string)$maxSize === (string)(int)$maxSize)
                $maxSize .= 'kb';

            if (Validator::size(null, $maxSize)->validate($file)) {
                $media_size = is_multisite() ? get_blog_option(get_main_site_id(), 'dfoxa_media_size') : get_option('dfoxa_media_size');
                dfoxaError('media.error-maxfilesize', array(
                    'sub_msg' => "请勿超过" . $media_size
                ));
            }

            /*
             * 重写文件名
             */
            $filename = get_micro_time_str();
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

            $metadata = wp_get_attachment_metadata($result);
            $post = get_post($result);
            wp_update_post(array(
                'ID' => $result,
                'post_title' => $file['name'],
                'post_author' => $userid
            ));

            $attachment = array(
                "id" => $result,
                "file_name" => $_FILES[$fileKey]['name'],
                "file_type" => $fileMime['type'],
                "url" => $post->guid,
                "title" => $file['name'],
                "caption" => $post->post_excerpt,
                "alt_text" => '',
                "description" => '',
                "uploaded_by" => get_the_author_meta('display_name', $userid),
                "uploaded_on" => $post->post_date,
                "file_size" => size_format($file['size'])
            );
            if (wp_attachment_is_image($result)) {
                $attachment['dimensions'] = $metadata['width'] . ' x ' . $metadata['height'];
                $attachment['sizes'] = $metadata['sizes'];
            } else if (empty($metadata['length_formatted'])) {
                $attachment['length'] = $metadata['length_formatted'];
            }

            $attachments[] = apply_filters('dfoxa_media_upload_file_attachment', $attachment, $result);

        }

        $attachments = apply_filters('dfoxa_medias_query', $attachments);
        return apply_filters('dfoxa_media_upload_files', $attachments);
    }
}