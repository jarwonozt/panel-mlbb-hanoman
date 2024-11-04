<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\I18n\Time;

class PanelFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Load the model (make sure it's available)
        $panelModel = new \App\Models\PanelModel();

        $panelInfo = $panelModel->getInfoByAdmin(session('unames'));

        if ($panelInfo === null) {
            return redirect()->to('/')->with('error', 'Access denied: No panel information found for user.');
        }

        $targetDate = new Time($panelInfo['expired']);
        $currentDate = new Time();

        if ($currentDate > $targetDate) {
            return redirect()->to('/')->with('error', 'Access denied: Your panel has expired.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something after the request
    }
}
