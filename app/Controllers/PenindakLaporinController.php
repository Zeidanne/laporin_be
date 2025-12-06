<?php

namespace App\Controllers;

use App\Models\LaporinModel;

class PenindakLaporinController extends BaseController
{
  protected $model;

  public function __construct()
  {
    $this->model = new LaporinModel();
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
}
