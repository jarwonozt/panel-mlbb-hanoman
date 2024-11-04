<?php

namespace App\Controllers;

use App\Models\KeysModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\Response;

class Connect extends BaseController
{
    protected $model, $time, $game, $uKey, $sDev, $maintenance, $staticRandw, $userModel, $user;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->user = $this->userModel->getUser();
        $this->model = new KeysModel();
        $this->maintenance = false;
        $this->staticRandw = "Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E";
        $this->time = new \CodeIgniter\I18n\Time;
    }

    function generateRandomString($length = 20)
    {
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
        }
    }

    public function index_post()
    {
        function encrypt($data)
        {
            $key = 'tx-₹7#_-3%%09*';
            $iv =  'tx-₹7#_-3%%09*';
            $method = 'AES-128-CBC';
            $ivsize = openssl_cipher_iv_length($method);
            $en = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
            return base64_encode($en);
        }

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
                    ->getKeysGame(['user_key' => $uKey, 'game' => $game, 'status' => true]);

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
                            if ($cDevices < $max_dev || $devices == null) {
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

                        if ($duration > 100) {
                            $setExpired = $time::now()->addMinutes($duration);
                        } else {
                            $setExpired = $time::now()->addDays($duration);
                        }

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
                        //TODO : Get notification from database
                        $connect = db_connect();
                        $builder = $connect->table('apk');
                        $builder->where('id_apk', 1);
                        $query = $builder->get();


                        foreach ($query->getResult() as $row) {
                            $stat = $row->status;
                            $nottext = $row->notification;
                        }

                        $devicesAdd = checkDevicesAdd($sDev, $devices, $max_dev);
                        if ($devicesAdd) {
                            if (is_array($devicesAdd)) {
                                $model->update($id_keys, $devicesAdd);
                            }
                            if (!$expired) {
                                if ($duration > 100) {
                                    $setExpired = $time::now()->addMinutes($duration);
                                } else {
                                    $setExpired = $time::now()->addDays($duration);
                                }
                                $EXPIREDSTRING = number_format(strtotime($setExpired) * 1000, 0, '.', '');
                            } else {
                                $EXPIREDSTRING = number_format(strtotime($expired) * 1000, 0, '.', '');
                            }
                            $real = "$game-$uKey-$sDev-$this->staticRandw";
                            $data = [
                                'status' => true,
                                'data' => [
                                    'real' => $real,
                                    'token' => md5($real),
                                    'rng' => $time->getTimestamp(),
                                    'ts' => $EXPIREDSTRING,
                                    'client' =>  $this->request->getPost('client'),
                                    'daemon' => $this->request->getPost('daemon')
                                ],
                                'exc_data' => [
                                    'world3_0' => encrypt("k3fXvld=")
                                ],
                                'apk_data' => [
                                    'uLink' => encrypt("JasqWplPdYHxznkgLhxTNlGQIkIpDl6PumocxFGLLMKQWJahEspPztzZKqjpLanwugINqm 2NZObOBxPx6K7UE75lldYzmX4M2BPLtCMVEIhHh SP8AeMiRVjwTIWpiedMzymN53kJrPK wdHEE1rrt2ms5hp5iGJasqWplPdYHxznkgLhxTNlGQIkIpDl6PumocxFGLLMKQWJqeGllSj71INq4ZmMJWYoefFMGYNn4cKFiVpQXTICA1EMdRAnrdKGfaxqkVIE7DEstNM3k7NqjWL30WuLR1F7hZmNKNk6OKjtAQVouwqlDLAsEgkLOZqvwZV5ublltXNtsN"),
                                    'toast' => encrypt("Login Success"),
                                    'snotif' => encrypt($stat),
                                    'fnotif' => encrypt($nottext)
                                ],
                                'apk_brand' => [
                                    'xarg_float1' => encrypt("XARG ESP"),
                                    'xarg_float2' => encrypt("XARG INJECTOR"),
                                    'ttd_float1' => encrypt("TANTEDARA ESP"),
                                    'ttd_float2' => encrypt("TANTEDARA INJECTOR")
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
        return encrypt(json_encode($data));
    }

    public function usernameCheck()
    {
        if ($this->request->getPost()) {
            return $this->usernameCheckApi()();
        } else {
            $account_id = $this->request->getGet('account_id');
            $server_id = $this->request->getGet('server_id');

            $data = [
                "voucherPricePoint.id" => "27670",
                "voucherPricePoint.price" => "242535.0",
                "voucherPricePoint.variablePrice" => "0",
                "n" => "12/7/2022-2046",
                "email" => "",
                "userVariablePrice" => "0",
                "order.data.profile" => "eyJuYW1lIjoiICIsImRhdGVvZmJpcnRoIjoiIiwiaWRfbm8iOiIifQ==",
                "user.userId" => $account_id,
                "user.zoneId" => $server_id,
                "msisdn" => "",
                "voucherTypeName" => "MOBILE_LEGENDS",
                "shopLang" => "id_ID",
                "voucherTypeId" => "5",
                "gvtId" => "19",
                "checkoutId" => "",
                "affiliateTrackingId" => "",
                "impactClickId" => "",
                "anonymousId" => ""
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://order-sg.codashop.com/initPayment.action');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                echo 'Error: ' . curl_error($ch);
            } else {
                $responseArray = json_decode($response, true);
                if ($responseArray['success'] == true) {
                    $username = $responseArray['confirmationFields']['username'];
                    $dataArray = [
                        'username' => urldecode($username)
                    ];
                    return $this->response->setJSON($dataArray);
                } else {
                    return $this->response->setJSON([
                        'message' => 'Used ID atau Server ID salah !'
                    ]);
                }
            }

            curl_close($ch);
        }
    }

    public function usernameCheckApi()
    {
        // return $this->response->setJSON([
        //     'message' => 'Ngapain lu bro ?',
        // ]);
    }
}
