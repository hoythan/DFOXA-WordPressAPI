<?php

namespace posts;
class get
{
    public function run()
    {
        // 开始查询文章,可在此 hook 做是否允许查询判断
        do_action('dfoxa_query_posts_get');

        $query = bizContentFilter(array(
            'query'
        ));

        // 检查或修改用户查询内容
        $query->query = apply_filters('dfoxa_query_posts_query', $query->query);

        $posts = get_posts(objectToArray($query->query));
        $rets = [];
        foreach ($posts as $post) {
            // 在此 hook 修改对用户返回的 post , 返回 false 将排除此文章
            $post = apply_filters('dfoxa_query_posts_get_post', $post, $query->query);

            if (!empty($post))
                $rets[] = $post;
        }

        // 在此 hook 修改对用户返回的内容，文章包含在 posts 中
        $rets = apply_filters('dfoxa_query_posts_get_posts', array('posts' => $rets), $query->query);
        dfoxaGateway($rets);
    }
}