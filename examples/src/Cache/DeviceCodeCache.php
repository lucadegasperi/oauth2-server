<?php
/**
 * @author Andrew Millington <andrew@noexceptions.io>
 * @copyright Copyright (c) Alex Bilbie
 * @license http://mit-license.org/
 *
 * @link https://github.com/thephpleague/oauth2-server
 */

namespace OAuth2ServerExamples\Cache;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Cache\Namespaced\NamespacedCachePool;
use Cache\Adapter\Filesystem\FilesystemCachePool;
use League\OAuth2\Server\Entities\DeviceCodeEntityInterface;

class DeviceCodeCache
{
    protected $prefix = 'deviceCode.';

    /**
     * @return void
     */
    public function __construct()
    {
        $this->filesystemAdapter = new Local(__DIR__.'/../../');
        $this->filesystem  = new Filesystem($this->filesystemAdapter);
        $this->pool = new FilesystemCachePool($this->filesystem);
    }

    /**
     * @param DeviceCodeEntityInterface $deviceCodeEntity
     *
     * @return void
     */
    public function store($deviceCodeEntity)
    {
        $mapCache = $this->map();
        $mapArray = $mapCache->get();
        $mapArray[$deviceCodeEntity->getUserCode()] = $deviceCodeEntity->getIdentifier();
        $mapCache->set($mapArray);

        $this->pool->save($mapCache);

        $deviceCodeCache = $this->pool->getItem($this->prefix . $deviceCodeEntity->getIdentifier());
        $deviceCodeCache->set(serialize($deviceCodeEntity));

        $this->pool->save($deviceCodeCache);
    }

    /**
     * @param string $userCode
     *
     * @return League\OAuth2\Server\Entities\DeviceCodeEntityInterface
     */
    public function whereUserCode($userCode)
    {
        $map = $this->map()->get();

        return $this->whereDeviceCode($map[$userCode]);
    }

    /**
     * @param string $deviceCode
     *
     * @return League\OAuth2\Server\Entities\DeviceCodeEntityInterface
     */
    public function whereDeviceCode($deviceCode)
    {
        $deviceCodeCache = $this->pool->getItem($this->prefix . $deviceCode);

        return unserialize($deviceCodeCache->get());
    }

    /**
     * @return array
     */
    protected function map() {
        return $this->pool->getItem($this->prefix . 'map', []);
    }
}
