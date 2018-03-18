<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountData extends Model{

    protected $table = 'account_data';
    public $timestamps = false;
    //public $incrementing = false;

    protected $fillable = [
        'account_name',
        'account_code',
        'status'
    ];

    protected $guarded = [
        'id'
    ];

    public function dimensions(){
        return $this->belongsToMany('App\Models\Dimension', 'account_dimension', 'account_id', 'dim_id');
    }

    public function items(){
        return $this->belongsToMany('App\Models\MappingsItem', 'account_item', 'account_code', 'mapping_code', 'account_code');
    }
}