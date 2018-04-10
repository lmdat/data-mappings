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
        'company_id',
        'typical_name'
    ];

    protected $guarded = [
        'id'
    ];

    public function dimensions(){
        return $this->hasMany('App\Models\Dimension', 'dim_type');
    }
}