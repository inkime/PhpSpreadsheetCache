<?php

/**
 *  +----------------------------------------------------------------
 *  Redis操作类
 *  +----------------------------------------------------------------
 * @author heykaka1020@163.com
 *  +----------------------------------------------------------------
 *  功能：验证 PhpSpreadsheet 配合 Redis 使用
 *  +----------------------------------------------------------------
 */

namespace Phpspreadsheetcache\driver;

use Psr\SimpleCache\CacheInterface;

class Redis implements CacheInterface
{

    protected $handler = null;

    protected $options = [
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => '',
        'select' => 0,
        'timeout' => 0,
        'expire' => 0,
        'persistent' => false,
        'prefix' => '',
    ];

    /**
     * 构造函数
     * @param array $options 缓存参数
     * @access public
     */
    public function __construct($options = [])
    {
        if (!extension_loaded('redis')) {
            throw new \BadFunctionCallException('not support: redis');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $this->handler = new \Redis;
        if ($this->options['persistent']) {
            $this->handler->pconnect($this->options['host'], $this->options['port'], $this->options['timeout'], 'persistent_id_' . $this->options['select']);
        } else {
            $this->handler->connect($this->options['host'], $this->options['port'], $this->options['timeout']);
        }

        if ('' != $this->options['password']) {
            $this->handler->auth($this->options['password']);
        }

        if (0 != $this->options['select']) {
            $this->handler->select($this->options['select']);
        }
    }

    public function get($name, $default = null)
    {
        $value = $this->handler->get($name);
        if (is_null($value) || false === $value) {
            return $default;
        }

        return unserialize($value);
    }

    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        if ($expire instanceof \DateTime) {
            $expire = $expire->getTimestamp() - time();
        }
        // 序列化对象
        $value = serialize($value);
        if ($expire) {
            $result = $this->handler->setex($name, $expire, $value);
        } else {
            $result = $this->handler->set($name, $value);
        }
        return $result;
    }

    public function has($name)
    {
        return $this->handler->exists($name);
    }

    public function clear()
    {
        return $this->handler->flushDB();
    }

    public function delete($key)
    {
    }

    public function deleteMultiple($keys)
    {
    }

    public function getMultiple($keys, $default = null)
    {
    }


    public function setMultiple($values, $ttl = null)
    {
    }
}
