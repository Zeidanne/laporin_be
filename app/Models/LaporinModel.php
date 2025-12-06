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

  public function getLaporanByPelapor($pelaporId, $filters = [])
  {
    // Base query dengan join ke tabel terkait menggunakan FK hasil_tindak
    $query = "laporin?pelapor=eq.$pelaporId&select=*,jenis_laporan(nama),tindak_lanjut:hasil_tindak(id,penindak,hasil,catatan_penindak)";

    // Tambahkan filter jenis laporan jika ada
    if (isset($filters['jenis_laporan']) && !empty($filters['jenis_laporan'])) {
      $query .= "&jenis_laporan=eq." . $filters['jenis_laporan'];
    }

    // Tambahkan filter status jika ada
    if (isset($filters['status']) && !empty($filters['status'])) {
      $query .= "&status=eq." . $filters['status'];
    }

    // Tambahkan search jika ada
    if (isset($filters['search']) && !empty($filters['search'])) {
      $search = rawurlencode($filters['search']);
      $query .= "&or=(alamat.ilike.*$search*,catatan_pelapor.ilike.*$search*)";
    }

    // Urutkan berdasarkan waktu terbaru
    $query .= "&order=waktu.desc";

    $response = $this->client->get(
      $this->url . "/rest/v1/" . $query,
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

  public function getLaporanById($id)
  {
    // Query dengan join semua tabel terkait
    // hasil_tindak adalah FK yang menunjuk ke tindak_lanjut.id
    // Kemudian dari tindak_lanjut, penindak adalah FK yang menunjuk ke users.id
    $query = "laporin?id=eq.$id&select=*,jenis_laporan(nama),tindak_lanjut:hasil_tindak(id,penindak,hasil,catatan_penindak,users:penindak(id,username,email))";

    $response = $this->client->get(
      $this->url . "/rest/v1/" . $query,
      [
        'headers' => [
          'apikey' => $this->serviceRole,
          'Authorization' => 'Bearer ' . $this->serviceRole,
          'Content-Type' => 'application/json'
        ]
      ]
    );

    $result = json_decode($response->getBody(), true);
    return $result[0] ?? null;
  }
}
