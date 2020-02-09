<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace League\OAuth2\Server\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\DeviceCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;

interface DeviceCodeRepositoryInterface extends RepositoryInterface
{
    /**
     * Creates a new DeviceCode
     *
     * @return DeviceCodeEntityInterface
     */
    public function getNewDeviceCode();

    /**
     * Persists a new auth code to permanent storage.
     *
     * @param DeviceCodeEntityInterface $deviceCodeEntity
     *
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewDeviceCode(DeviceCodeEntityInterface $deviceCodeEntity);

    /**
     * Get a device code entity.
     *
     * @param string                $deviceCodeId
     * @param string                $grantType
     * @param ClientEntityInterface $clientEntity
     *
     * @return DeviceCodeEntityInterface|null
     */
    public function getDeviceCodeByIdentifier($deviceCodeId, $grantType, ClientEntityInterface $clientEntity);

    /**
     * Revoke a device code.
     *
     * @param string $deviceCodeId
     */
    public function revokeDeviceCode($deviceCodeId);

    /**
     * Check if the device code has been revoked.
     *
     * @param string $deviceCodeId
     *
     * @return bool Return true if this code has been revoked
     */
    public function isDeviceCodeRevoked($deviceCodeId);
}
