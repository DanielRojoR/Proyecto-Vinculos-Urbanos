<?php

require_once __DIR__ . '/../core/Model.php';

class Donations extends Model {
    protected $table = 'donations';
    
    public function saveDonation($data) {
        return $this->create([
            'amount' => $data['amount'],
            'donor_name' => $data['donor_name'],
            'email' => $data['email'],
            'payment_method' => $data['payment_method'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    // public function getTotalDonations() {
    //     $sql = "SELECT SUM(amount) as total FROM {$this->table}";
    //     return $this->query($sql)[0]['total'] ?? 0;
    // }

    public function getAllDonations() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->query($sql);
    }
}