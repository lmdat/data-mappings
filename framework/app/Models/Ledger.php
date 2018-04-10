<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model{

    protected $table = 'ledger';
    public $timestamps = false;
    //public $incrementing = false;

    protected $fillable = [
        'company_id',
        'account_code',
        'ledger_key',
        'ledger_code',
        'base_amount',
        'accounting_period',
        'year',
        'month',
        'quarter_number',
        'extra_attr',
        'dim_code_list',
        'dim_type_order',
        'created_at',
        'revision'
    ];

    protected $guarded = [
        'id'
    ];

    public function topics(){
        return $this->belongsToMany('App\Models\Topic', 'ledger_topic', 'ledger_code', 'topic_code', 'ledger_key');
    }

    public function upload_revision(){
        return $this->belongsTo('App\Models\UploadRevision', 'revision');
    }

}