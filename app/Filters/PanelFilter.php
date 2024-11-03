<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class PanelFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Load the model (make sure it's available)
        $panelModel = new \App\Models\PanelModel();

        // Check if getInfo() returns null
        if ($panelModel->getInfo() === null) {
            // Redirect or return an error response
            // return redirect()->to('/your-login-page'); 
            return redirect()->to('/')->with('error', 'Access denied.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something after the request
    }
}
