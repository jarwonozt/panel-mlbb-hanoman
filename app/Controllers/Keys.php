<?php

namespace App\Controllers;

use App\Models\HistoryModel;
use App\Models\KeysModel;
use App\Models\PanelModel;
use App\Models\UserModel;
use Config\Services;

class Keys extends BaseController
{
    protected $userModel, $model, $user, $time, $game_list, $duration, $trial_duration, $price;
    public $panelModel, $panel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->user = $this->userModel->getUser();
        $this->model = new KeysModel();
        $this->time = new \CodeIgniter\I18n\Time;

        $this->panelModel = new PanelModel();
        $this->panel = $this->panelModel->getInfo();
        // dd($this->panel);

        /* ------- Game ------- */
        $this->game_list = [
            'MLBB' => 'Mobile Legends'
        ];

        $this->duration = [
            1 => '1 Days &mdash; Rp 5000/Device',
            3 => '3 Days &mdash; Rp 15000/Device',
            7 => '7 Days &mdash; Rp 30000/Device',
            //14 => '14 Days &mdash; Rp 30000/Device',
            30 => '30 Days &mdash; Rp 60000/Device',
            //60 => '60 Days &mdash; Rp 100000/Device',
            //1825 => '1825 Days &mdash; $60/Device',
        ];

        $this->price = [
            1 => 5000,
            3 => 15000,
            7 => 30000,
            //14 => 30000,
            30 => 60000,
            //60 => 100000,
            //1825 => 60,
        ];

