<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $first_name;
    public $last_name;
    public $user_name;
    public $password;

    function __construct($db){
        $this->conn = $db;
    }

    function createUser() {
        $stmt = $this->conn->prepare("INSERT into users
            (first_name, last_name, user_name, password) 
                values 
                    (:firstname, :lastname, :username, :password)"
        );

        $this->first_name=htmlspecialchars(strip_tags($this->first_name));
        $this->last_name=htmlspecialchars(strip_tags($this->last_name));
        $this->user_name=htmlspecialchars(strip_tags($this->user_name));
        $this->password=htmlspecialchars(strip_tags($this->password));

        $stmt->bindParam(":firstname", $this->first_name);
        $stmt->bindParam(":lastname", $this->last_name);
        $stmt->bindParam(":username", $this->user_name);

        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(":password", $password_hash);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    function testUserData($rules){
        if($this->password < $rules['password']){
            return false;
        } elseif($this->user_name < $rules['username']){
            return false;
        } elseif($this->first_name < $rules['first_name']){
            return false;
        } elseif($this->last_name < $rules['last_name']){
            return false;
        }
        return true;
    }

    public function update(){

        $password_set=!empty($this->password) ? ", password = :password" : "";
     
        $query = "UPDATE " . $this->table_name . "
                SET
                    firstname = :firstname,
                    lastname = :lastname,
                    email = :email
                    {$password_set}
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->firstname=htmlspecialchars(strip_tags($this->firstname));
        $this->lastname=htmlspecialchars(strip_tags($this->lastname));
        $this->email=htmlspecialchars(strip_tags($this->email));
     
        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':lastname', $this->lastname);
        $stmt->bindParam(':email', $this->email);

        if(!empty($this->password)){
            $this->password=htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        }
     
        $stmt->bindParam(':id', $this->id);
     
        if($stmt->execute()){
            return true;
        }
     
        return false;
    }

    function isUserName() {
        
        $query = "SELECT id, first_name, last_name, password FROM " . $this->table_name . " WHERE user_name = :username LIMIT 0,1";

        $stmt = $this->conn->prepare($query);

        $this->user_name=htmlspecialchars(strip_tags($this->user_name));

        $stmt->bindParam(":username", $this->user_name);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if($result) {

            $this->id = $result['id'];
            $this->firstname = $result['first_name'];
            $this->lastname = $result['last_name'];
            $this->password = $result['password'];
            
            return true;
        }
        
        return false;
        
    }
}
?>