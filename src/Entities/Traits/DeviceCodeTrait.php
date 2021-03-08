<?php

/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace League\OAuth2\Server\Entities\Traits;

use DateTimeImmutable;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

trait DeviceCodeTrait
{
    /**
     * @var string
     */
    private $userCode;

    /**
     * @var string
     */
    private $verificationUri;

    /**
     * @var int
     */
    private $retryInterval;

    /**
     * @var DateTimeImmutable
     */
    private $lastPolledDateTime;

    /**
     * @return string
     */
    public function getUserCode()
    {
        return $this->userCode;
    }

    /**
     * @param string $userCode
     *
     * @return string
     */
    public function setUserCode($userCode)
    {
        $this->userCode = $userCode;
    }

    /**
     * @return string
     */
    public function getVerificationUri()
    {
        return $this->verificationUri;
    }

    /**
     * @param string $verificationUri
     */
    public function setVerificationUri($verificationUri)
    {
        $this->verificationUri = $verificationUri;
    }

    /**
     * @return int
     */
    public function getRetryInterval()
    {
        return $this->retryInterval;
    }

    /**
     * @param int $retryInterval
     */
    public function setRetryInterval($retryInterval)
    {
        $this->retryInterval = $retryInterval;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getLastPolledDateTime()
    {
        return $this->lastPolledDateTime;
    }

    /**
     * @param DateTimeImmutable $lastPolledDateTime
     */
    public function setLastPolledDateTime($lastPolledDateTime)
    {
        $this->lastPolledDateTime = $lastPolledDateTime;
    }

    /**
     * @param DateTimeImmutable $nowDateTime
     * @return int Slow-down in seconds for the retry interval.
     */
    public function checkRetryFrequency(DateTimeImmutable $nowDateTime)
    {
        $retryInterval = $this->getRetryInterval();
        $lastPolledDateTime = $this->getLastPolledDateTime();

        if ($lastPolledDateTime) {

            // Seconds passed since last retry.
            $nowTimestamp = $nowDateTime->getTimestamp();
            $lastPollingTimestamp = $lastPolledDateTime->getTimestamp();

            if ($retryInterval > $nowTimestamp - $lastPollingTimestamp) {
                return $retryInterval; // polling to fast.
            }
        }

        return 0;
    }

    /**
     * @return ClientEntityInterface
     */
    abstract public function getClient();

    /**
     * @return DateTimeImmutable
     */
    abstract public function getExpiryDateTime();

    /**
     * @return ScopeEntityInterface[]
     */
    abstract public function getScopes();

    /**
     * @return string
     */
    abstract public function getIdentifier();
}
