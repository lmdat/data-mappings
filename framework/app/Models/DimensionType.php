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
        'type_code'
    ];

    protected $guarded = [
        'id'
    ];

    public function items(){
        return $this->hasMany('App\Models\MappingsItem', 'type_id');
    }
}