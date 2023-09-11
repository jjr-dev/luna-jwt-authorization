<?php
    use App\Controllers\Pages;

    $router->get('/', [
        function($request, $response) {
            return Pages\Home::homePage($request, $response);
        }
    ]);

    $router->get('/blocked', [
        'middlewares' => ['authorization-web'],
        function($request, $response) {
            return Pages\Home::blockedPage($request, $response);
        }
    ]);