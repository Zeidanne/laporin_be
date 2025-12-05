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
}
