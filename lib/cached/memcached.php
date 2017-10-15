<?php

namespace cached;

class memcached
{
    public $cache;
    function __construct()
    {
        if(!class_exists('Memcached'))
            dfoxaError('cache.empyt-memcached');

        $this->cache = new \Memcached();
        $this->cache->addServer('127.0.0.1',11211);
    }

    public function set($key,$value = '',$expiration = 0)
    {
        $this->cache->set($key,$value,$expiration);
    }

    public function get($key,$group = '')
    {
        return $this->cache->get($key,$group);
    }

    public function remove($key)
    {
        return $this->cache->delete($key);
    }

    public function test()
    {
        return 'test cached/memcached';
    }
}

?>