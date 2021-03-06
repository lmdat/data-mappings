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
        'company_id',
        'status'
    ];

    protected $guarded = [
        'id'
    ];

    // public function dimensions(){
    //     return $this->belongsToMany('App\Models\Account', 'account_dimension', 'dim_id', 'account_id');
    // }

    public function dimension_type(){
        return $this->belongsTo('App\Models\DimensionType', 'dim_type');
    }

    public function company(){
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function topics(){
        return $this->belongsToMany('App\Model\Topic', 'topic_dimension', 'dim_code', 'topic_id', 'dim_code');
    }

    public function accounts(){
        return $this->belongsToMany('App\Model\Account', 'account_dimension', 'dim_code', 'account_code', 'dim_code', 'account_code');
    }
}