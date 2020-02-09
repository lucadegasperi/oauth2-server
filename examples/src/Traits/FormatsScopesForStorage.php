<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace OAuth2ServerExamples\Traits;

trait FormatsScopesForStorage
{
    /**
     * Get an array of scope identifiers for storage.
     *
     * @param  array  $scopes
     * @return array
     */
    public function scopesToArray(array $scopes)
    {
        return array_map(function ($scope) {
            return $scope->getIdentifier();
        }, $scopes);
    }
}
