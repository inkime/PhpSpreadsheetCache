<?php
/**
 *  +----------------------------------------------------------------
 *  缓存操作类
 *  +----------------------------------------------------------------
 * @author heykaka1020@163.com
 *  +----------------------------------------------------------------
 *  功能：支持切换不同缓存引擎
 *  +----------------------------------------------------------------
 */

namespace Phpspreadsheetcache;


class Cache
{
    /**
     * @var array 缓存的实例
     */
    public static $instance = [];

    /**
     * @var object 操作句柄
     */
    public static $handler;

    /**
     * 连接缓存驱动
     * @access public
     * @param array $options 配置数组
     * @param bool|string $name 缓存连接标识 true 强制重新连接
     * @return Driver
     */
    public static function connect(array $options = [], $name = false)
    {
        $type = !empty($options['type']) ? $options['type'] : 'File';

        if (false === $name) {
            $name = md5(serialize($options));
        }

        if (true === $name || !isset(self::$instance[$name])) {
            $class = false === strpos($type, '\\') ? '\\Phpspreadsheetcache\\driver\\' . ucwords($type) : $type;

            if (true === $name) {
                return new $class($options);
            }

            self::$instance[$name] = new $class($options);
        }

        return self::$instance[$name];
    }

    /**
     * 自动初始化缓存
     * @access public
     * @param array $options 配置数组
     * @return Driver
     */
    public static function init(array $options = [])
    {
        if (is_null(self::$handler)) {
            self::$handler = self::connect($options);
        }

        return self::$handler;
    }

    /**
     * 判断缓存是否存在
     * @access public
     * @param string $name 缓存变量名
     * @return bool
     */
    public static function has($name)
    {
        return self::init()->has($name);
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存标识
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function get($name, $default = false)
    {
        return self::init()->get($name, $default);
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存标识
     * @param mixed $value 存储数据
     * @param int|null $expire 有效时间 0为永久
     * @return boolean
     */
    public static function set($name, $value, $expire = null)
    {
        return self::init()->set($name, $value, $expire);
    }

    /**
     * 清除缓存
     * @access public
     * @param string $tag 标签名
     * @return boolean
     */
    public static function clear($tag = null)
    {
        return self::init()->clear($tag);
    }

}
