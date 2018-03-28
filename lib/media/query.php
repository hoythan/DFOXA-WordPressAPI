<?php

namespace media;

use cached\cache;

class query
{
    private $cache_group = 'query_media_count';

    public function run()
    {
        $query = bizContentFilter(array(
            'paged',
            'post_mime_types',
            'posts_per_page'
        ));

        $paged = isset($query->paged) ? absint($query->paged) > 0 ? absint($query->paged) : 1 : 1;
        $posts_per_page = isset($query->posts_per_page) ? absint($query->posts_per_page) > 0 ? absint($query->posts_per_page) : 20 : 20;
        $post_mime_types = isset($query->post_mime_types) ? $query->post_mime_types : [];

        $posts = get_children(array(
            'paged' => $paged,
            'posts_per_page' => $posts_per_page,
            'post_type' => 'attachment',
            'post_mime_type' => $post_mime_types
        ));

        $attachments = [];
        foreach ($posts as $post) {
            $metadata = wp_get_attachment_metadata($post->ID);
            $fileMime = wp_check_filetype($post->guid);
            $attachment = array(
                "id" => $post->ID,
                "file_name" => basename($post->guid),
                "file_type" => $fileMime['type'],
                "url" => $post->guid,
                "title" => $post->post_title,
                "caption" => $post->post_excerpt,
                "alt_text" => get_post_meta($post->ID, '_wp_attachment_image_alt', true),
                "description" => $post->post_content,
                "uploaded_by" => get_the_author_meta('display_name', $post->post_author),
                "uploaded_on" => $post->post_date,
                "file_size" => size_format(filesize(get_attached_file($post->ID)))
            );

            if (wp_attachment_is_image($post->ID)) {
                $attachment['dimensions'] = $metadata['width'] . ' x ' . $metadata['height'];
            } else if (empty($metadata['length_formatted'])) {
                $attachment['length'] = $metadata['length_formatted'];
            }

            $attachments[] = apply_filters('dfoxa_media_query_file_attachment', $attachment, $post->ID);
        }

        $attachments = apply_filters('dfoxa_media_query', $attachments);
        dfoxaGateway(array('files' => $attachments, 'count' => $this->get_media_count($post_mime_types)));
    }

    /**
     * 获取相关资源类型的数量
     * @param array $post_mime_types
     * @return bool|number
     */
    private function get_media_count($post_mime_types = array())
    {
        $cacheObj = new cache();

        $key = implode(',', $post_mime_types);
        $count = $cacheObj->get($key, $this->cache_group, false);
        if (!$count) {
            $count = 0;
            $countObj = wp_count_attachments($post_mime_types);

            unset($countObj->trash); // 不需要统计被删除的资源

            foreach ($countObj as $key => $val) {
                $count += (int)$val;
            }
            $cacheObj->set(implode(',', $post_mime_types), $count, $this->cache_group);
        }

        return $count;
    }

    /**
     * 清空统计缓存
     */
    public function clear_media_count($file = '')
    {
        $cacheObj = new cache();
        $cacheObj->clearGroup($this->cache_group);
        return $file;
    }
}

add_filter('wp_handle_upload', array(new \media\query(), 'clear_media_count'), 10, 1);
add_filter('wp_delete_file', array(new \media\query(), 'clear_media_count'), 10, 1);