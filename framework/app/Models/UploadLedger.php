<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadLedger extends Model{

    protected $table = 'upload_ledger';
    public $timestamps = false;
    //public $incrementing = false;

    protected $fillable = [
        'company_id',
        'upload_title',
        'salt_key',
        'created_at',
        'status'
    ];

    protected $guarded = [
        'id'
    ];

    public function upload_revisions(){
        return $this->hasMany('App\Models\UploadRevision', 'upload_id');
    }

}