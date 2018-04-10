<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopicType extends Model{

    protected $table = 'topic_type';
    public $timestamps = false;
    //public $incrementing = false;

    protected $fillable = [
        'type_name',
        'company_id',
        'status'
    ];

    protected $guarded = [
        'id'
    ];

    public function topics(){
        return $this->hasMany('App\Models\Topic', 'type_id');
    }
}