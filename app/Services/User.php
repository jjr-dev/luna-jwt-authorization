<?php
    namespace App\Services;

    use Luna\Utils\Service;
    use Luna\Utils\Environment;
    use App\Models\User as UserModel;
    use App\Helpers\Session as SessionHelper;
    use Firebase\JWT\Key;
    use Firebase\JWT\JWT;
    use Exception;

    class User extends Service {
        private static function verifyPassword($id, $password) {
            $user = UserModel::find($id)->makeVisible(['password']);

            if(!$user)
                throw new Exception("Usuário não encontrado", 404);

            return password_verify($password, $user->password);
        }

        public static function authenticate($email, $password) {
            try {
                $user = UserModel::where('email', $email)->first();

                if(!$user || !self::verifyPassword($user->id, $password))
                    throw new Exception("Email ou senha incorretos", 401);

                $authorizationPayload = [
                    "user_id" => $user->id,
                    "exp" => time() + 3600
                ];

                $refreshPayload = [
                    "user_id" => $user->id,
                    "exp" => time() + (7 * 24 * 3600)
                ];

                $authorizationToken = JWT::encode($authorizationPayload, Environment::get('JWT_SECRET_KEY'), 'HS256');
                $refreshToken = JWT::encode($refreshPayload, Environment::get('JWT_SECRET_KEY'), 'HS256');

                $tokens = [
                    'authorization' => $authorizationToken,
                    'refresh' => $refreshToken
                ];

                SessionHelper::start($tokens);

                return $tokens;
            } catch(Exception $e) {
                var_dump($e->getMessage());
                parent::exception($e);
            }
        }

        public static function refreshAuthorization($id) {
            try {
                if(!UserModel::find($id)->exists())
                    throw new Exception("Usuário não encontrado", 404);

                $authorizationPayload = [
                    "user_id" => $id,
                    "exp" => time() + 3600
                ];

                $authorizationToken = JWT::encode($authorizationPayload, Environment::get('JWT_SECRET_KEY'), 'HS256');

                return $authorizationToken;
            } catch(Exception $e) {
                parent::exception($e);
            }
        }

        public static function destroyAuthorization($id) {
            try {
                if(!UserModel::find($id)->exists())
                    throw new Exception("Usuário não encontrado", 404);

                SessionHelper::destroy();

                return;
            } catch(Exception $e) {
                parent::exception($e);
            }
        }

        public static function verifyAuthorization($token) {
            try {
                return JWT::decode($token, new Key(Environment::get('JWT_SECRET_KEY'), 'HS256'));
            } catch(\Firebase\JWT\SignatureInvalidException $e) {
                throw new Exception("Assinatura inválida", 1);
            } catch(\Firebase\JWT\ExpiredException $e) {
                throw new Exception("Token expirado", 2);
            }
        }
    }