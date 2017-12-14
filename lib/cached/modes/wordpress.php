<?php

namespace cached;

class wordpress
{
    public $group_prefix = 'group_cache_';

    /*
     * 设置缓存
     *
     * 缓存时间需转换为秒
     */
    public function set($key, $value = '', $group = '', $expire = 0)
    {
        /**
         * 如果group不为空,则创建clearGroup支持项
         */
        if ($group != '') {
            $keys = $this->group_prefix . $group;
            if (!$this->get($keys) || !is_array($this->get($keys))) {
                /*
                 * 添加到组缓存
                 */
                $this->set($keys, array($key));
            } else {
                $arr = $this->get($keys);
                $this->set($keys, array_merge($arr, array($key)));
            }
        }

        return wp_cache_set($key, $value, $group, $expire);
    }

    /*
     * 获取缓存
     */
    public function get($key, $group = '')
    {
        return wp_cache_get($key, $group);
    }

    /*
     * 删除指定缓存
     */
    public function delete($key, $group = '')
    {
        return wp_cache_delete($key, $group);
    }

    /*
     * 获取组那所有元素
     */
    public function getGroupKeys($group)
    {
        $group_keys = $this->get($this->group_prefix . $group);
        if ($group_keys)
            return $group_keys;

        return false;
    }

    /*
     * 清空组
     */
    public function clearGroup($group)
    {
        $group_keys = $this->get($this->group_prefix . $group);
        if (!is_array($group_keys))
            return true;

        foreach ($group_keys as $key) {
            $this->delete($key, $group);
        }
        return true;
    }

    /*
     * 清空缓存
     */
    public function flush()
    {
        wp_cache_flush();
    }
}

?>