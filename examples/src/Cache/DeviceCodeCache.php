<?php
/**
 * @author Andrew Millington <andrew@noexceptions.io>
 * @copyright Copyright (c) Alex Bilbie
 * @license http://mit-license.org/
 *
 * @link https://github.com/thephpleague/oauth2-server
 */

namespace OAuth2ServerExamples\Cache;

use Illuminate\Cache\FileStore;
use Illuminate\Filesystem\Filesystem;
use League\OAuth2\Server\Entities\DeviceCodeEntityInterface;

class DeviceCodeCache
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->cache = new FileStore(
            new Filesystem,
            __DIR__ . '/../../cache/device_code'
        );
    }

    /**
     * @param DeviceCodeEntityInterface $deviceCodeEntity
     *
     * @return void
     */
    public function store($deviceCodeEntity)
    {
        $cache = [];

        $deviceCodeSerialized = serialize($deviceCodeEntity);
        $cache[$deviceCodeEntity->getUserCode()] = $deviceCodeSerialized;
        $cache[$deviceCodeEntity->getIdentifier()] = $deviceCodeSerialized;

        $this->cache->putMany($cache, 60);
    }

    /**
     * @param string $userCode
     *
     * @return League\OAuth2\Server\Entities\DeviceCodeEntityInterface
     */
    public function whereUserCode($userCode)
    {
        return unserialize($this->cache->get($userCode));
    }

    /**
     * @param string $deviceCode
     *
     * @return League\OAuth2\Server\Entities\DeviceCodeEntityInterface
     */
    public function whereDeviceCode($deviceCode)
    {
        return unserialize($this->cache->get($deviceCode));
    }
}
