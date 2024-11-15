<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PanelModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class PanelAPIClientController extends BaseController
{
    use ResponseTrait;
    protected $model, $time, $modelPanel;

    public function __construct()
    {
        $this->model = new UserModel();
        $this->time = new \CodeIgniter\I18n\Time;
        $this->modelPanel = new PanelModel();
    }

    public function index()
    {
        $data = [
            'panel_name'   => config('App')->appName,
            'panel_url'    => base_url(),
            'admin_panel'  => $this->model->getUser(session()->userid)->username,
            'expired'      => $this->time::now()->addDays(30)->toLocalizedString('YYYY-MM-dd HH:mm:ss'),
            'status'       => true,
            'description'  => null,
        ];

        $existingPanel = $this->modelPanel->where('admin_panel', $data['admin_panel'])->first();

        if ($existingPanel) {
            return redirect()->back()->with('msgDanger', 'Panel already exists.');
        }

        if ($this->modelPanel->insert($data)) {
            return redirect()->back()->with('msgSuccess', 'Panel Activated Successfully');
        } else {
            return redirect()->back()->with('msgDanger', 'An error occurred');
        }

        // dd($data);

        // $jsonData = json_encode($data);
        // if (json_last_error() !== JSON_ERROR_NONE) {
        //     dd('JSON encoding error: ' . json_last_error_msg());
        // }
        // $apiEndpoint = config('App')->endpointApi;
        // $ch = curl_init('http://laragon.pmiiunusia.id/panels');

        // // Set cURL options
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, [
        //     'Content-Type: application/json',
        //     'x-api-key: b090963f32b04b1e29ccfb2ec285a1e0044095a5d84b1577243dba034aa18a79',
        // ]);
        // curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10 seconds timeout

        // // Execute the request
        // $response = curl_exec($ch);

        // // Check for errors
        // if (curl_errno($ch)) {
        //     $errorMessage = curl_error($ch);
        //     curl_close($ch);
        //     return $this->response->setStatusCode(500)->setBody('cURL Error: ' . $errorMessage);
        // }

        // // Close the cURL session
        // curl_close($ch);
        // if ($response) {
        //     $res = json_decode($response, true);
        //     $msg =  $res['status'] == 'success' ? 'Panel Actived Successfully' : 'An error occurred or expired';
        //     if ($res['status'] == 'success') {
        //         return redirect()->back()->with('msgSuccess', $msg);
        //     } else {
        //         return redirect()->back()->with('msgDanger', $msg);
        //     }
        // } else {
        //     dd('Failed to send data to external API');
        // }
    }
}
