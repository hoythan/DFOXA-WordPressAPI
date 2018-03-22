<?php

namespace media;

class query
{
    public function run()
    {
        $query = bizContentFilter(array(
            'paged',
            'post_mime_type',
            'posts_per_page'
        ));

        $paged = isset($query->paged) ? absint($query->paged) > 0 ? absint($query->paged) : 1 : 1;
        $posts_per_page = isset($query->posts_per_page) ? absint($query->posts_per_page) > 0 ? absint($query->posts_per_page) : 20 : 20;
        $post_mime_type = isset($query->post_mime_type) ? $query->post_mime_type : '';

        $posts = get_posts(array(
            'paged' => $paged,
            'posts_per_page' => $posts_per_page,
            'post_mime_type' => $post_mime_type,
            'post_type' => 'attachment'
        ));

        $attachments = apply_filters('dfoxa_media_query', $posts);
        exit;
    }
}
