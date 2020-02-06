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
use OAuth2ServerExamples\Traits\CanStoreInCache;

class DeviceCodeRepository implements DeviceCodeRepositoryInterface
{
    use CanStoreInCache;

    /**
     * @var string
     */
    public static $cacheNamespace = 'device_code';

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
        self::setCache(
            $deviceCodeEntity->getUserCode(),
            $deviceCodeEntity->getIdentifier()
        );

        self::storeInCache(
            $deviceCodeEntity->getIdentifier(),
            [
                'id' => $deviceCodeEntity->getIdentifier(),
                'user_id' => null,
                'user_code' => $deviceCodeEntity->getUserCode(),
                'client_id' => $deviceCodeEntity->getClient()->getIdentifier(),
                'revoked' => false,
                'scopes' => $deviceCodeEntity->getScopes(),
                'expires_at' => $deviceCodeEntity->getExpiryDateTime(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDeviceCodeEntityByDeviceCode($deviceCode, $grantType, ClientEntityInterface $clientEntity)
    {
        $record = self::getCache($deviceCode);

        $deviceCodeEntity = $this->getNewDeviceCode();
        $deviceCodeEntity->setIdentifier($record['id']);
        $deviceCodeEntity->setUserCode($record['user_code']);
        $deviceCodeEntity->setClient($clientEntity);

        foreach ($record['scopes'] as $scope) {
            $deviceCodeEntity->addScope($scope);
        }

        // The user identifier should be set when the user authenticates on the OAuth server
        $deviceCodeEntity->setUserIdentifier($record['user_id']);

        return $deviceCodeEntity;
    }

    /**
     * {@inheritdoc}
     */
    public function revokeDeviceCode($deviceCode)
    {
        $record = self::getCache($deviceCode);

        $record['revoked'] = true;

        self::storeInCache($deviceCode, $record);
    }

    /**
     * {@inheritdoc}
     */
    public function isDeviceCodeRevoked($deviceCode)
    {
        $record = self::getCache($deviceCode);

        return $record['revoked'] === true;
    }
}
