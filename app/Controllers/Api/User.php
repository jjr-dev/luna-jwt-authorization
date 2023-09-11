<?php
    namespace App\Controllers\Api;

    use Luna\Utils\Controller;
    use App\Services\User as UserService;
    use Exception;

    class User extends Controller {
        public static function authenticate($req, $res) {
            try {
                $email = $req->body('email');
                $password = $req->body('password');

                if(!$email || !$password || empty($email) || empty($password))
                    throw new Exception("Informe o email e senha para acessar", 400);

                if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                    throw new Exception("Email inválido");

                $authorization = UserService::authenticate($email, $password);

                return $res->send(200, parent::success($authorization), 'json');
            } catch(Exception $e) {
                return $res->send($e->getCode(), parent::error($e->getMessage(), $e->getCode()), 'json');
            }
        }

        public static function refreshAuthorization($req, $res) {
            try {
                $authorization = UserService::refreshAuthorization($req->user_id);
                return $res->send(200, parent::success($authorization), 'json');
            } catch(Exception $e) {
                return $res->send($e->getCode(), parent::error($e->getMessage(), $e->getCode()), 'json');
            }
        }

        public static function destroyAuthorization($req, $res) {
            try {
                UserService::destroyAuthorization($req->user_id);
                return $res->send(200, parent::success(), 'json');
            } catch(Exception $e) {
                return $res->send($e->getCode(), parent::error($e->getMessage(), $e->getCode()), 'json');
            }
        }
    }