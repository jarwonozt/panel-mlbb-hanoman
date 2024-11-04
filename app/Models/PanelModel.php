<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class PanelModel extends Model
{
    protected $table            = 'panels';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['panel_name', 'panel_url', 'admin_panel', 'description', 'expired', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getInfoByAdmin($username)
    {
        return $this->where('admin_panel', $username)->first(); // Fetch panel info where panel_admin matches the username
    }
    
    public function getInfo()
    {
        $url = base_url();
        $wfind = $this->where('panel_url', $url)
            ->get()
            ->getFirstRow();
        if ($wfind) {
            $targetDate = new Time($wfind->expired);
            $currentDate = new Time();

            if ($currentDate > $targetDate) {
                $this->update($wfind->id, ['status' => false]);
                $result = 'Panel Expired';
            }else{
                $interval = $currentDate->diff($targetDate);
                $result = $this->formatInterval($interval);
            }
        }
        return $result ?? null;
    }

    private function formatInterval($interval)
    {
        $days = $interval->d;
        $hours = $interval->h;
        $minutes = $interval->i;

        return "{$days} Days, {$hours} Hours, {$minutes} Minutes";
    }
}
