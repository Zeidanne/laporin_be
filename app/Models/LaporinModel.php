<?php

namespace App\Models;

use Config\Services;

class LaporinModel
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

  public function insertData($data)
  {
    $response = $this->client->post(
      $this->url . "/rest/v1/laporin",
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

  public function getJenisLaporan()
  {
    $response = $this->client->get(
      $this->url . "/rest/v1/jenis_laporan?select=*",
      [
        'headers' => [
          'apikey' => $this->serviceRole,
          'Authorization' => 'Bearer ' . $this->serviceRole,
          'Content-Type' => 'application/json'
        ]
      ]
    );
    return json_decode($response->getBody(), true);
  }
}
