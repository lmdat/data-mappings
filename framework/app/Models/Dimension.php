<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dimension extends Model{

    protected $table = 'dimension';
    public $timestamps = false;
    //public $incrementing = false;

    protected $fillable = [
        'dim_name',
        'dim_code',
        'dim_type',
        'description',
        'status'
    ];

    protected $guarded = [
        'id'
    ];

    public function dimensions(){
        return $this->belongsToMany('App\Models\AccountData', 'account_dimension', 'dim_id', 'account_id');
    }
}