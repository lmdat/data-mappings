<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MappingsType extends Model{

    protected $table = 'mappings_type';
    public $timestamps = false;
    //public $incrementing = false;

    protected $fillable = [
        'type_name',
        'short_code'
    ];

    protected $guarded = [
        'id'
    ];

    public function items(){
        return $this->hasMany('App\Models\MappingsItem', 'type_id');
    }
}