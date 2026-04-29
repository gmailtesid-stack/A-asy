<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class CloudinaryService
{
    private UploadApi $uploadApi;

    public function __construct()
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key'    => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
            'url' => ['secure' => true],
        ]);

        $this->uploadApi = new UploadApi();
    }

    /**
     * Upload file ke Cloudinary.
     *
     * @param  string  $filePath  Path file lokal
     * @param  string  $folder    Folder di Cloudinary (e.g. 'easy-pos/products')
     * @return string             Secure URL hasil upload
     */
    public function upload(string $filePath, string $folder = 'easy-pos'): string
    {
        $result = $this->uploadApi->upload($filePath, [
            'folder'          => $folder,
            'resource_type'   => 'image',
            'transformation'  => [
                ['width' => 800, 'height' => 800, 'crop' => 'limit', 'quality' => 'auto'],
            ],
        ]);

        return $result['secure_url'];
    }

    /**
     * Hapus file dari Cloudinary berdasarkan public_id.
     */
    public function delete(string $publicId): void
    {
        $this->uploadApi->destroy($publicId);
    }

    /**
     * Ambil public_id dari full Cloudinary URL.
     */
    public static function extractPublicId(string $url): string
    {
        // https://res.cloudinary.com/{cloud}/image/upload/v{ver}/{folder}/{file}.ext
        preg_match('/\/upload\/(?:v\d+\/)?(.+)\.\w+$/', $url, $matches);
        return $matches[1] ?? '';
    }
}
