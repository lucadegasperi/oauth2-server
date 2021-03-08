<?php

/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace League\OAuth2\Server\Entities;

use DateTimeImmutable;

interface DeviceCodeEntityInterface extends TokenInterface
{
    /**
     * @return string
     */
    public function getUserCode();

    /**
     * @param string $userCode
     */
    public function setUserCode($userCode);

    /**
     * @return string
     */
    public function getVerificationUri();

    /**
     * @param string $verificationUri
     */
    public function setVerificationUri($verificationUri);

    /**
     * @return int
     */
    public function getRetryInterval();

    /**
     * @param int $retryInterval
     */
    public function setRetryInterval($retryInterval);

    /**
     * @return DateTimeImmutable
     */
    public function getLastPolledDateTime();

    /**
     * @param DateTimeImmutable $lastPolledDateTime
     */
    public function setLastPolledDateTime(DateTimeImmutable $lastPolledDateTime);

    /**
     * @param DateTimeImmutable $nowDateTime
     * @return int Slow-down in seconds for the retry interval.
     */
    public function checkRetryFrequency(DateTimeImmutable $nowDateTime);
}
