<?php

// namespace App\Controllers;


// use CodeIgniter\RESTful\ResourceController;
// use App\Models\PanelModel;
// use App\Models\UserModel;

// class PanelAPIController extends ResourceController
// {
//     protected $model;
//     protected $format, $time;
//     protected $userModel;

//     public function __construct()
//     {
//         $this->model  = new PanelModel();
//         $this->format = 'json';
//         $this->userModel = new UserModel();
//         $this->time = new \CodeIgniter\I18n\Time;
//     }

//     public function index()
//     {
//         if ($this->request->getPost()) {
//             return $this->create();
//         }

//         $items = $this->model->findAll();
//         // return $this->response->setJSON($items);
//         return $this->respondCreated([
//             'status' => 'success',
//             'message' => 'Data Panel Registed',
//             'data' => $items
//         ]);
//     }

//     public function show($id = null)
//     {
//         $item = $this->model->find($id);
//         if ($item) {
//             return $this->response->setJSON($item);
//         } else {
//             return $this->failNotFound('Data not found');
//         }
//     }

//     public function create()
//     {
//         $validation = \Config\Services::validation();

//         $validation->setRules([
//             'panel_name'   => 'required',
//             'panel_url'    => [
//                 'rules'  => 'required|is_unique[panels.panel_url]',
//                 'errors' => [
//                     'required'   => 'Field admin_panel wajib diisi.',
//                     'is_unique'  => 'Panel ini sudah Aktif, silakan digunakan.',
//                 ],
//             ],
//             'admin_panel'  => [
//                 'rules'  => 'required|is_unique[panels.admin_panel]',
//                 'errors' => [
//                     'required'   => 'Field admin_panel wajib diisi.',
//                     'is_unique'  => 'Panel ini sudah Aktif, silakan digunakan.',
//                 ],
//             ],
//             'status'       => 'required|in_list[0,1]',
//             'description'  => 'permit_empty|string',
//         ]);

//         if (!$validation->withRequest($this->request)->run()) {
//             return $this->failValidationErrors($validation->getErrors());
//         }
//         $expired = $this->time->now()->addDays($this->request->getPost('expired'));
//         $data = [
//             'panel_name'   => $this->request->getPost('panel_name'),
//             'panel_url'    => $this->request->getPost('panel_url'),
//             'admin_panel'  => $this->request->getPost('admin_panel'),
//             'expired'      => 30,
//             'status'       => $this->request->getPost('status'),
//             'description'  => $this->request->getPost('description'),
//         ];

//         $json = $this->request->getJSON();
//         if ($json === null) {
//             return $this->failValidationErrors('Invalid JSON data');
//         }
    
//         $data = [
//             'panel_name'   => $json->panel_name,
//             'panel_url'    => $json->panel_url,
//             'admin_panel'  => $json->admin_panel,
//             'expired'      => '2024-10-19 04:04:47'            ,
//             'status'       => $json->status,
//             'description'  => $json->description,
//         ];

//         if ($this->model->insert($data)) {
//             return $this->respondCreated([
//                 'status' => 'success',
//                 'message' => 'Panel created successfully',
//                 'data' => $data
//             ]);
//         } else {
//             return $this->failValidationErrors($this->model->errors());
//         }
//     }

//     public function update($id = null)
//     {
//         $data = $this->request->getRawInput();
//         if ($this->model->update($id, $data)) {
//             return $this->respond($data, 200, 'Data updated');
//         } else {
//             return $this->failValidationErrors($this->model->errors());
//         }
//     }

//     public function delete($id = null)
//     {
//         if ($this->model->find($id)) {
//             $this->model->delete($id);
//             return $this->respondDeleted(['id' => $id], 'Data deleted');
//         } else {
//             return $this->failNotFound('Data not found');
//         }
//     }
// }
