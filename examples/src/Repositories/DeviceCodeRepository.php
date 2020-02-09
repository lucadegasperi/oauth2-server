<?php
/**
 * @author    Andrew Millington <andrew@noexceptions.io>
 * @copyright Copyright (c) Alex Bilbie
 * @license   http://mit-license.org/
 *
 * @link      https://github.com/thephpleague/oauth2-server
 */

namespace OAuth2ServerExamples\Repositories;

use DateTimeImmutable;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\DeviceCodeEntityInterface;
use League\OAuth2\Server\Repositories\DeviceCodeRepositoryInterface;
use OAuth2ServerExamples\Entities\ScopeEntity as Scope;
use OAuth2ServerExamples\Entities\DeviceCodeEntity;
use OAuth2ServerExamples\Traits\CanStoreInCache;
use OAuth2ServerExamples\Traits\FormatsScopesForStorage;

class DeviceCodeRepository implements DeviceCodeRepositoryInterface
{
    use CanStoreInCache, FormatsScopesForStorage;

    /**
     * @var string
     */
    public static $cacheNamespace = 'device_code';

    /**
     * {@inheritdoc}
     */
    public function getNewDeviceCode()
    {
        return new DeviceCodeEntity;
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
                'user_code' => $deviceCodeEntity->getUserCode(),
                'user_id' => null,
                'client_id' => $deviceCodeEntity->getClient()->getIdentifier(),
                'scopes' => $this->scopesToArray($deviceCodeEntity->getScopes()),
                'revoked' => false,
                'retry_interval' => $deviceCodeEntity->getRetryInterval(),
                'last_polled_at' => $deviceCodeEntity->getLastPolledDateTime(),
                'expires_at' => $deviceCodeEntity->getExpiryDateTime(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDeviceCodeByIdentifier($deviceCodeId, $grantType, ClientEntityInterface $clientEntity)
    {
        $deviceCode = (object) self::getCache($deviceCodeId);

        $deviceCodeEntity = $this->getNewDeviceCode();
        $deviceCodeEntity->setIdentifier($deviceCode->id);
        $deviceCodeEntity->setUserCode($deviceCode->user_code);
        $deviceCodeEntity->setUserIdentifier($deviceCode->user_id);
        $deviceCodeEntity->setRetryInterval($deviceCode->retry_interval);
        $deviceCodeEntity->setLastPolledDateTime($deviceCode->last_polled_at);

        foreach ($deviceCode->scopes as $scope) {
            $deviceCodeEntity->addScope(new Scope($scope));
        }

        $deviceCodeEntity->setClient($clientEntity);

        self::setCache('last_polled_at', new DateTimeImmutable, $deviceCode->id);

        return $deviceCodeEntity;
    }

    /**
     * {@inheritdoc}
     */
    public function revokeDeviceCode($deviceCodeId)
    {
        $deviceCode = (object) self::getCache($deviceCodeId);

        self::setCache('revoked', true, $deviceCode->id);
    }

    /**
     * {@inheritdoc}
     */
    public function isDeviceCodeRevoked($deviceCodeId)
    {
        $deviceCode = self::getCache($deviceCodeId);

        return $deviceCode['revoked'] === true;
    }
}
