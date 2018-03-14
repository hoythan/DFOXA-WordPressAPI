<?php

namespace account\multi;
use account\token\verify as Verify;
class blog
{
    public static function get_current_blog_id()
    {
        $blog_id = 0;
        $query = bizContentFilter(array(
            'blog_id'
        ));

        if (!empty($query->blog_id)) {
            // 从用户请求中获取
            $blog_id = $query->blog_id;
        } else if (isset($_GET['blog_id'])) {
            // 从 URL地址 中获取
            $blog_id = $_GET['blog_id'];
        } else if (isset($_COOKIE['blog_id'])) {
            // 从 Cookie 中获取
            $blog_id = $_COOKIE['blog_id'];
        } else if (isset($_SERVER['HTTP_BLOG_ID'])) {
            // 从 请求头 获取
            $blog_id = $_SERVER['HTTP_BLOG_ID'];
        }

        if($blog_id === 0)
            dfoxaError('multi.empty-blogid');

        $ret = false;
        $blogs = get_blogs_of_user(Verify::getSignUserID(), false);
        foreach ($blogs as $blog) {
            if ($blog->userblog_id == $blog_id) {
                $ret = $blog_id;
                break;
            }
        }

        if ($ret === false)
            dfoxaError('multi.error-blogid');

        return $ret;
    }
}