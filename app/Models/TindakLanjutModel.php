<?php

namespace App\Models;

use Config\Services;

class TindakLanjutModel
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

  public function insertData()
  {
    $response = $this->client->post(
      $this->url . "/rest/v1/tindak_lanjut",
      [
        'headers' => [
          'apikey' => $this->serviceRole,
          'Authorization' => 'Bearer ' . $this->serviceRole,
          'Content-Type' => 'application/json',
          'Prefer' => 'return=representation'
        ],
        'json' => new \stdClass()
      ]
    );

    return json_decode($response->getBody(), true);
  }
}
