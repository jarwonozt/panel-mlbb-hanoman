<?php

namespace App\Controllers;

use App\Models\KeysModel;

class Connect extends BaseController
{
    protected $model, $game, $uKey, $sDev;

    public function __construct()
    {
        $this->model = new KeysModel();
        $this->maintenance = false;
        $this->staticRandw = "Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E";
    }


    function generateRandomString($length = 20) {
    $characters = '012346789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return md5($randomString);
    } 

    public function index()
    {
        if ($this->request->getPost()) {
            return $this->index_post();
        } else {
            $nata = [
                "Info" => [
                        'message' => 'HI NOOB!!'
                        ],
                "web_info" => [
                    "_client" => "XARG-YUNI",
                    "license" => "Qp5KSGTquetnUkjX6UVBAURH8hTkZuLM",
                    "version" => "9.9.9.999999++++",
                ],
                "dev" => [
                    "author" => "XARG",
                    "telegram" => "https://t.me/@channelarg"
                ],
            ];

            return $this->response->setJSON($nata);
        }
    }

    public function index_post()
    {
        $isMT = $this->maintenance;
        $game = $this->request->getPost('game');
        $uKey = $this->request->getPost('user_key');
        $sDev = $this->request->getPost('serial');

        if ($isMT) {
            $data = [
                'status' => false,
                'reason' => 'MAINTENANCE'
            ];
        } else {
            if (!$game or !$uKey or !$sDev) {
                $data = [
                    'status' => false,
                    'reason' => 'INVALID PARAMETER'
                ];
            } else {
                $time = new \CodeIgniter\I18n\Time;
                $model = $this->model;
                $findKey = $model
                    ->getKeysGame(['user_key' => $uKey, 'game' => $game]);

                if ($findKey) {
                    $id_keys = $findKey->id_keys;
                    $duration = $findKey->duration;
                    $expired = $findKey->expired_date;
                    $max_dev = $findKey->max_devices;
                    $devices = $findKey->devices;

                    function checkDevicesAdd($serial, $devices, $max_dev)
                    {
                        $lsDevice = explode(",", $devices);
                        $cDevices = count($lsDevice);
                        $serialOn = in_array($serial, $lsDevice);

                        if ($serialOn) {
                            return true;
                        } else {
                            if ($cDevices < $max_dev || $devices == null ) {
                                array_push($lsDevice, $serial);
                                $setDevice = reduce_multiples(implode(",", $lsDevice), ",", true);
                                return ['devices' => $setDevice];
                            } else {
                                // ! false - devices max
                                return false;
                            }
                        }
                    }

                    if (!$expired) {
                        $setExpired = $time::now()->addDays($duration);
                        $model->update($id_keys, ['expired_date' => $setExpired]);
                        $data['status'] = true;
                    } else {
                        if ($time::now()->isBefore($expired)) {
                            $data['status'] = true;
                        } else {
                            $data = [
                                'status' => false,
                                'reason' => 'EXPIRED KEY'
                            ];
                        }
                    }

                    if ($data['status']) {
                        $devicesAdd = checkDevicesAdd($sDev, $devices, $max_dev);
                        if ($devicesAdd) {
                            if (is_array($devicesAdd)) {
                                $model->update($id_keys, $devicesAdd);
                            }
                                                            if (!$expired) { 
                                $setExpired = $time::now()->addDays($duration);
                                $EXPIREDSTRING = number_format(strtotime($setExpired)*1000, 0, '.', '');
                                } else {
                                $EXPIREDSTRING = number_format(strtotime($expired)*1000, 0, '.', '');
                                }
                            $real = "$game-$uKey-$sDev-$this->staticRandw";
                            $data = [
                                'status' => true,
                                'data' => [
                                    'real' => $real,
                                    'token' => md5($real),
                                    'rng' => $time->getTimestamp() ,
                                    'ts' => $EXPIREDSTRING
                                   // 'hash' => generateRandomString()
                                ],
                            ];
                        } else {
                            $data = [
                                'status' => false,
                                'reason' => 'MAX DEVICE REACHED'
                            ];
                        }
                    }
                } else {
                    $data = [
                        'status' => false,
                        'reason' => 'USER OR GAME NOT REGISTERED'
                    ];
                }
            }
        }
        return $this->response->setJSON($data);
    }
}
