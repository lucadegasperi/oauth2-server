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
     * @var DateTimeImmutable
     */
    private $lastPolledTime;

    /**
     * @var int
     */
    private $pollingInterval;

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
     * @return DateTimeImmutable
     */
    public function getLastPolledTime()
    {
        return $this->lastPolledTime;
    }

    /**
     * @param DateTimeImmutable $lastPolledTime
     */
    public function setLastPolledTime($lastPolledTime)
    {
        $this->lastPolledTime = $lastPolledTime;
    }

    /**
     * @return int
     */
    public function getPollingInterval()
    {
        return $this->pollingInterval;
    }

    /**
     * @param int $seconds
     */
    public function setPollingInterval($seconds)
    {
        $this->pollingInterval = $seconds;
    }

    /**
     * @param DateTimeImmutable $polledTime
     * @param int               $slowDownSeconds
     *
     * @return int
     */
    public function checkPollingRateInterval($polledTime, $slowDownSeconds = 0)
    {
        if($this->lastPolledTime === null) {
            $this->setLastPolledTime($polledTime);

            return $slowDownSeconds;
        }

        // Seconds passed since last retry.
        $lapsedTime = $this->lastPolledTime->getTimestamp() - $polledTime->getTimestamp();

        if($this->pollingInterval > $lapsedTime) {
            // Slow down logic this can be moved to
            // Examples and here only return default rate 5.
            $slowDownSeconds = ceil($this->pollingInterval * 1.1);
        }

        $this->setLastPolledTime($polledTime);
        $this->setPollingInterval($slowDownSeconds);

        return $slowDownSeconds;
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
