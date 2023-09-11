<?php
    use App\Controllers\Api;

    $prefix = '/api/v1';

    $router->post($prefix . '/users/auth', [
        function($request, $response) {
            return Api\User::authenticate($request, $response);
        }
    ]);

    $router->put($prefix . '/users/auth', [
        'middlewares' => ['authorization-api'],
        function($request, $response) {
            return Api\User::refreshAuthorization($request, $response);
        }
    ]);

    $router->delete($prefix . '/users/auth', [
        'middlewares' => ['authorization-api'],
        function($request, $response) {
            return Api\User::destroyAuthorization($request, $response);
        }
    ]);