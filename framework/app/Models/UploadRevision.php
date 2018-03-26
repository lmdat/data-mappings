<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadRevision extends Model{

    protected $table = 'upload_revision';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id',
        'upload_id',
        'company_id',
        'revision_number',
        'file_path',
        'created_at',
        'status'
    ];

    protected $guarded = [
        
    ];

    public function upload(){
        return $this->belongsTo('App\Models\UploadLedger', 'upload_id');
    }

    public function ledgers(){
        return $this->hasMany('App\Models\Ledger', 'revision');
    }

}