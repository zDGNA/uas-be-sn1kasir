<?php

require_once '../models/Transaction.php';
require_once '../models/TransactionDetail.php';
require_once '../models/Product.php';
require_once '../controllers/AuthController.php';

class TransactionController {
    private $transaction;
    private $transactionDetail;
    private $product;
    private $auth;

    public function __construct() {
        $this->transaction = new Transaction();
        $this->transactionDetail = new TransactionDetail();
        $this->product = new Product();
        $this->auth = new AuthController();
        $this->auth->requireLogin();
    }

    public function index() {
        $stmt = $this->transaction->read();
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $transactions;
    }

    public function show($id) {
        $this->transaction->id = $id;
        if($this->transaction->readOne()) {
            $transaction_data = [
                'id' => $this->transaction->id,
                'transaction_code' => $this->transaction->transaction_code,
                'customer_id' => $this->transaction->customer_id,
                'user_id' => $this->transaction->user_id,
                'transaction_date' => $this->transaction->transaction_date,
                'subtotal' => $this->transaction->subtotal,
                'tax_amount' => $this->transaction->tax_amount,
                'discount_amount' => $this->transaction->discount_amount,
                'total_amount' => $this->transaction->total_amount,
                'payment_method' => $this->transaction->payment_method,
                'payment_amount' => $this->transaction->payment_amount,
                'change_amount' => $this->transaction->change_amount,
                'notes' => $this->transaction->notes,
                'status' => $this->transaction->status,
                'created_at' => $this->transaction->created_at,
                'updated_at' => $this->transaction->updated_at
            ];

            // Get transaction details
            $stmt = $this->transactionDetail->readByTransaction($id);
            $transaction_data['details'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $transaction_data;
        }
        return null;
    }

    public function store($data) {
        try {
            // Begin transaction
            $this->transaction->connection->beginTransaction();

            // Set transaction data
            $current_user = $this->auth->getCurrentUser();
            $this->transaction->customer_id = $data['customer_id'] ?? null;
            $this->transaction->user_id = $current_user['id'];
            $this->transaction->transaction_date = date('Y-m-d H:i:s');
            $this->transaction->subtotal = $data['subtotal'];
            $this->transaction->tax_amount = $data['tax_amount'] ?? 0;
            $this->transaction->discount_amount = $data['discount_amount'] ?? 0;
            $this->transaction->total_amount = $data['total_amount'];
            $this->transaction->payment_method = $data['payment_method'];
            $this->transaction->payment_amount = $data['payment_amount'];
            $this->transaction->change_amount = $data['change_amount'] ?? 0;
            $this->transaction->notes = $data['notes'] ?? '';
            $this->transaction->status = 'completed';

            // Create transaction
            if(!$this->transaction->create()) {
                throw new Exception('Failed to create transaction');
            }

            $transaction_id = $this->transaction->id;

            // Create transaction details and update stock
            foreach($data['items'] as $item) {
                $this->transactionDetail->transaction_id = $transaction_id;
                $this->transactionDetail->product_id = $item['product_id'];
                $this->transactionDetail->quantity = $item['quantity'];
                $this->transactionDetail->unit_price = $item['unit_price'];
                $this->transactionDetail->total_price = $item['total_price'];

                if(!$this->transactionDetail->create()) {
                    throw new Exception('Failed to create transaction detail');
                }

                // Update product stock
                $this->product->id = $item['product_id'];
                if(!$this->product->updateStock($item['quantity'])) {
                    throw new Exception('Failed to update product stock');
                }
            }

            // Commit transaction
            $this->transaction->connection->commit();

            return [
                'success' => true,
                'message' => 'Transaction created successfully',
                'transaction_id' => $transaction_id,
                'transaction_code' => $this->transaction->transaction_code
            ];

        } catch(Exception $e) {
            // Rollback transaction
            $this->transaction->connection->rollback();
            
            return [
                'success' => false,
                'message' => 'Transaction failed: ' . $e->getMessage()
            ];
        }
    }

    public function getDailySalesReport($date) {
        return $this->transaction->getDailySalesReport($date);
    }

    public function getByDateRange($start_date, $end_date) {
        $stmt = $this->transaction->getByDateRange($start_date, $end_date);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBestSellingProducts($limit = 10) {
        $stmt = $this->transactionDetail->getBestSellingProducts($limit);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>