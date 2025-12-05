<?php

namespace App\Controllers;

use Cloudinary\Cloudinary;

class MediaController extends BaseController
{
  public function uploadSingle()
  {
    $file = $this->request->getFile('file');

    if (!$file || !$file->isValid()) {
      return $this->response->setStatusCode(400)->setJSON([
        'error' => 'No valid file uploaded'
      ]);
    }

    $cloudinary = new Cloudinary([
      'cloud' => [
        'cloud_name' => getenv('CLOUDINARY_NAME'),
        'api_key'    => getenv('CLOUDINARY_KEY'),
        'api_secret' => getenv('CLOUDINARY_SECRET'),
      ]
    ]);

    $result = $cloudinary->uploadApi()->upload($file->getTempName());

    return $this->response->setJSON([
      // ✅ PAKAI SECURE_URL (HTTPS)
      'url' => $result['secure_url'],
      'public_id' => $result['public_id']
    ]);
  }

  public function deleteSingle()
  {
    $json = $this->request->getJSON(true);
    $publicId = $json['public_id'] ?? null;

    if (!$publicId) {
      return $this->response->setStatusCode(400)->setJSON([
        'error' => 'No public id provided'
      ]);
    }

    $cloudinary = new Cloudinary([
      'cloud' => [
        'cloud_name' => getenv('CLOUDINARY_NAME'),
        'api_key'    => getenv('CLOUDINARY_KEY'),
        'api_secret' => getenv('CLOUDINARY_SECRET'),
      ]
    ]);

    $cloudinary->uploadApi()->destroy($publicId);

    return $this->response->setJSON([
      'message' => 'File berhasil dihapus'
    ]);
  }
}
