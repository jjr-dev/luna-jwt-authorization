<?php
    namespace App\Middlewares;

    use App\Helpers\Session as SessionHelper;
    use App\Services\User as UserService;
    use Exception;

    class AuthorizationWeb {
        public function handle($req, $res, $next) {
            $session = SessionHelper::get();

            try {
                if(!isset($session['authorization']) || !$session['authorization'] || !isset($session['refresh']) || !$session['refresh']) 
                    throw new Exception();

                try {
                    $payload = UserService::verifyAuthorization($session['authorization']);
                } catch (Exception $e) {
                    if($e->getCode() !== 2) throw new Exception();

                    $payload = UserService::verifyAuthorization($session['refresh']);
                    SessionHelper::update('auth_token', UserService::refreshAuthorization($payload->user_id));
                }

                $req->user_id = $payload->user_id;

                return $next($req, $res);
            } catch(Exception $e) {
                return $req->getRouter()->redirect('/');
            }
        }
    }