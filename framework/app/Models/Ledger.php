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
        'base_amount',
        'accounting_period',
        'year',
        'month',
        'quarter_number',
        'extra_attr',
        'dim_code_list',
        'dim_type_order',
        'created_at'
    ];

    protected $guarded = [
        'id'
    ];

    public function items(){
        return $this->belongsToMany('App\Models\MappingsItem', 'ledger_item', 'ledger_code', 'mapping_code', 'ledger_code');
    }

}