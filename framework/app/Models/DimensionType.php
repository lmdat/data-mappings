<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DimensionType extends Model{

    protected $table = 'dimension_type';
    public $timestamps = false;
    //public $incrementing = false;

    protected $fillable = [
        'type_name',
        'alias',
        'type_code',
        'company_id'
    ];

    protected $guarded = [
        'id'
    ];

    public function dimensions(){
        return $this->hasMany('App\Models\Dimension', 'dim_type');
    }
}