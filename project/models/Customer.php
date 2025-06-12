<?php

require_once '../config/Database.php';

class Customer {
    private $connection;
    private $table_name = "customers";

    public $id;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $city;
    public $postal_code;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->connect();
    }

    // Create customer
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, email=:email, phone=:phone, 
                      address=:address, city=:city, postal_code=:postal_code, status=:status";

        $stmt = $this->connection->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->city = htmlspecialchars(strip_tags($this->city));
        $this->postal_code = htmlspecialchars(strip_tags($this->postal_code));
        $this->status = htmlspecialchars(strip_tags($this->status));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":city", $this->city);
        $stmt->bindParam(":postal_code", $this->postal_code);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read all customers
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name ASC";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single customer
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->address = $row['address'];
            $this->city = $row['city'];
            $this->postal_code = $row['postal_code'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    // Update customer
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name=:name, email=:email, phone=:phone, 
                      address=:address, city=:city, postal_code=:postal_code, status=:status 
                  WHERE id=:id";

        $stmt = $this->connection->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->city = htmlspecialchars(strip_tags($this->city));
        $this->postal_code = htmlspecialchars(strip_tags($this->postal_code));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':postal_code', $this->postal_code);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete customer
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->connection->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Search customers
    public function search($keyword) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE (name LIKE :keyword OR phone LIKE :keyword OR email LIKE :keyword) 
                  AND status = 'active'
                  ORDER BY name ASC";
        
        $stmt = $this->connection->prepare($query);
        $keyword = htmlspecialchars(strip_tags($keyword));
        $keyword = "%{$keyword}%";
        $stmt->bindParam(':keyword', $keyword);
        $stmt->execute();
        return $stmt;
    }
}

?>