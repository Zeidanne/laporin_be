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

  public function getAllData()
  {
    $response = $this->client->get(
      $this->url . "/rest/v1/users?select=*",
      [
        'headers' => [
          'apikey' => $this->serviceRole,
          'Authorization' => 'Bearer ' . $this->serviceRole,
          'Content-Type' => 'application/json',
        ]
      ]
    );

    return json_decode($response->getBody(), true);
  }

  public function insertData($data)
  {
    $response = $this->client->post(
      $this->url . "/rest/v1/users",
      [
        'headers' => [
          'apikey' => $this->serviceRole,
          'Authorization' => 'Bearer ' . $this->serviceRole,
          'Content-Type' => 'application/json',
          'Prefer' => 'return=representation'
        ],
        'json' => $data
      ]
    );

    return json_decode($response->getBody(), true);
  }

  public function login($data)
  {
    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    $response = $this->client->get(
      $this->url . "/rest/v1/users?email=eq.$email&select=*",
      [
        'headers' => [
          'apikey' => $this->serviceRole,
          'Authorization' => 'Bearer ' . $this->serviceRole,
          'Content-Type' => 'application/json',
        ]
      ]
    );

    $users = json_decode($response->getBody(), true);

    // Kalau tidak ada user
    if (empty($users)) {
      return false;
    }

    $user = $users[0]; // Ambil user tunggal

    // Verifikasi password
    if (!password_verify($password, $user['password'])) {
      return false;
    }

    return $user;
  }
}
