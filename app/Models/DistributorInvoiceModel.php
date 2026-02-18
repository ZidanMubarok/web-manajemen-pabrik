<?php

namespace App\Models;

use CodeIgniter\Model;

class DistributorInvoiceModel extends Model
{
    protected $table      = 'distributor_invoices';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array'; // <--- Perubahan di sini! Mengembalikan data sebagai array
    protected $useSoftDeletes = false; // Sesuaikan jika Anda pakai soft delete

    protected $allowedFields = [
        'order_id',
        'invoice_date',
        'due_date',
        'total_amount',
        'status',
        'invoice_number',
        'payment_date',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at'; // Hanya dipakai jika useSoftDeletes = true

    protected $validationRules    = [
        'order_id'     => 'required|integer',
        'invoice_date' => 'required|valid_date',
        'due_date'     => 'required|valid_date',
        'total_amount' => 'required|numeric|greater_than[0]',
        'status'       => 'required|in_list[unpaid,paid,partially_paid,cancelled]',
        'invoice_number' => 'permit_empty|is_unique[distributor_invoices.invoice_number]|max_length[50]',
    ];
    protected $validationMessages = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Metode bantuan jika diperlukan di masa depan, tapi tidak wajib untuk integrasi awal
    // public function getUnpaidInvoices()
    // {
    //     return $this->where('status', 'unpaid')->findAll();
    // }
}
