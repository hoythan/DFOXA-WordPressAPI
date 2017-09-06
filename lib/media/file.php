<?php

namespace media;

abstract class file
{
    public function run()
    {
        throw new \Exception('gateway.close-api');
    }
}