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
    $data = $this->model->getAllData();
    return $this->response->setJSON($data);
  }

  public function login()
  {
    $json = $this->request->getJSON(true);
    $result = $this->model->login($json);

    if (!$result) {
      return $this->response->setJSON([
        'error' => 'Wrong Credentials',
      ])->setStatusCode(401);
    }

    return $this->response->setJSON($result);
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
