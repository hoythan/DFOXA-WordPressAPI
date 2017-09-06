<?php

namespace cached;

class cache
{
    public $cache;

    function __construct()
    {
        $cacheMode = get_option('dfoxa_cache_type');

        $cacheMode = '\cached\\' . $cacheMode;
        $this->cache = new $cacheMode();
    }

    function run()
    {
        throw new \Exception('gateway.close-api');
    }

    /*
     * 设置缓存
     */
    public function set($key, $value = '', $group = '', $expire = 0)
    {
        return $this->cache->set($key, $value, $group, $expire);
    }

    /*
     * 获取缓存
     * $key
     * $group
     * $default 默认值
     */
    public function get($key, $group = '', $default = false)
    {
        $res = $this->cache->get($key, $group);
        if ($res === false)
            return $default;

        return $res;
    }

    /*
     * 删除指定缓存
     */
    public function delete($key, $group = '')
    {
        return $this->cache->delete($key, $group);
    }

    /*
     * 获取组内所有元素
     */
    public function getGroupKeys($group)
    {
        return $this->cache->getGroupKeys($group);
    }

    /*
     * 清空缓存组
     */
    public function clearGroup($group)
    {
        return $this->cache->clearGroup($group);
    }

    /*
     * 清空缓存
     */
    public function flush()
    {
        return $this->cache->flush();
    }

    public static function test()
    {
        return 'test cached\cache';
    }
}

?>