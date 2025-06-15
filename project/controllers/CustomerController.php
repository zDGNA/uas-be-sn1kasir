<?php

require_once '../models/Customer.php';
require_once '../controllers/AuthController.php';

class CustomerController {
    private $customer;
    private $auth;

    public function __construct() {
        $this->customer = new Customer();
        $this->auth = new AuthController();
        $this->auth->requireLogin();
    }

    public function index() {
        $stmt = $this->customer->read();
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $customers;
    }

    public function show($id) {
        $this->customer->id = $id;
        if($this->customer->readOne()) {
            return [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'email' => $this->customer->email,
                'phone' => $this->customer->phone,
                'address' => $this->customer->address,
                'city' => $this->customer->city,
                'postal_code' => $this->customer->postal_code,
                'status' => $this->customer->status,
                'created_at' => $this->customer->created_at,
                'updated_at' => $this->customer->updated_at
            ];
        }
        return null;
    }

    public function store($data) {
        $this->customer->name = $data['name'];
        $this->customer->email = $data['email'] ?? '';
        $this->customer->phone = $data['phone'] ?? '';
        $this->customer->address = $data['address'] ?? '';
        $this->customer->city = $data['city'] ?? '';
        $this->customer->postal_code = $data['postal_code'] ?? '';
        $this->customer->status = $data['status'] ?? 'active';

        if($this->customer->create()) {
            return [
                'success' => true,
                'message' => 'Customer created successfully'
            ];
        }
        return [
            'success' => false,
            'message' => 'Failed to create customer'
        ];
    }

    public function update($id, $data) {
        $this->customer->id = $id;
        $this->customer->name = $data['name'];
        $this->customer->email = $data['email'] ?? '';
        $this->customer->phone = $data['phone'] ?? '';
        $this->customer->address = $data['address'] ?? '';
        $this->customer->city = $data['city'] ?? '';
        $this->customer->postal_code = $data['postal_code'] ?? '';
        $this->customer->status = $data['status'] ?? 'active';

        if($this->customer->update()) {
            return [
                'success' => true,
                'message' => 'Customer updated successfully'
            ];
        }
        return [
            'success' => false,
            'message' => 'Failed to update customer'
        ];
    }

    public function destroy($id) {
        $this->customer->id = $id;
        if($this->customer->delete()) {
            return [
                'success' => true,
                'message' => 'Customer deleted successfully'
            ];
        }
        return [
            'success' => false,
            'message' => 'Failed to delete customer'
        ];
    }

    public function search($keyword) {
        $stmt = $this->customer->search($keyword);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $customers;
    }
}

?>