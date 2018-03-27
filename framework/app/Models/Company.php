<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model{

    protected $table = 'company';
    public $timestamps = false;
    //public $incrementing = false;

    protected $fillable = [
        'company_name',
        'short_name',
        'phone',
        'mobile',
        'status'
    ];

    protected $guarded = [
        'id'
    ];

    public function accounts(){
        return $this->hasMany('App\Models\Account', 'company_id');
    }

    public function items(){
        return $this->hasMany('App\Models\MappingsItem', 'company_id');
    }

    public function dimensions(){
        return $this->hasMany('App\Models\Dimension', 'company_id');
    }

    public function ledgers(){
        return $this->hasMany('App\Models\Ledger', 'company_id');
    }

    
    public function users(){
        return $this->belongsToMany('App\Models\User', 'user_company', 'company_id', 'user_id');
    }

}