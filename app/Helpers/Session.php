<?php
    namespace App\Helpers;

    class Session {
        public static function start($session = []) {
            session_start();

            foreach($session as $key => $value) {
                $_SESSION[$key] = $value;
            }
        } 

        public static function get($key = false) {
            session_start();
            
            return (!$key) ? $_SESSION : $_SESSION[$key];
        }

        public static function destroy() {
            session_start();

            foreach($_SESSION as $key => $value) {
                unset($_SESSION[$key]);
            }

            session_destroy();
        }

        public static function update($key, $value) {
            session_start();

            $_SESSION[$key] = $value;
        }
    }