<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountDimension extends Model{

    protected $table = 'account_dimension';
    public $timestamps = false;
    //public $incrementing = false;

    protected $fillable = [
        'account_id',
        'dim_id',
        'based_amount',
        'account_period',
        'year',
        'month',
        'quarter_number',
        'extra_attr'
    ];

    protected $guarded = [
        'id'
    ];

}