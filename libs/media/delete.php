<?php

namespace media;

use account\token\verify as Verify;

class delete
{
    public function run()
    {
        $query = bizContentFilter(array(
            'attachment_id',
            'attachment_ids'
        ));

        $attachment_ids = $query->attachment_ids;
        if (!empty($query->attachment_id)) {
            $attachment_ids = array($query->attachment_id);
        }

        if (!is_array($attachment_ids))
            dfoxaError('media.empty-delete-file');

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

        foreach ($attachment_ids as $attachment_id) {
//            $response = wp_delete_attachment($attachment_id);
        }

    }
}