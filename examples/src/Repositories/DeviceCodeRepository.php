<?php
/**
 * @author    Andrew Millington <andrew@noexceptions.io>
 * @copyright Copyright (c) Alex Bilbie
 * @license   http://mit-license.org/
 *
 * @link      https://github.com/thephpleague/oauth2-server
 */

namespace OAuth2ServerExamples\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\DeviceCodeEntityInterface;
use League\OAuth2\Server\Repositories\DeviceCodeRepositoryInterface;
use OAuth2ServerExamples\Entities\DeviceCodeEntity;
use OAuth2ServerExamples\Cache\DeviceCodeCache;

class DeviceCodeRepository implements DeviceCodeRepositoryInterface
{

    protected $deviceCodeCache;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->deviceCodeCache = new DeviceCodeCache;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewDeviceCode()
    {
        return new DeviceCodeEntity();
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewDeviceCode(DeviceCodeEntityInterface $deviceCodeEntity)
    {
        // Some logic to persist a new device code to a database
        $this->deviceCodeCache->store($deviceCodeEntity);
    }

    /**
     * {@inheritdoc}
     */
    public function getDeviceCodeEntityByDeviceCode($deviceCode, $grantType, ClientEntityInterface $clientEntity)
    {
        $deviceCode = $this->deviceCodeCache->whereDeviceCode($deviceCode);

        return $deviceCode;
    }

    /**
     * {@inheritdoc}
     */
    public function revokeDeviceCode($codeId)
    {
        // Some logic to revoke device code
    }

    /**
     * {@inheritdoc}
     */
    public function isDeviceCodeRevoked($codeId)
    {
        // Some logic to check if a device code has been revoked
    }
}
