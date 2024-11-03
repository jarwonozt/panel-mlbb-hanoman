<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class PanelAPIClientController extends BaseController
{
    use ResponseTrait;
    protected $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function index()
    {
        $data = [
            'panel_name'   => 'Laragon',
            'panel_url'    => site_url(),
            'admin_panel'  => $this->model->getUser(session()->userid)->username,
            'expired'      => 31,
            'status'       => true,
            'description'  => null,
        ];

        // dd($data);

        $client = \Config\Services::curlrequest();

        $response = $client->post('https://panel.jarwonozt.my.id/panels', [
            'headers' => [
                'Content-Type' => 'application/json',
                'x-api-key' => 'b090963f32b04b1e29ccfb2ec285a1e0044095a5d84b1577243dba034aa18a79',
            ],
            'json'    => $data,
            'timeout' => 10,
        ]);

        if ($response) {
            return redirect()->back()->with('msgSuccess', 'Panel Activation Successfully');
        } else {
            dd('Failed to send data to external API');
        }
    }
}
