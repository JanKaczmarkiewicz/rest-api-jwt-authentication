<?php
    class Database {
        public $connection;

        private $dns = "sqlite:C:/xampp/htdocs/wia/api/db/wia_user_articles.db";
        private $user ='';
        private $password ='';
        private $options = array();
        
        function Connect() {

            $this->connection = null;
            
            try{
                $this->connection = new PDO(
                    $this->dns, 
                    $this->user, 
                    $this->password,
                    $this->options
                );
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch(PDOException $e) {
                echo 'Connection Error: ' . $e->getMessage();
                die();
            }

            return $this->connection;
        }
    }
