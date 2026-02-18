<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\OrderModel;
use App\Models\ShipmentModel;
use CodeIgniter\I18n\Time; // Import Class Time dari CodeIgniter

class CourierController extends Controller
{
    protected $orderModel;
    protected $shipmentModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->shipmentModel = new ShipmentModel();
    }

    public function showUpdateForm()
    {
        return view('courier/update_status_form');
    }
    public function showInfoPage()
    {
        return view('courier/info_page');
    }

    public function updateDeliveryStatus()
    {

        $rules = [
            'tracking_number' => [
                'label'  => 'Nomor Resi',
                'rules'  => 'required|regex_match[/^[0-9A-Z]{5}$/]|is_not_unique[shipments.tracking_number]',
                'errors' => [
                    'required'      => '{field} harus diisi.',
                    'regex_match'   => '{field} harus berformat 5 karakter heksadesimal (0-9, A-F). Contoh: 68AB4.',
                    'is_not_unique' => '{field} tidak ditemukan dalam sistem pengiriman.'
                ]
            ],
            'delivery_status' => [
                'label'  => 'Status Pengiriman',
                'rules'  => 'required|in_list[on_transit,delivered,failed]',
                'errors' => [
                    'required' => '{field} harus dipilih.',
                    'in_list'  => '{field} yang Anda pilih tidak valid untuk saat ini.'
                ]
            ]
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $trackingNumber = $this->request->getPost('tracking_number');
        $newDeliveryStatus = $this->request->getPost('delivery_status');

        $shipment = $this->shipmentModel->where('tracking_number', $trackingNumber)->first();

        if (!$shipment) {
            return redirect()->back()->withInput()->with('error', 'Nomor resi tidak ditemukan. Mohon periksa kembali.');
        }

        $currentStatus = $shipment['delivery_status'];
        $orderId = $shipment['order_id'];

        // Logika transisi status
        switch ($currentStatus) {
            case 'pending':
                if ($newDeliveryStatus !== 'on_transit') {
                    return redirect()->back()->withInput()->with('error', 'Status "Pending" hanya bisa diubah menjadi "On Transit".');
                }
                break;
            case 'on_transit':
                if ($newDeliveryStatus !== 'delivered' && $newDeliveryStatus !== 'failed') {
                    return redirect()->back()->withInput()->with('error', 'Status "On Transit" hanya bisa diubah menjadi "Terkirim" atau "Ditolak".');
                }
                break;
            case 'delivered':
                // Izinkan perubahan dari delivered ke failed
                if ($newDeliveryStatus !== 'failed') {
                    return redirect()->back()->withInput()->with('error', 'Status pengiriman sudah berstatus "Terkirim".');
                }
                break;
            case 'failed':
                // Izinkan perubahan dari failed ke delivered
                if ($newDeliveryStatus !== 'delivered') {
                    return redirect()->back()->withInput()->with('error', 'Status pengiriman sudah berstatus "Gagal".');
                }
                break;
            default:
                // Tangani status yang tidak terduga atau invalid
                return redirect()->back()->withInput()->with('error', 'Status pengiriman saat ini tidak dikenal atau tidak valid.');
        }

        // Update Status Pengiriman di tabel `shipments`
        $dataShipment = [
            'delivery_status' => $newDeliveryStatus,
            'updated_at'      => date('Y-m-d H:i:s') // Menggunakan date() untuk updated_at shipment
        ];

        $updateShipment = $this->shipmentModel->update($shipment['id'], $dataShipment);

        if (!$updateShipment) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui status pengiriman. Silakan coba lagi.');
        }

        // Update Status Order di tabel `orders` berdasarkan status pengiriman baru
        $newOrderStatus = '';
        $deliveryDate = null; // Inisialisasi delivery_date menjadi null

        if ($newDeliveryStatus === 'delivered') {
            $newOrderStatus = 'completed';
            // Set delivery_date jika statusnya completed
            // Cara 1: Menggunakan date()
            $deliveryDate = date('Y-m-d H:i:s');

            // Cara 2: Menggunakan CodeIgniter's Time class (lebih direkomendasikan untuk konsistensi timezone)
            // $now = Time::now(); // Menggunakan timezone yang diatur di app/Config/App.php
            // $deliveryDate = $now->toDateTimeString();

        } elseif ($newDeliveryStatus === 'failed') {
            $newOrderStatus = 'rejected';
        }

        if (!empty($newOrderStatus)) {
            $dataOrder = [
                'status'     => $newOrderStatus,
                // 'updated_at' => date($deliveryDate)
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Tambahkan delivery_date hanya jika tidak null (yaitu, jika status completed)
            if ($deliveryDate !== null) {
                $dataOrder['delivery_date'] = $deliveryDate;
            }

            $updateOrder = $this->orderModel->update($orderId, $dataOrder);

            if (!$updateOrder) {
                log_message('error', 'Gagal memperbarui status order {orderId} menjadi {newOrderStatus}.', ['orderId' => $orderId, 'newOrderStatus' => $newOrderStatus]);
            }
        }

        return redirect()->back()->with('success', 'Status pengiriman dan pesanan berhasil diperbarui menjadi ' . ucfirst(str_replace('_', ' ', $newDeliveryStatus)) . '!');
    }
}
