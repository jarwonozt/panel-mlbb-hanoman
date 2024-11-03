<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\UserModel;
use CodeIgniter\Config\Services;

class ApiKeyFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $apiKey = $request->getHeaderLine('x-api-key');
        
        if (empty($apiKey)) {
            return Services::response()
                ->setJSON(['error' => 'API key required'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        $model = new UserModel();
        $user = $model->where('api_key', $apiKey)->first();

        if (!$user) {
            return Services::response()
                ->setJSON(['error' => 'Invalid API key'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        // API key valid, bisa melanjutkan
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi setelah request
    }
}
