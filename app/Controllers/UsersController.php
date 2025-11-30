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
    $data = $this->model->getAll(); // fetch semua user
    return $this->response->setJSON($data);
  }

  public function create()
  {
    $data = [
      'username' => 'johndoe',
      'email' => 'john@example.com',
      'password' => password_hash('123456', PASSWORD_DEFAULT)
    ];

    $result = $this->model->insert('users', $data);
    return $this->response->setJSON($result);
  }
}