        // Trial Duration
        $this->trial_duration = [
            '180' => '3 Hours',
            '300' => '5 Hours',
            '600' => '10 Hours',
            '900' => '15 Hours',
        ];
    }

    public function index()
    {
        $model = $this->model;
        $user = $this->user;

        if ($user->level != 1) {
            $keys = $model->where('registrator', $user->username)
                ->findAll();
        } else {
            $keys = $model->findAll();
        }

        $data = [
            'title' => 'Keys',
            'user' => $user,
            'keylist' => $keys,
            'time' => $this->time,
            'panel' => $this->panel
        ];
        return view('Keys/list', $data);
    }

    public function api_get_keys()
    {
        // ? API for DataTable Keys
        $model = $this->model;
        return $model->API_getKeys();
    }

    public function api_key_reset()
    {
        $user = $this->user;

        sleep(1);
        $model = $this->model;
        $keys = $this->request->getGet('userkey');
        $reset = $this->request->getGet('reset');
        $db_key = $model->getKeys($keys);

        $rules = [];
        if ($db_key) {
            $total = $db_key->devices ? explode(',', $db_key->devices) : [];
            $rules = ['devices_total' => count($total), 'devices_max' => (int) $db_key->max_devices];
            $user = $this->user;
            if ($db_key->devices and $reset) {
                if ($user->level == 1/* or $db_key->registrator == $user->username*/) {
                    $model->set('devices', NULL)
                        ->where('user_key', $keys)
                        ->update();
                    $rules = ['reset' => true, 'devices_total' => 0, 'devices_max' => $db_key->max_devices];
                }
            } else {
            }
        }

        $data = [
            'registered' => $db_key ? true : false,
            'keys' => $keys,
            'isAdmin' => $user->level,
        ];

        $real_response = array_merge($data, $rules);
        return $this->response->setJSON($real_response);
    }

    public function api_key_delete()
    {
        sleep(1);
        $model = $this->model;
        $keys = $this->request->getGet('userkey');
        $delete = $this->request->getGet('delete');
        $db_key = $model->getKeys($keys);

        $rules = [];
        if ($db_key) {
            /*$total = $db_key->devices ? explode(',', $db_key->devices) : [];
            $rules = ['devices_total' => count($total), 'devices_max' => (int) $db_key->max_devices];*/
            $user = $this->user;
            if ($db_key->user_key and $delete) {
                if ($user->level == 1 or $db_key->registrator == $user->username) {
                    $model->where('user_key', $keys)
                        ->delete();
                    $rules = ['delete' => true];
                }
            } else {
            }
        }

        $data = [
            'registered' => $db_key ? true : false,
            'keys' => $keys,
        ];

        $real_response = array_merge($data, $rules);
        return $this->response->setJSON($real_response);
    }

    public function edit_key($key = false)
    {
        if ($this->request->getPost()) return $this->edit_key_action();
        $msgDanger = "The user key no longer exists.";
        if ($key) {
            $dKey = $this->model->getKeys($key, 'id_keys');
            $user = $this->user;
            if ($dKey) {
                if ($user->level == 1 or $dKey->registrator == $user->username) {
                    $validation = Services::validation();
                    $data = [
                        'title' => 'Key',
                        'user' => $user,
                        'key' => $dKey,
                        'game_list' => $this->game_list,
                        'time' => $this->time,
                        'key_info' => getDevice($dKey->devices),
                        'messages' => setMessage('Please carefuly edit information'),
                        'panel' => $this->panel,
                        'validation' => $validation,
                    ];
                    return view('Keys/key_edit', $data);
                } else {
                    $msgDanger = "Restricted to this user key.";
                }
            }
        }
        return redirect()->to('keys')->with('msgDanger', $msgDanger);
    }

    private function edit_key_action()
    {
        $keys = $this->request->getPost('id_keys');
        $user = $this->user;
        $dKey = $this->model->getKeys($keys, 'id_keys');
        $game = implode(",", array_keys($this->game_list));

        if (!$dKey) {
            $msgDanger = "The user key no longer exists~";
        } else {
            if ($user->level == 1 or $dKey->registrator == $user->username) {
                $form_reseller = [
                    'status' => [
                        'label' => 'status',
                        'rules' => 'required|integer|in_list[0,1]',
                        'erros' => [
                            'integer' => 'Invalid {field}.',
                            'in_list' => 'Choose between list.'
                        ]
                    ]
                ];
                $form_admin = [
                    'id_keys' => [
                        'label' => 'keys',
                        'rules' => 'required|is_not_unique[keys_code.id_keys]|numeric',
                        'errors' => [
                            'is_not_unique' => 'Invalid keys.'
                        ],
                    ],
                    'game' => [
                        'label' => 'Games',
                        'rules' => "required|alpha_numeric_space|in_list[$game]",
                        'errors' => [
                            'alpha_numeric_space' => 'Invalid characters.'
                        ],
                    ],
                    'user_key' => [
                        'label' => 'User keys',
                        'rules' => "required|is_unique[keys_code.user_key,user_key,$dKey->user_key]|alpha_numeric",
                        'errors' => [
                            'is_unique' => '{field} has been taken.'
                        ],
                    ],
                    'duration' => [
                        'label' => 'duration',
                        'rules' => 'required|numeric|greater_than_equal_to[1]',
                        'errors' => [
                            'greater_than_equal_to' => 'Minimum {field} is invalid.',
                            'numeric' => 'Invalid day {field}.'
                        ]
                    ],
                    'max_devices' => [
                        'label' => 'devices',
                        'rules' => 'required|numeric|greater_than_equal_to[1]',
                        'errors' => [
                            'greater_than_equal_to' => 'Minimum {field} is invalid.',
                            'numeric' => 'Invalid max of {field}.'
                        ]
                    ],
                    'registrator' => [
                        'label' => 'registrator',
                        'rules' => 'permit_empty|alpha_numeric_space|min_length[4]'
                    ],
                    'expired_date' => [
                        'label' => 'expired',
                        'rules' => 'permit_empty|valid_date[Y-m-d H:i:s]',
                        'errors' => [
                            'valid_date' => 'Invalid {field} date.',
                        ]
                    ],
                    'devices' => [
                        'label' => 'device list',
                        'rules' => 'permit_empty'
                    ]
                ];

                if ($user->level == 1) {
                    // Admin full rules.
                    $form_rules = array_merge($form_reseller, $form_admin);
                    $devices = $this->request->getPost('devices');
                    $max_devices = $this->request->getPost('max_devices');

                    $data_saves = [
                        'game' => $this->request->getPost('game'),
                        'user_key' => $this->request->getPost('user_key'),
                        'duration' => $this->request->getPost('duration'),
                        'max_devices' => $max_devices,
                        'status' => $this->request->getPost('status'),
                        'registrator' => $this->request->getPost('registrator'),
                        'expired_date' => $this->request->getPost('expired_date') ?: NULL,
                        'devices' => setDevice($devices, $max_devices),
                    ];
                } else {
                    // Reseller just status rules, you can set manually later.
                    $form_rules = $form_reseller;
                    $data_saves = ['status' => $this->request->getPost('status')];
                }

                if (!$this->validate($form_rules)) {
                    return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the error');
                } else {
                    // * Data Updates
                    $this->model->update($dKey->id_keys, $data_saves);
                    return redirect()->back()->with('msgSuccess', 'User key successfuly updated!');
                }
            } else {
                $msgDanger = "Restricted to this user key~";
            }
        }
        return redirect()->to('keys')->with('msgDanger', $msgDanger);
    }

    public function generate()
    {
        if ($this->request->getPost())
            return $this->generate_action();

        $user = $this->user;
        $validation = Services::validation();

        $message = setMessage("<i class='bi bi-wallet'></i> Total Balance Rp " . number_format($user->saldo));
        if ($user->saldo <= 0) {
            $message = setMessage("Please top up to your beloved admin.", 'warning');
        }

        $data = [
            'title' => 'Generate',
            'user' => $user,
            'time' => $this->time,
            'game' => $this->game_list,
            'duration' => $this->duration,
            'price' => json_encode($this->price),
            'messages' => $message,
            'panel' => $this->panel,
            'validation' => $validation,
        ];
        return view('Keys/generate', $data);
    }

    private function generate_action()
    {
        $user = $this->user;
        $game = $this->request->getPost('game');
        $maxd = $this->request->getPost('max_devices');
        $drtn = $this->request->getPost('duration');
        $bulkkey = $this->request->getPost('bulk_key');
        $getPrice = $this->request->getPost('estimation');

        $game_list = implode(",", array_keys($this->game_list));
        $form_rules = [
            'game' => [
                'label' => 'Games',
                'rules' => "required|alpha_numeric_space|in_list[$game_list]",
                'errors' => [
                    'alpha_numeric_space' => 'Invalid characters.'
                ],
            ],
            'duration' => [
                'label' => 'duration',
                'rules' => 'required|numeric|greater_than_equal_to[1]',
                'errors' => [
                    'greater_than_equal_to' => 'Minimum {field} is invalid.',
                    'numeric' => 'Invalid day {field}.'
                ]
            ],
            'max_devices' => [
                'label' => 'devices',
                'rules' => 'required|numeric|greater_than_equal_to[1]',
                'errors' => [
                    'greater_than_equal_to' => 'Minimum {field} is invalid.',
                    'numeric' => 'Invalid max of {field}.'
                ]
            ],
        ];

        $validation = Services::validation();
        $reduceCheck = ($user->saldo - $getPrice);
        // dd($reduceCheck);
        if ($reduceCheck < 0) {
            $validation->setError('duration', 'Insufficient balance');
            return redirect()->back()->withInput()->with('msgWarning', 'Please top up to your beloved admin.');
        } else {
            if (!$this->validate($form_rules)) {
                return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the error');
            } else {
                $msg = "Successfuly Generated.";
                $data = '';
                $datacp = '';

                for ($i = 0; $i < $bulkkey; $i++) {
                    $license = random_string('alnum', 16);
                    $passlicense = random_string('alnum', 10);
                    $data_response = [
                        'game' => $game,
                        'user_key' => $license,
                        'user_pass' => $passlicense,
                        'duration' => $drtn,
                        'max_devices' => $maxd,
                        'registrator' => $user->username,
                    ];
                    $data .= $license . "\n";
                    $idKeys = $this->model->insert($data_response);
                }
                // * reseller reduce saldo
                $this->userModel->update(session('userid'), ['saldo' => $reduceCheck]);

                $history = new HistoryModel();
                $history->insert([
                    'keys_id' => $idKeys,
                    'user_do' => $user->username,
                    'info' => "$game|" . substr($license, 0, 5) . "|$drtn|$maxd"
                ]);

                $other_response = [
                    'fees' => $getPrice,
                    'gendata' => $data,
                ];

                session()->setFlashdata(array_merge($data_response, $other_response));
                return redirect()->back()->with('msgSuccess', $msg);
            }
        }
    }

    // generate trial key
    public function generate_trial_key()
    {
        if ($this->request->getPost())
            return $this->generate_trial_key_action();

        $user = $this->user;
        $validation = Services::validation();

        $message = setMessage("<i class='bi bi-wallet'></i> Total Balance Rp " . number_format($user->saldo));
        if ($user->saldo <= 0) {
            $message = setMessage("Please top up to your beloved admin.", 'warning');
        }

        $data = [
            'title' => 'Generate',
            'user' => $user,
            'time' => $this->time,
            'game' => $this->game_list,
            'duration' => $this->trial_duration,
            'price' => json_encode($this->price),
            'messages' => $message,
            'panel' => $this->panel,
            'validation' => $validation,
        ];
        return view('Keys/trial_generate', $data);
    }

    private function generate_trial_key_action()
    {
        $user = $this->user;
        $game = $this->request->getPost('game');
        $maxd = $this->request->getPost('max_devices');
        $drtn = $this->request->getPost('duration');
        $bulkkey = 1;
        $getPrice = 0;

        $game_list = implode(",", array_keys($this->game_list));
        $form_rules = [
            'game' => [
                'label' => 'Games',
                'rules' => "required|alpha_numeric_space|in_list[$game_list]",
                'errors' => [
                    'alpha_numeric_space' => 'Invalid characters.'
                ],
            ],
            'duration' => [
                'label' => 'duration',
                'rules' => 'required|numeric|greater_than_equal_to[1]',
                'errors' => [
                    'greater_than_equal_to' => 'Minimum {field} is invalid.',
                    'numeric' => 'Invalid day {field}.'
                ]
            ],
            'max_devices' => [
                'label' => 'devices',
                'rules' => 'required|numeric|greater_than_equal_to[1]',
                'errors' => [
                    'greater_than_equal_to' => 'Minimum {field} is invalid.',
                    'numeric' => 'Invalid max of {field}.'
                ]
            ],
        ];

        $validation = Services::validation();
        $reduceCheck = ($user->saldo - $getPrice);
        // dd($reduceCheck);
        if ($reduceCheck < 0) {
            $validation->setError('duration', 'Insufficient balance');
            return redirect()->back()->withInput()->with('msgWarning', 'Please top up to your beloved admin.');
        } else {
            if (!$this->validate($form_rules)) {
                return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the error');
            } else {
                $msg = "Successfuly Generated.";
                $data = '';
                $datacp = '';

                for ($i = 0; $i < $bulkkey; $i++) {
                    $license = 'TRIAL-' . random_string('alnum', 10);
                    $passlicense = 'TRIAL-' . random_string('alnum', 10);
                    $data_response = [
                        'game' => $game,
                        'user_key' => $license,
                        'user_pass' => $passlicense,
                        'duration' => $drtn,
                        'max_devices' => $maxd,
                        'registrator' => $user->username,
                    ];
                    $data .= $license . "\n";
                    $idKeys = $this->model->insert($data_response);
                }
                // * reseller reduce saldo
                $this->userModel->update(session('userid'), ['saldo' => $reduceCheck]);

                $history = new HistoryModel();
                $history->insert([
                    'keys_id' => $idKeys,
                    'user_do' => $user->username,
                    'info' => "$game|" . substr($license, 0, 5) . "|$drtn|$maxd"
                ]);

                $other_response = [
                    'fees' => $getPrice,
                    'gendata' => $data,
                ];

                session()->setFlashdata(array_merge($data_response, $other_response));
                return redirect()->back()->with('msgSuccess', $msg);
            }
        }
    }

    public function compe_all_key()
    {
        if ($this->request->isAJAX()) {
            $csrfToken = $this->request->getPost(csrf_token());

            $duration = (int) $this->request->getPost('duration');

            $builder = $this->model;
            $timeee = $this->time;
            $used = $builder->where('expired_date !=', NULL)->countAllResults();


            for ($i = 0; $i < $used; $i++) {
                $used2 = $builder->getWhere('expired_date !=', NULL)->getRowArray($i);
                $value = $used2['expired_date'];
                $setexp = $value ? $timeee::parse($value)->addHours($duration)->toLocalizedString('YYYY-MM-dd HH:mm:ss') : '';
                $used3 = $builder->where('expired_date', $value);
                $used3->set('expired_date',  $setexp)->update();
            }


            $rules = [];

            $data = [
                'total' => $used,
                'status' => 'success',
                'message' => 'Competation ' . $duration . ' hours FOR ALL MEMBER! | TOTAL : ' . $used
            ];

            $real_response = array_merge($data, $rules);
            return $this->response->setJSON($real_response);
        }
    }

    public function del_exp_key()
    {
        sleep(1);

        $timeee = $this->time;
        $nowww = $timeee::now()->toLocalizedString('Y-M-d H:m:s');
        $connect = db_connect();
        $builder = $connect->table('keys_code');
        $exp = $builder->where('expired_date <', $nowww);

        $rules = [];
        $ppp = $exp->countAllResults();
        if ($ppp != 0) {
            $data = [
                'totaldel' => $ppp,
                'registered' => true
            ];
            $exp2 = $builder->where('expired_date <', $nowww);
            $exp2->delete();
            $rules = ['deleteallexp' => true];
            $real_response = array_merge($data, $rules);

            return $this->response->setJSON($real_response);
        } else {
            $data = [
                'totaldel' => 0,
                'registered' => false
            ];
            $real_response = array_merge($data, $rules);
            return $this->response->setJSON($real_response);
        }
    }


    public function del_null_key()
    {
        sleep(1);

        $connect = db_connect();
        $builder = $connect->table('keys_code');
        $exp = $builder->where('expired_date', NULL);

        $rules = [];
        $ppp = $exp->countAllResults();
        if ($ppp != 0) {
            $data = [
                'totaldel' => $ppp,
                'registered' => true
            ];
            $exp2 = $builder->where('expired_date', NULL);
            $exp2->delete();
            $rules = ['deleteallnull' => true];
            $real_response = array_merge($data, $rules);

            return $this->response->setJSON($real_response);
        } else {
            $data = [
                'totaldel' => 0,
                'registered' => false
            ];
            $real_response = array_merge($data, $rules);
            return $this->response->setJSON($real_response);
        }
    }

    public function reset_all_key()
    {
        sleep(1);

        $connect = db_connect();
        $builder = $connect->table('keys_code');

        $user = $this->user;
        $rules = [];
        if ($user->level == 1) {
            $used = $builder->where('devices !=', NULL);
            $ppp = $used->countAllResults();
            if ($ppp != 0) {
                $data = [
                    'totaldel' => $ppp,
                    'registered' => true
                ];
                $used2 = $builder->where('devices !=', NULL);
                $used2->set('devices', NULL)->update();
                $rules = ['resetallkey' => true];
                $real_response = array_merge($data, $rules);

                return $this->response->setJSON($real_response);
            } else {
                $data = [
                    'totaldel' => 0,
                    'registered' => false
                ];
                $real_response = array_merge($data, $rules);
                return $this->response->setJSON($real_response);
            }
        } else {



            $used = $builder->where('devices !=', NULL);
            $used->where('registrator', $user->username);
            $ppp = $used->countAllResults();
            if ($ppp != 0) {
                $data = [
                    'totaldel' => $ppp,
                    'registered' => true
                ];
                $used2 = $builder->where('devices !=', NULL);
                $used2->where('registrator', $user->username);
                $used2->set('devices', NULL)->update();
                $rules = ['resetallkey' => true];
                $real_response = array_merge($data, $rules);

                return $this->response->setJSON($real_response);
            } else {
                $data = [
                    'totaldel' => 0,
                    'registered' => false
                ];
                $real_response = array_merge($data, $rules);
                return $this->response->setJSON($real_response);
            }
        }
    }
}
