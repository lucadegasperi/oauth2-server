<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace OAuth2ServerExamples\Traits;

trait CanStoreInCache
{
    /**
     * @var string
     */
    public static $path2cache = __DIR__ . '/../../cache/';

    /**
     * @param string $key
     * @param mix    $value
     * @param string $storeName
     */
    public static function setCache($key, $value, string $storeName = 'defaultStore')
    {
        $record = (array) self::getCache($storeName);

        $record[$key] = $value;

        self::storeInCache($storeName, $record);
    }

    /**
     * @param string      $storeName
     * @param string|null $key
     *
     * @return mix
     */
    public static function getCache(string $storeName, $key = null)
    {
        $store = self::loadFromCache($storeName) ?: [];

        if(is_null($key)) {
            return $store;
        }

        if(isset($store[$key])) {
            return $store[$key];
        }

        return null;
    }

    /**
     * @param string|null  $name
     * @param array|object $record
     */
    public static function storeInCache($storeName, $record)
    {
        if(is_object($record)){
            $record = json_decode(json_encode($record), True);
        }

        $path = self::$path2cache . '/' . self::$cacheNamespace;

        if (! is_dir($path)) {
            mkdir($path);
        }

        file_put_contents(
            $path . '/' . $storeName,
            serialize($record)
        );
    }

    /**
     * @param string      $storeName
     *
     * @return array|null
     */
    public static function loadFromCache(string $storeName)
    {
        $fn = self::$path2cache . '/' . self::$cacheNamespace . '/' . $storeName;

        if(file_exists($fn)) {
            return unserialize(file_get_contents($fn));
        }

        return null;
    }
}
