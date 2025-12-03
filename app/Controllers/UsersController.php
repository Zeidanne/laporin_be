<?php

namespace App\Controllers;

use App\Models\UsersModel;

class UsersController extends BaseController
{
  protected $model;

  public function __construct()
  {
    $this->model = new UsersModel();
  }

  public function index()
  {
    $data = $this->model->getAllData(); // fetch semua user
    return $this->response->setJSON($data);
  }

  public function insert()
  {
    $json = $this->request->getJSON(true);

    if (!$json) {
      return $this->response->setJSON([
        'error' => 'Invalid Format',
      ])->setStatusCode(400);
    }
    $json['password'] = password_hash($json['password'], PASSWORD_DEFAULT);
    $result = $this->model->insertData($json);
    return $this->response->setJSON($result);
  }
}
