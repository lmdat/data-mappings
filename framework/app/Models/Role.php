<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model{
    
    protected $table = 'role';
    public $timestamps = false;
    
    protected $fillable = [
        'role_name',
        'alias',
        'power'
    ];
    
    protected $guarded = ['id'];
    
    
    public function users(){
        return $this->belongsToMany('App\Models\User', 'user_role', 'role_id', 'user_id');
    }

   
    
}