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

    $requiredFields = ['username', 'email', 'password'];
    foreach ($requiredFields as $field) {
      if (empty($json[$field])) {
        return $this->response->setJSON([
          'error' => "Field $field is required",
        ])->setStatusCode(422);
      }
    }

    if (!filter_var($json['email'], FILTER_VALIDATE_EMAIL)) {
      return $this->response->setJSON([
        'error' => 'Invalid email format',
      ])->setStatusCode(422);
    }

    if ($this->model->getByEmail($json['email'])) {
      return $this->response->setJSON([
        'error' => 'Email already registered',
      ])->setStatusCode(409);
    }

    $result = $this->model->register($json);
    return $this->response->setJSON($result)->setStatusCode(201);
  }
}
