<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'user';
    public $timestamps = false;
    //public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'salt_key',
        'status',
        'created_by',
        'role_id'
        
    ];
    
    protected $guarded = [
        'id'
    ];
    
  
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function companies(){
        return $this->belongsToMany('App\Models\Company', 'user_company', 'user_id', 'company_id');
    }

    public function roles(){
        return $this->belongsToMany('App\Models\Role', 'user_role', 'user_id', 'role_id');
    }

    public function maxRole(){
        return $this->roles()->max('power');
    }

 
    public function roleId($max=0){
        if($max == 0)
            $max = $this->maxRole();

        $role = $this->roles()->where('power', $max)->first();
        
        if($role != null)
            return $role->id;

        return false;
    }

    public function getMaxRoleAlias($max=0){
        if($max == 0)
            $max = $this->maxRole();

        $role = $this->roles()->where('power', $max)->first();

        if($role != null)
            return $role->alias;

        return false;
    }

    public function getMaxRoleName($max=0){
        if($max == 0)
            $max = $this->maxRole();

        $role = $this->roles()->where('power', $max)->first();

        if($role != null)
            return $role->role_name;

        return false;
    }

    public function hasRole($pows){
        $my_role = intval($this->maxRole());
        if($my_role == 9999)
            return true;

        $passed = true;
        if(is_array($pows)){
            if(!in_array($my_role, $pows)){
                foreach($pows as $p){
                    if(intval($p) > $my_role){
                        $passed = false;
                        break;
                    }
                }
            }
        }
        else{
            if(intval($pows) > $my_role)
                $passed = false;
        }

        return $passed;
    }

    

}
