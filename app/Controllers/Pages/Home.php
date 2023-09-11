<?php
    namespace App\Controllers\Pages;

    use Luna\Utils\View;
    use Luna\Utils\Controller;

    class Home extends Controller {
        static function homePage($req, $res) {
            $title = 'JWT Auth';
            $content = parent::page($title, View::render('home'));
            return $res->send(200, $content);
        }

        static function blockedPage($req, $res) {
            $title = 'JWT Auth';
            $content = parent::page($title, View::render('blocked'));
            return $res->send(200, $content);
        }
    }