<?php

    class authentification
    {
        private $pdo;

        public function __construct() 
        {
            $config = parse_ini_file("config.ini");
    
            try 
            {
                $this->pdo = new \PDO("mysql:host=".$config["host"].";dbname=".$config["database"].";charset=utf8", $config["user"], $config["password"]);
            } 
            catch(Exception $e) 
            {
                echo $e->getMessage();
            }
        }

        public function connexion($login, $mdp)
        {
            $sql="SELECT idPatient, mdpPatient FROM patient WHERE loginPatient = :leLogin";
        
            $req = $this->pdo->prepare($sql);
            $req->bindParam(":leLogin", $login, PDO::PARAM_STR);
            $req->execute();

            $ligne = $req->fetch();

            if(($ligne) != false)
            {
                if(password_verify($mdp, $ligne["mdpPatient"]))
                {
                    $token = uniqid();
                    $ip = $_SERVER['REMOTE_ADDR'];

                    $sql="INSERT INTO authentification VALUES (:leToken, :lIdAppareil, (SELECT idPatient FROM patient WHERE loginPatient = :leLogin))";

                    $req = $this->pdo->prepare($sql);
                    $req->bindParam(":leLogin", $login, PDO::PARAM_STR);
                    $req->bindParam(":lIdAppareil", $ip, PDO::PARAM_STR);
                    $req->bindParam(":leToken", $token, PDO::PARAM_STR);
        
                    $req->execute();

                    return $token;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
    }

?>