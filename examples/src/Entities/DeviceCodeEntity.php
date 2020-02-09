<?php
/**
 * @author Andrew Millington <andrew@noexceptions.io>
 * @copyright Copyright (c) Alex Bilbie
 * @license http://mit-license.org/
 *
 * @link https://github.com/thephpleague/oauth2-server
 */

namespace OAuth2ServerExamples\Entities;

use DateTimeImmutable;
use League\OAuth2\Server\Entities\DeviceCodeEntityInterface;
use League\OAuth2\Server\Entities\Traits\DeviceCodeTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;
use OAuth2ServerExamples\Repositories\DeviceCodeRepository;

class DeviceCodeEntity implements DeviceCodeEntityInterface
{
    use EntityTrait, TokenEntityTrait, DeviceCodeTrait {
        checkRetryFrequency as parentCheckRetryFrequency;
    }

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
    public function checkRetryFrequency(DateTimeImmutable $nowDateTime)
    {
        $slowDownSeconds = $this->parentCheckRetryFrequency($nowDateTime);

        if ($slowDownSeconds) {
            $slowDownSeconds = ceil($slowDownSeconds * 2.0);
            DeviceCodeRepository::setCache(
                'retry_interval',
                $slowDownSeconds,
                $this->getIdentifier()
            );
        }

        return $slowDownSeconds;
    }
}
