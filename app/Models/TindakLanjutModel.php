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

  private function defaultHeaders(): array
  {
    return [
      'apikey' => $this->serviceRole,
      'Authorization' => 'Bearer ' . $this->serviceRole,
      'Content-Type' => 'application/json'
    ];
  }

  public function insertData()
  {
    $response = $this->client->post(
      $this->url . "/rest/v1/tindak_lanjut",
      [
        'headers' => array_merge($this->defaultHeaders(), [
          'Prefer' => 'return=representation'
        ]),
        'json' => new \stdClass()
      ]
    );

    return json_decode($response->getBody(), true);
  }

  /**
   * Update tindak lanjut data (penindak, catatan_penindak, hasil)
   */
  public function updateTindakLanjut($id, $data)
  {
    $response = $this->client->request(
      'PATCH',
      $this->url . "/rest/v1/tindak_lanjut?id=eq.$id",
      [
        'headers' => array_merge($this->defaultHeaders(), [
          'Prefer' => 'return=representation'
        ]),
        'json' => $data
      ]
    );

    return json_decode($response->getBody(), true);
  }
}
