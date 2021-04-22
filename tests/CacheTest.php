<?php


namespace Phpspreadsheetcache\tests;

use Phpspreadsheetcache\Cache;

class CacheTest extends \PHPUnit\Framework\TestCase
{
    public function testFile()
    {
        $cacheDir = dirname(__FILE__) . '/data';
        $config = [
            'type' => 'File',
            'path' => $cacheDir, // 文件缓存路径
        ];
        Cache::init($config);
        Cache::set('aaa', 111);
        $data = Cache::get('aaa');
        $this->assertEquals($data, '111');
    }

    public function testRedis()
    {
        $config = [
            'type' => 'Redis',
            'host' => '192.168.238.108',
            'port' => 6379,
            'password' => '123456',
            'select' => '1',
        ];
        Cache::init($config, true);
        Cache::set('bbb', 222);
        $data = Cache::get('bbb');
        $this->assertEquals($data, '222');
    }
}
