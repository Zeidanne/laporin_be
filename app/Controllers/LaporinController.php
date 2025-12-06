<?php

namespace App\Controllers;

use App\Models\LaporinModel;
use App\Models\TindakLanjutModel;

class LaporinController extends BaseController
{
  protected $model;

  public function __construct()
  {
    $this->model = new LaporinModel();
  }

  public function jenisLaporan()
  {
    $data = $this->model->getJenisLaporan();
    return $this->response->setJSON($data);
  }

  public function insert()
  {
    $tindakModel = new TindakLanjutModel();
    $json = $this->request->getJSON(true);

    if (!$json) {
      return $this->response->setJSON(['error' => 'Invalid Format'])->setStatusCode(400);
    }

    $hasilTindak = $tindakModel->insertData();
    $idTindak = $hasilTindak[0]['id'] ?? null;

    if (!$idTindak) {
      return $this->response->setJSON(['error' => 'Gagal insert tindak lanjut'])->setStatusCode(500);
    }

    $json['hasil_tindak'] = $idTindak;

    // Insert ke tabel laporin
    $result = $this->model->insertData($json);

    return $this->response->setJSON($result);
  }

  public function riwayatUser($userId = null)
  {
    if (!$userId) {
      return $this->response->setJSON([
        'error' => 'User ID is required'
      ])->setStatusCode(400);
    }

    // Ambil filter dari query params
    $filters = [];
    
    $jenisLaporan = $this->request->getGet('jenis_laporan');
    if ($jenisLaporan) {
      $filters['jenis_laporan'] = $jenisLaporan;
    }

    $status = $this->request->getGet('status');
    if ($status) {
      $filters['status'] = $status;
    }

    $search = $this->request->getGet('search');
    if ($search) {
      $filters['search'] = $search;
    }

    $data = $this->model->getLaporanByPelapor($userId, $filters);

    return $this->response->setJSON([
      'success' => true,
      'data' => $data
    ]);
  }

  public function detailUser($id = null)
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
      'data' => $data
    ]);
  }
}
