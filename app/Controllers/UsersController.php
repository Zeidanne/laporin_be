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

  public function create()
  {
    $data = [
      'username' => 'johndoe2',
      'email' => 'john2@example.com',
      'password' => password_hash('12345678', PASSWORD_DEFAULT)
    ];

    $result = $this->model->insertData($data);
    return $this->response->setJSON($result);
  }
}
