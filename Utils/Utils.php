<?php

    namespace Utils;

    class Utils{

        public static function checkStudentSession(){
            if(!isset($_SESSION['student'])){
                header("Location:".FRONT_ROOT);
            }
        }

        public static function checkAdminSession(){
            if(!isset($_SESSION['admin'])){
                header("Location:".FRONT_ROOT);
            }
        }
        public static function checkSession(){
            if(!(isset($_SESSION['admin']) || isset($_SESSION['student']) || isset($_SESSION['company']))){
                $userNotLogged = true;
                require_once(VIEWS_PATH ."login.php");
            }
        }

        public static function logout(){
            session_destroy();
            header("Location: ".FRONT_ROOT);
        }

        public static function getIdUser(){
            if(isset($_SESSION['student'])){
                $idUser = $_SESSION['student']->getId();
            }
            else if(isset($_SESSION['company'])){
                $idUser = $_SESSION['company']->getId();
            }
            return $idUser;
        }

        public static function getUserEmail(){
            if(isset($_SESSION['student'])){
                $idUser = $_SESSION['student']->getEmail();
            }
            else if(isset($_SESSION['company'])){
                $idUser = $_SESSION['company']->getEmail();
            }
            return $idUser;
        }

        public static function completeSearch(String $haystack, String $needle)
        {
            return $needle != '' && strncmp($haystack, $needle, strlen($needle)) == 0;
        }
    }

    

?>