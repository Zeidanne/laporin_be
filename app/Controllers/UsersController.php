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

  public function updateProfile($id = null)
  {
    if (!$id) {
      return $this->response->setJSON([
        'error' => 'User ID is required',
      ])->setStatusCode(400);
    }

    $json = $this->request->getJSON(true);

    if (!$json) {
      return $this->response->setJSON([
        'error' => 'Invalid Format',
      ])->setStatusCode(400);
    }

    // Validasi user exists
    $existingUser = $this->model->getById($id);
    if (!$existingUser) {
      return $this->response->setJSON([
        'error' => 'User not found',
      ])->setStatusCode(404);
    }

    // Prepare data untuk update
    $updateData = [];

    if (isset($json['username'])) {
      $updateData['username'] = $json['username'];
    }

    if (isset($json['email'])) {
      $updateData['email'] = $json['email'];
    }

    if (isset($json['password']) && !empty($json['password'])) {
      $updateData['password'] = password_hash($json['password'], PASSWORD_DEFAULT);
    }

    if (empty($updateData)) {
      return $this->response->setJSON([
        'error' => 'No data to update',
      ])->setStatusCode(400);
    }

    $result = $this->model->updateData($id, $updateData);

    if (!$result) {
      return $this->response->setJSON([
        'error' => 'Failed to update profile',
      ])->setStatusCode(500);
    }

    return $this->response->setJSON([
      'message' => 'Profile updated successfully',
      'data' => $result
    ]);
  }

  public function getProfile($id = null)
  {
    if (!$id) {
      return $this->response->setJSON([
        'error' => 'User ID is required',
      ])->setStatusCode(400);
    }

    $user = $this->model->getById($id);

    if (!$user) {
      return $this->response->setJSON([
        'error' => 'User not found',
      ])->setStatusCode(404);
    }

    // Remove password dari response
    unset($user['password']);

    return $this->response->setJSON($user);
  }
}
