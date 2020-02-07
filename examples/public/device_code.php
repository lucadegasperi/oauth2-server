<?php
/**
 * @author    Andrew Millington <andrew@noexceptions.io>
 * @copyright Copyright (c) Alex Bilbie
 * @license   http://mit-license.org/
 *
 * @link      https://github.com/thephpleague/oauth2-server
 */

error_reporting(E_ALL & ~E_USER_NOTICE);

include __DIR__ . '/../vendor/autoload.php';

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\DeviceCodeGrant;
use OAuth2ServerExamples\Repositories\AccessTokenRepository;
use OAuth2ServerExamples\Repositories\ClientRepository;
use OAuth2ServerExamples\Repositories\DeviceCodeRepository;
use OAuth2ServerExamples\Repositories\RefreshTokenRepository;
use OAuth2ServerExamples\Repositories\ScopeRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Zend\Diactoros\Stream;

$app = new App([
    'settings' => [
        'displayErrorDetails' => true,
    ],
    AuthorizationServer::class => function () {
        // Init our repositories
        $clientRepository = new ClientRepository();
        $scopeRepository = new ScopeRepository();
        $accessTokenRepository = new AccessTokenRepository();
        $refreshTokenRepository = new RefreshTokenRepository();
        $deviceCodeRepository = new DeviceCodeRepository();

        $privateKeyPath = 'file://' . __DIR__ . '/../private.key';

        // Set up the authorization server
        $server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKeyPath,
            'lxZFUEsBCJ2Yb14IF2ygAHI5N4+ZAUXXaSeeJm6+twsUmIen'
        );

        // Set up the device code grant
        $deviceCodeGrant =  new DeviceCodeGrant(
            $deviceCodeRepository,
            $refreshTokenRepository,
            new \DateInterval('PT10M'),
            5
        );

        // Set the device code grant verification uri
        $deviceCodeGrant->setVerificationUri(
            (string) Slim\Http\Uri::createFromEnvironment(
                Slim\Http\Environment::mock($_SERVER)
            )->withPath('device_activate')
        );

        // Enable the device code grant on the server with a token TTL of 1 hour
        $server->enableGrantType(
            $deviceCodeGrant,
            new \DateInterval('PT1H')
        );

        return $server;
    },
]);

$app->post('/device_authorization', function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {
    /* @var \League\OAuth2\Server\AuthorizationServer $server */
    $server = $app->getContainer()->get(AuthorizationServer::class);

    try {
        $deviceAuthRequest = $server->validateDeviceAuthorizationRequest($request);

        // Once the user has logged in, set the user on the authorization request
        //$deviceAuthRequest->setUser();

        // Once the user has approved or denied the client, update the status
        //$deviceAuthRequest->setAuthorizationApproved(true);

        // Return the HTTP redirect response
        return $server->completeDeviceAuthorizationRequest($deviceAuthRequest, $response);
    } catch (OAuthServerException $exception) {
        return $exception->generateHttpResponse($response);
    } catch (\Exception $exception) {
        $body = new Stream('php://temp', 'r+');
        $body->write($exception->getMessage());

        return $response->withStatus(500)->withBody($body);
    }
});

$app->post('/access_token', function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {
    /* @var \League\OAuth2\Server\AuthorizationServer $server */
    $server = $app->getContainer()->get(AuthorizationServer::class);

    try {
        return $server->respondToAccessTokenRequest($request, $response);
    } catch (OAuthServerException $exception) {
        return $exception->generateHttpResponse($response);
    } catch (\Exception $exception) {
        $body = new Stream('php://temp', 'r+');
        $body->write($exception->getMessage());

        return $response->withStatus(500)->withBody($body);
    }
});

$app->get('/device_activate', function (
    ServerRequestInterface $request, ResponseInterface $response) use ($app) {
        return $response->write(<<<EOD
            <h1>PHP OAuth 2.0 Server Device Flow Example</h1>
            <hr>
            <form method="POST">
                <label>Enter Code:</label>
                <input type="text" name="user_code"/>
                <button>activate</button>
            </form>
        EOD);
    }
);

$app->post('/device_activate', function (
    ServerRequestInterface $request, ResponseInterface $response) use ($app) {
        $userCode = $request->getParsedBody()['user_code'];

        if($deviceCode = DeviceCodeRepository::getCache('defaultStore', $userCode)) {
            // same as $deviceCode->setUserIdentifier($currentUserId = 1);
            DeviceCodeRepository::setCache('user_id', 1, $deviceCode);

            return $response->write(<<<EOD
                <h1>PHP OAuth 2.0 Server Device Flow Example</h1>
                <hr>
                <p style="color:green;">
                    Device with code: $userCode is activated.
                </p>
                <button onclick="window.history.back()">Go Back</button>
            EOD);
        }

        return $response->write(<<<EOD
            <h1>PHP OAuth 2.0 Server Device Flow Example</h1>
            <hr>
            <p style="color:red;">
                Oops, cannot activate device with code: $userCode.
            </p>
            <button onclick="window.history.back()">Go Back</button>
        EOD);
    }
);

$app->run();
