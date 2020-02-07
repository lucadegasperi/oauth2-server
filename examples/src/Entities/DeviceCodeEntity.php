<?php
/**
 * @author Andrew Millington <andrew@noexceptions.io>
 * @copyright Copyright (c) Alex Bilbie
 * @license http://mit-license.org/
 *
 * @link https://github.com/thephpleague/oauth2-server
 */

namespace OAuth2ServerExamples\Entities;

use League\OAuth2\Server\Entities\DeviceCodeEntityInterface;
use League\OAuth2\Server\Entities\Traits\DeviceCodeTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;
use OAuth2ServerExamples\Repositories\DeviceCodeRepository;

class DeviceCodeEntity implements DeviceCodeEntityInterface
{
    use EntityTrait, DeviceCodeTrait, TokenEntityTrait;

    /**
     * {@inheritdoc}
     */
    public function setUserCode($userCode)
    {
        $this->userCode = substr_replace($userCode, '-', 4, 0);
    }

    /**
     * {@inheritdoc}
     */
    public function setLastPolledTime($lastPolledTime)
    {
        $this->lastPolledTime = $lastPolledTime;

        if($deviceCode = $this->getIdentifier()) {
            DeviceCodeRepository::setCache(
                'last_polled_at',
                $lastPolledTime,
                $deviceCode
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setPollingInterval($pollingInterval)
    {
        $this->pollingInterval = $pollingInterval;

        if($deviceCode = $this->getIdentifier()) {
            DeviceCodeRepository::setCache(
                'polling_interval',
                $pollingInterval,
                $deviceCode
            );
        }
    }

}
