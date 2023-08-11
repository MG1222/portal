<?php

	namespace Library\Auth;


	class Authenticate
    {
        public function __construct()
        {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
        }


        public function isAuthenticated(): bool
        {
            return isset($_SESSION['user_id']);
        }


        public function isAdmin(): bool
        {
            return isset($_SESSION['admin']);

        }

        public function login(int $id): void
        {
            $_SESSION['user_id'] = $id;
        }

        public function logout(): void
        {
            unset($_SESSION['user_id']);
            session_destroy();
        }


        public function getUserId()
        {
            return $_SESSION['user_id'];
        }

        public function getFullName()
        {
            if (isset($_SESSION['userFullName'])) {
                return $_SESSION['userFullName'];
            }

        }

        public function getEmail()
        {
            if (isset($_SESSION['user_email'])) {
                return $_SESSION['user_email'];
            }
        }

        /**
         * @return mixed|void
         */
        public function canEdit()
        {
            if (isset($_SESSION['type_project'])) {
                return $_SESSION['type_project'];
            }
        }

        public function superAdmin(): bool
        {
            return isset($_SESSION['superAdmin']);
        }

    }
