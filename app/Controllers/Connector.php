<?php

namespace App\Controllers;
use App\Models\KeysModel;
//use App\Controllers\Crypt\Crypt_AES;

//class Connector extends BaseController
class Connector
{
    
    protected $model, $game, $uKey, $sDev;

    public function __construct()
    {
        $this->model = new KeysModel();
        $this->maintenance = false;
        $this->staticRandw = "Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E";
        set_include_path(get_include_path() . PATH_SEPARATOR . APPPATH . 'Controllers/Crypt');
include(APPPATH . 'Controllers/Crypt/Crypt_AES.php');
        $aes_crypt = new Crypt_AES();
    }

    public function index()
    {
        if ($this->request->getPost()) {
            return $this->index_post();
        } else {
            return $this->index_post();
        }
    }

    public function index_post()
    {
        
        function tokenResponse($data){
            global $aes_crypt;
            $data = json_encode($data);
            $datahash = sha256($data);
            $new_iv = random_bytes(16);
            $aes_crypt->setIV($new_iv);
            $aes_crypt->setKey("-p2-7^fqQDH^#_uPP2Ssc@xHxRfdYvBt");
    
            $res_crypted = $aes_crypt->encrypt($data);
            $sign = openssl_digest($res_crypted, "sha256", true);
    
          return base64_encode($new_iv."|||".$sign."|||".$res_crypted);
        }
        
        $token = $_GET['token'];

        list($iv_block, $hash_result, $raw_text) = explode("|||", $token, 3);

        $iv_block = base64_decode($iv_block);
        $hash_result = base64_decode($hash_result);
        $raw_text = base64_decode($raw_text);

        $aes_crypt->setIV($iv_block);
        $aes_crypt->setKey("__&#d4eBMLWdLM7bRgS3YY@X7p+3P_8*");

        $new_sign = openssl_digest($raw_text, "sha256", true);
        
        if($hash_result != $new_sign) {
            $data = [
                'status' => false,
                'reason' => 'Unknown Error : -0x3'
            ];
        }
        
        $decdata_0 = $aes_crypt->decrypt($raw_text);
        $data = json_decode($decdata_0);
        
        $isMT = $this->maintenance;

        $game = $data["game"];
        $uKey = $data["user_key"];
        $sDev = $data["serial"];
        $uCode = $data["unique"];
        
        $verify_data = openssl_digest($uCode, "sha512", true);
        
        if ($isMT) {
            $data = [
                'status' => false,
                'reason' => 'MAINTENANCE'
            ];
        } else {
            if (!$game or !$uKey or !$sDev or !$uCode) {
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
                            $real = "$game-$uKey-$sDev-$this->staticRandw";
                            
                            $data = [
                                'status' => true,
                                'data' => [
                                    'real' => $real,
                                    'token' => md5($real),
                                    'rng' => $time->getTimestamp(),
                                    'hash' => $verify_data
                                
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
        //return $this->response->setJSON(base64_encode(xorthis($data)));
        //echo (base64_encode(xorthis(json_encode($data))));
        echo (tokenResponse($data));
    }
}
