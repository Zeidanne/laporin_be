<?php

namespace App\Controllers;

use App\Models\LaporinModel;
use App\Models\TindakLanjutModel;

class PenindakLaporinController extends BaseController
{
  protected $model;
  protected $tindakModel;

  public function __construct()
  {
    $this->model = new LaporinModel();
    $this->tindakModel = new TindakLanjutModel();
  }

  /**
   * Daftar semua laporan untuk penindak (admin).
   * Query param optional: status=0|1|2
   */
  public function index()
  {
    $statusParam = $this->request->getGet('status');
    $jenisParam = $this->request->getGet('jenis_laporan');
    $searchParam = $this->request->getGet('search');

    $filters = [
      'status' => ($statusParam !== null && $statusParam !== '') ? (int) $statusParam : null,
      'jenis_laporan' => $jenisParam ?: null,
      'search' => $searchParam ?: null,
    ];

    $data = $this->model->getAllForPenindak($filters);

    return $this->response->setJSON([
      'success' => true,
      'data' => $data,
    ]);
  }

  /**
   * Detail laporan untuk penindak.
   */
  public function detail($id = null)
  {
    if (!$id) {
      return $this->response->setJSON([
        'error' => 'Laporan ID is required'
      ])->setStatusCode(400);
    }

    $data = $this->model->getLaporanById($id);

    if (!$data) {
      return $this->response->setJSON([
        'error' => 'Laporan not found'
      ])->setStatusCode(404);
    }

    return $this->response->setJSON([
      'success' => true,
      'data' => $data,
    ]);
  }

  /**
   * Tindak lanjut laporan oleh penindak.
   * Request body:
   * - penindak_id (required): ID user penindak
   * - catatan_penindak (required): Catatan dari penindak
   * - hasil (optional): URL gambar bukti tindak lanjut
   * 
   * Status akan berubah menjadi:
   * - 1 (Sedang Diproses) jika hanya catatan
   * - 2 (Selesai) jika catatan + gambar
   */
  public function tindakLanjut($id = null)
  {
    if (!$id) {
      return $this->response->setJSON([
        'error' => 'Laporan ID is required'
      ])->setStatusCode(400);
    }

    // Get request body
    $json = $this->request->getJSON(true);

    if (!$json) {
      return $this->response->setJSON([
        'error' => 'Invalid request format'
      ])->setStatusCode(400);
    }

    // Validate required fields
    if (empty($json['penindak_id'])) {
      return $this->response->setJSON([
        'error' => 'penindak_id is required'
      ])->setStatusCode(400);
    }

    if (empty($json['catatan_penindak'])) {
      return $this->response->setJSON([
        'error' => 'catatan_penindak is required'
      ])->setStatusCode(400);
    }

    // Get laporan to find hasil_tindak FK
    $laporan = $this->model->getLaporanById($id);

    if (!$laporan) {
      return $this->response->setJSON([
        'error' => 'Laporan not found'
      ])->setStatusCode(404);
    }

    $hasilTindakId = $laporan['hasil_tindak'] ?? null;

    if (!$hasilTindakId) {
      return $this->response->setJSON([
        'error' => 'Tindak lanjut record not found for this laporan'
      ])->setStatusCode(404);
    }

    // Prepare tindak lanjut data
    $tindakData = [
      'penindak' => $json['penindak_id'],
      'catatan_penindak' => $json['catatan_penindak']
    ];

    // Check if hasil (image URL) is provided
    $hasHasil = !empty($json['hasil']);
    if ($hasHasil) {
      $tindakData['hasil'] = $json['hasil'];
    }

    // Update tindak_lanjut table
    $this->tindakModel->updateTindakLanjut($hasilTindakId, $tindakData);

    // Determine new status based on whether image is provided
    // 1 = Sedang Diproses (catatan only)
    // 2 = Selesai (catatan + gambar)
    $newStatus = $hasHasil ? 2 : 1;

    // Update laporin status
    $this->model->updateStatus($id, $newStatus);

    // Get updated laporan
    $updatedLaporan = $this->model->getLaporanById($id);

    return $this->response->setJSON([
      'success' => true,
      'message' => $hasHasil ? 'Laporan telah diselesaikan' : 'Laporan sedang diproses',
      'data' => $updatedLaporan
    ]);
  }
}
