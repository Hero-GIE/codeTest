<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PublitioService
{
    private $apiKey;
    private $apiSecret;
    private $baseUrl = 'https://api.publit.io/v1';

    public function __construct()
    {
        $this->apiKey    = env('PUBLITIO_API_KEY', 'vBiyqsmd27xcy5H1L3GD');
        $this->apiSecret = env('PUBLITIO_API_SECRET', 'MzDoAaA3Mtaeqbma9ZacPpLmDTxpuRVe');

        if (! $this->apiKey || ! $this->apiSecret) {
            Log::error('Publitio API credentials are missing');
            throw new \Exception('Publitio API credentials are not configured');
        }
    }

    /**
     * Upload file to Publitio
     */
    public function uploadFile($file, $folder = 'QDh5kALd', $title = null)
    {
        try {
            Log::info("📤 Starting Publitio upload for file: " . $file->getClientOriginalName());

            // Prepare the file for upload
            $fileContent = file_get_contents($file->getRealPath());
            $fileName    = $title ?: $file->getClientOriginalName();

            // Create multipart request
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('PUBLITIO_TOKEN_KEY', 'UXfhLwqlYNH8ZW6KPDNqTh6zzc6XFS2KO6KtLDwM'),
                ])
                ->asMultipart()
                ->post("{$this->baseUrl}/files/create", [
                    [
                        'name'     => 'file',
                        'contents' => $fileContent,
                        'filename' => $fileName,
                    ],
                    [
                        'name'     => 'folder',
                        'contents' => $folder,
                    ],
                    [
                        'name'     => 'title',
                        'contents' => $fileName,
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();

                // Validate required fields in response
                if (! isset($data['url_preview']) || ! isset($data['id'])) {
                    Log::error("❌ Publitio response missing required fields", $data);
                    return [
                        'success' => false,
                        'error'   => 'Invalid response from upload service',
                    ];
                }
                Log::info("✅ Publitio upload successful", [
                    'file_id'   => $data['id'],
                    'url'       => $data['url_preview'],
                    'file_name' => $fileName,
                ]);

                return [
                    'success'     => true,
                    'url'         => $data['url_preview'],
                    'publitio_id' => $data['id'],
                    'bytes'       => $data['size'] ?? 0,
                    'format'      => $data['format'] ?? $data['extension'] ?? pathinfo($fileName, PATHINFO_EXTENSION),
                    'type'        => $data['type'] ?? 'image',
                ];
            } else {
                Log::error("❌ Publitio upload failed", [
                    'status'    => $response->status(),
                    'response'  => $response->body(),
                    'file_name' => $fileName,
                ]);

                return [
                    'success' => false,
                    'error'   => 'Upload failed: ' . $response->status() . ' - ' . $response->body(),
                ];
            }

        } catch (\Exception $error) {
            Log::error("❌ Publitio upload exception", [
                'error' => $error->getMessage(),
                'file'  => $file->getClientOriginalName(),
            ]);

            return [
                'success' => false,
                'error'   => 'Upload exception: ' . $error->getMessage(),
            ];
        }
    }

    /**
     * Delete file from Publitio
     */
    public function deleteFile($publitioId)
    {
        try {
            Log::info("🗑️ Deleting Publitio file: " . $publitioId);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('PUBLITIO_TOKEN_KEY', 'UXfhLwqlYNH8ZW6KPDNqTh6zzc6XFS2KO6KtLDwM'),
                ])
                ->post("{$this->baseUrl}/files/delete/{$publitioId}");

            if ($response->successful()) {
                Log::info("✅ Publitio file deleted successfully", ['file_id' => $publitioId]);
                return ['success' => true];
            } else {
                Log::error("❌ Publitio file deletion failed", [
                    'file_id'  => $publitioId,
                    'status'   => $response->status(),
                    'response' => $response->body(),
                ]);
                return ['success' => false, 'error' => 'Deletion failed'];
            }

        } catch (\Exception $error) {
            Log::error("❌ Publitio deletion exception", [
                'error'   => $error->getMessage(),
                'file_id' => $publitioId,
            ]);
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

    /**
     * List files in folder (optional)
     */
    public function listFiles($folder = 'QDh5kALd', $limit = 50)
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('PUBLITIO_TOKEN_KEY', 'UXfhLwqlYNH8ZW6KPDNqTh6zzc6XFS2KO6KtLDwM'),
                ])
                ->get("{$this->baseUrl}/files/list", [
                    'folder' => $folder,
                    'limit'  => $limit,
                ]);

            if ($response->successful()) {
                return ['success' => true, 'files' => $response->json()];
            } else {
                return ['success' => false, 'error' => 'Failed to list files'];
            }

        } catch (\Exception $error) {
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }
}
