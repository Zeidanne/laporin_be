<?php

namespace App\Models;

use Config\Services;

class UsersModel
{
  private $client;
  private $url;
  private $serviceRole;

  public function __construct()
  {
    $this->client = Services::curlrequest();
    $this->url = getenv('SUPABASE_URL');
    $this->serviceRole = getenv('SUPABASE_SERVICE_ROLE');
  }

  private function defaultHeaders(): array
  {
    return [
      'apikey' => $this->serviceRole,
      'Authorization' => 'Bearer ' . $this->serviceRole,
      'Content-Type' => 'application/json',
    ];
  }

  public function getAllData()
  {
    $response = $this->client->get(
      $this->url . "/rest/v1/users?select=*",
      [
        'headers' => $this->defaultHeaders()
      ]
    );

    return json_decode($response->getBody(), true);
  }

  public function googleAuth($data)
  {
    // Cek apakah user sudah ada berdasarkan email
    $existing = $this->client->get(
      $this->url . "/rest/v1/users?email=eq." . $data['email'],
      [
        'headers' => $this->defaultHeaders()
      ]
    );

    $existingData = json_decode($existing->getBody(), true);

    if (!empty($existingData)) {
      return $existingData;
    }

    $response = $this->client->post(
      $this->url . "/rest/v1/users",
      [
        'headers' => array_merge($this->defaultHeaders(), [
          'Prefer' => 'return=representation'
        ]),
        'json' => $data
      ]
    );

    return json_decode($response->getBody(), true);
  }


  public function insertData($data)
  {
    $response = $this->client->post(
      $this->url . "/rest/v1/users",
      [
        'headers' => array_merge($this->defaultHeaders(), [
          'Prefer' => 'return=representation'
        ]),
        'json' => $data
      ]
    );
    return json_decode($response->getBody(), true);
  }

  public function getByEmail(?string $email)
  {
    if (!$email) {
      return null;
    }

    $encodedEmail = rawurlencode($email);

    $response = $this->client->get(
      $this->url . "/rest/v1/users?email=eq.$encodedEmail&select=*",
      [
        'headers' => $this->defaultHeaders()
      ]
    );

    $users = json_decode($response->getBody(), true);

    return $users[0] ?? null;
  }

  public function register(array $data)
  {
    $payload = [
      'username' => $data['username'],
      'email' => $data['email'],
      'password' => password_hash($data['password'], PASSWORD_DEFAULT),
      'roles' => 2,
      'created_at' => date('Y-m-d H:i:s'),
    ];

    return $this->insertData($payload);
  }

  public function login($data)
  {
    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    $user = $this->getByEmail($email);

    // Kalau tidak ada user
    if (empty($user)) {
      return false;
    }

    // Verifikasi password
    if (!password_verify($password, $user['password'])) {
      return false;
    }

    return $user;
  }

  public function getById($id)
  {
    if (!$id) {
      return null;
    }

    $response = $this->client->get(
      $this->url . "/rest/v1/users?id=eq.$id&select=*",
      [
        'headers' => $this->defaultHeaders()
      ]
    );

    $users = json_decode($response->getBody(), true);

    return $users[0] ?? null;
  }

  public function updateData($id, $data)
  {
    if (!$id || empty($data)) {
      return null;
    }

    $response = $this->client->patch(
      $this->url . "/rest/v1/users?id=eq.$id",
      [
        'headers' => array_merge($this->defaultHeaders(), [
          'Prefer' => 'return=representation'
        ]),
        'json' => $data
      ]
    );

    $result = json_decode($response->getBody(), true);

    return $result[0] ?? null;
  }
}
