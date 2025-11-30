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

  public function getAll()
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

  public function insert($data)
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
}
