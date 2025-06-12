<?php

require_once '../config/Database.php';

class Transaction {
    private $connection;
    private $table_name = "transactions";

    public $id;
    public $transaction_code;
    public $customer_id;
    public $user_id;
    public $transaction_date;
    public $subtotal;
    public $tax_amount;
    public $discount_amount;
    public $total_amount;
    public $payment_method;
    public $payment_amount;
    public $change_amount;
    public $notes;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->connect();
    }

    // Generate transaction code
    private function generateTransactionCode() {
        $date = date('Ymd');
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE DATE(created_at) = CURDATE()";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $row['count'] + 1;
        return "TRX" . $date . str_pad($count, 4, "0", STR_PAD_LEFT);
    }

    // Create transaction
    public function create() {
        // Generate transaction code
        $this->transaction_code = $this->generateTransactionCode();
        
        $query = "INSERT INTO " . $this->table_name . " 
                  SET transaction_code=:transaction_code, customer_id=:customer_id, 
                      user_id=:user_id, transaction_date=:transaction_date, 
                      subtotal=:subtotal, tax_amount=:tax_amount, 
                      discount_amount=:discount_amount, total_amount=:total_amount, 
                      payment_method=:payment_method, payment_amount=:payment_amount, 
                      change_amount=:change_amount, notes=:notes, status=:status";

        $stmt = $this->connection->prepare($query);

        $this->customer_id = htmlspecialchars(strip_tags($this->customer_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->transaction_date = htmlspecialchars(strip_tags($this->transaction_date));
        $this->subtotal = htmlspecialchars(strip_tags($this->subtotal));
        $this->tax_amount = htmlspecialchars(strip_tags($this->tax_amount));
        $this->discount_amount = htmlspecialchars(strip_tags($this->discount_amount));
        $this->total_amount = htmlspecialchars(strip_tags($this->total_amount));
        $this->payment_method = htmlspecialchars(strip_tags($this->payment_method));
        $this->payment_amount = htmlspecialchars(strip_tags($this->payment_amount));
        $this->change_amount = htmlspecialchars(strip_tags($this->change_amount));
        $this->notes = htmlspecialchars(strip_tags($this->notes));
        $this->status = htmlspecialchars(strip_tags($this->status));

        $stmt->bindParam(":transaction_code", $this->transaction_code);
        $stmt->bindParam(":customer_id", $this->customer_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":transaction_date", $this->transaction_date);
        $stmt->bindParam(":subtotal", $this->subtotal);
        $stmt->bindParam(":tax_amount", $this->tax_amount);
        $stmt->bindParam(":discount_amount", $this->discount_amount);
        $stmt->bindParam(":total_amount", $this->total_amount);
        $stmt->bindParam(":payment_method", $this->payment_method);
        $stmt->bindParam(":payment_amount", $this->payment_amount);
        $stmt->bindParam(":change_amount", $this->change_amount);
        $stmt->bindParam(":notes", $this->notes);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute()) {
            $this->id = $this->connection->lastInsertId();
            return true;
        }
        return false;
    }

    // Read all transactions
    public function read() {
        $query = "SELECT t.*, c.name as customer_name, u.full_name as user_name 
                  FROM " . $this->table_name . " t
                  LEFT JOIN customers c ON t.customer_id = c.id
                  LEFT JOIN users u ON t.user_id = u.id
                  ORDER BY t.created_at DESC";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single transaction
    public function readOne() {
        $query = "SELECT t.*, c.name as customer_name, u.full_name as user_name 
                  FROM " . $this->table_name . " t
                  LEFT JOIN customers c ON t.customer_id = c.id
                  LEFT JOIN users u ON t.user_id = u.id
                  WHERE t.id = :id LIMIT 0,1";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->transaction_code = $row['transaction_code'];
            $this->customer_id = $row['customer_id'];
            $this->user_id = $row['user_id'];
            $this->transaction_date = $row['transaction_date'];
            $this->subtotal = $row['subtotal'];
            $this->tax_amount = $row['tax_amount'];
            $this->discount_amount = $row['discount_amount'];
            $this->total_amount = $row['total_amount'];
            $this->payment_method = $row['payment_method'];
            $this->payment_amount = $row['payment_amount'];
            $this->change_amount = $row['change_amount'];
            $this->notes = $row['notes'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    // Update transaction
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET customer_id=:customer_id, subtotal=:subtotal, 
                      tax_amount=:tax_amount, discount_amount=:discount_amount, 
                      total_amount=:total_amount, payment_method=:payment_method, 
                      payment_amount=:payment_amount, change_amount=:change_amount, 
                      notes=:notes, status=:status 
                  WHERE id=:id";

        $stmt = $this->connection->prepare($query);

        $this->customer_id = htmlspecialchars(strip_tags($this->customer_id));
        $this->subtotal = htmlspecialchars(strip_tags($this->subtotal));
        $this->tax_amount = htmlspecialchars(strip_tags($this->tax_amount));
        $this->discount_amount = htmlspecialchars(strip_tags($this->discount_amount));
        $this->total_amount = htmlspecialchars(strip_tags($this->total_amount));
        $this->payment_method = htmlspecialchars(strip_tags($this->payment_method));
        $this->payment_amount = htmlspecialchars(strip_tags($this->payment_amount));
        $this->change_amount = htmlspecialchars(strip_tags($this->change_amount));
        $this->notes = htmlspecialchars(strip_tags($this->notes));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->bindParam(':subtotal', $this->subtotal);
        $stmt->bindParam(':tax_amount', $this->tax_amount);
        $stmt->bindParam(':discount_amount', $this->discount_amount);
        $stmt->bindParam(':total_amount', $this->total_amount);
        $stmt->bindParam(':payment_method', $this->payment_method);
        $stmt->bindParam(':payment_amount', $this->payment_amount);
        $stmt->bindParam(':change_amount', $this->change_amount);
        $stmt->bindParam(':notes', $this->notes);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete transaction
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

    // Get transactions by date range
    public function getByDateRange($start_date, $end_date) {
        $query = "SELECT t.*, c.name as customer_name, u.full_name as user_name 
                  FROM " . $this->table_name . " t
                  LEFT JOIN customers c ON t.customer_id = c.id
                  LEFT JOIN users u ON t.user_id = u.id
                  WHERE DATE(t.transaction_date) BETWEEN :start_date AND :end_date
                  ORDER BY t.created_at DESC";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();
        return $stmt;
    }

    // Get daily sales report
    public function getDailySalesReport($date) {
        $query = "SELECT 
                    COUNT(*) as total_transactions,
                    SUM(total_amount) as total_sales,
                    SUM(subtotal) as total_subtotal,
                    SUM(tax_amount) as total_tax,
                    SUM(discount_amount) as total_discount
                  FROM " . $this->table_name . " 
                  WHERE DATE(transaction_date) = :date AND status = 'completed'";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>