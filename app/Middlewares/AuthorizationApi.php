<?php
    namespace App\Middlewares;

    use App\Services\User as UserService;
    use Luna\Utils\Controller;
    use Exception;

    class AuthorizationApi {
        public function handle($req, $res, $next) {
            try {
                $authorizationToken = $req->header('Authorization');

                $expToken = explode(' ', $authorizationToken);

                if(!$authorizationToken || count($expToken) != 2 || strtolower($expToken[0]) !== 'bearer')
                    throw new Exception("Token de autorização inválido", 403);

                $authorizationToken = $expToken[1];

                $payload = UserService::verifyAuthorization($authorizationToken);

                $req->user_id = $payload->user_id;

                return $next($req, $res);
            } catch(Exception $e) {
                return $res->send(401, Controller::error($e->getMessage(), 403), 'json');
            }
        }
    }