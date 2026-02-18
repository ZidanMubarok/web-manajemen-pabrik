<?php

namespace App\Models;

use CodeIgniter\Model;

class ShipmentModel extends Model
{
    protected $table = 'shipments'; // Sesuaikan dengan nama tabel amdk_app_db_shipments
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false; // Jika tidak menggunakan soft delete
    protected $allowedFields = [
        'order_id',
        'shipping_date',
        'delivery_status', // Contoh: pending, on_transit, delivered, failed
        'tracking_number',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Anda bisa menambahkan validation rules jika diperlukan
    protected $validationRules    = [
        'order_id'          => 'required|integer',
        'shipping_date'     => 'required|valid_date',
        'delivery_status'   => 'required|in_list[pending,on_transit,delivered,failed]',
        'tracking_number'   => 'permit_empty|string|max_length[255]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function generateUniqueTrackingNumber(): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $trackingNumber = '';
        $isUnique = false;

        // Loop until a unique tracking number is generated
        while (!$isUnique) {
            $trackingNumber = '';
            for ($i = 0; $i < 5; $i++) {
                $trackingNumber .= $characters[rand(0, $charactersLength - 1)];
            }

            // Check if this tracking number already exists in the database
            $existingNota = $this->where('tracking_number', $trackingNumber)->first();

            if (empty($existingNota)) {
                $isUnique = true; // It's unique, so we can use it
            }
            // If not unique, the loop continues to generate a new one
        }

        return $trackingNumber;
    }
}
