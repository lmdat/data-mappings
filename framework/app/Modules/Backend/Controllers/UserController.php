<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Http\Request;
use App\Libs\Utils\Vii;

use App\Models\User;
use App\Models\Company;
use App\Models\Role;

use App\Modules\Backend\Requests\User\UserCreateRequest;
use App\Modules\Backend\Requests\User\UserEditRequest;

class UserController extends Controller{
    const LANG_NAME = 'user';

    public function __construct(){
        parent::__construct();

        view()->share('lang_mod', $this->mod . '/' . self::LANG_NAME);

        $actions = request()->route()->getAction();
        $this->prefixUrl = $actions['prefix'];

    }

    public function getUser(Request $request, $id=null){
        $display_rows = $request->input('rows_per_page', 15);

        $aqs = $request->except('page'); 
        // unset($aqs['page']);
        $paging_qs = Vii::queryStringBuilder($aqs);

        $role = Role::where('power', 9999)->first();
        
        $entries = User::join('user_role', 'user.id', '=', 'user_role.user_id')
            ->where('user_role.role_id', '!=', $role->id)
            ->where('user.id', '!=', $request->user($this->guardName)->id)
            ->select('user.*')
            ->orderBy('first_name', 'ASC')
            ->paginate($display_rows);
        
        $entries->withPath(route('user', [str_replace('?', '', $paging_qs)]));

        // $company_entries = Company::where('status', 1)
        //     ->orderBy('company_name')
        //     ->select('*')
        //     ->get();

        $user_power = $request->user()->maxRole();
        $alias = $request->user($this->guardName)->getMaxRoleAlias($user_power);
        // dd($power);

        // $operator = '<';
        // if($alias == 'SA' || $alias == 'A')
        //     $operator .= '=';

        $role_entries = Role::where('power', '!=', 9999)
            // ->where('power', $operator, $user_power)
            ->orderBy('power', 'DESC')->select('*')->get();
        
        $k = 0;
        $disabled_roles = [];
        foreach($role_entries as &$role){
            if(!($alias == 'SA' || $alias == 'A') && $role->power >= $user_power)
                $disabled_roles[] = $role->power;

            $s = '';
            $i = 0;
            while($i < $k){
                $s .= '---';
                $i++;
            }
                
            $role->role_name = $s . ' ' . $role->role_name;
            $k++;
        }
        
        if($id != null){
            $user = User::findOrFail($id);

            return view(
                'Backend::user.edit-user',
                [
                    'form_uri' => route('user-put-edit'),
                    'page_title' => 'Edit User',
                    'entries' => $entries,
                    'qs' => Vii::queryStringBuilder($request->getQueryString()),
                    'user' => $user,
                    'roles' => $role_entries,
                    'disabled_roles' => $disabled_roles,
                    //'type_list' => Vii::createOptionData($mappings_type->toArray(), 'id', ['type_name', 'short_code']),
                    
                    //'user' => session()->get('test-name', $full_name)
                ]
            );
        }
        
        return view(
            'Backend::user.add-user',
            [
                'form_uri' => route('user-post-create'),
                'page_title' => 'Create User',
                'entries' => $entries,
                'qs' => Vii::queryStringBuilder($request->getQueryString()),
                // 'companies' => Vii::createOptionData($company_entries->toArray(), 'id', ['company_name']),
                'roles' => $role_entries,
                'disabled_roles' => $disabled_roles,
                //'type_list' => Vii::createOptionData($mappings_type->toArray(), 'id', ['type_name', 'short_code']),
                
                //'user' => session()->get('test-name', $full_name)
            ]
        );
    }

    public function getAssignCompany(Request $request, $id=null){
        $display_rows = $request->input('rows_per_page', 15);

        $aqs = $request->except('page'); 
        // unset($aqs['page']);
        $paging_qs = Vii::queryStringBuilder($aqs);

        $role = Role::where('power', 9999)->first();
        
        $entries = User::join('user_role', 'user.id', '=', 'user_role.user_id')
            ->where('user_role.role_id', '!=', $role->id)
            ->where('user.id', '!=', $request->user($this->guardName)->id)
            ->select('user.*')
            ->orderBy('first_name', 'ASC')
            ->paginate($display_rows);
        
        $entries->withPath(route('user', [str_replace('?', '', $paging_qs)]));
        
        $user_power = $request->user()->maxRole();
        $alias = $request->user($this->guardName)->getMaxRoleAlias($user_power);
        // dd($power);

        // $operator = '<';
        // if($alias == 'SA' || $alias == 'A')
        //     $operator .= '=';

        $role_entries = Role::where('power', '!=', 9999)
            // ->where('power', $operator, $user_power)
            ->orderBy('power', 'DESC')->select('*')->get();
        
        $k = 0;
        $disabled_roles = [];
        foreach($role_entries as &$role){
            if(!($alias == 'SA' || $alias == 'A') && $role->power >= $user_power)
                $disabled_roles[] = $role->power;

            $s = '';
            $i = 0;
            while($i < $k){
                $s .= '---';
                $i++;
            }
                
            $role->role_name = $s . ' ' . $role->role_name;
            $k++;
        }

        $company_entries = Company::where('status', 1)
            ->orderBy('company_name')
            ->select('*')
            ->get();
        
        $user = User::findOrFail($id);
       
        
        return view(
            'Backend::user.assign-company',
            [
                'form_uri' => route('user-post-assign'),
                'page_title' => 'Assign Company for User',
                'entries' => $entries,
                'qs' => Vii::queryStringBuilder($request->getQueryString()),
                'companies' => Vii::createOptionData($company_entries->toArray(), 'id', ['company_name'], false),
                'user' => $user

                //'type_list' => Vii::createOptionData($mappings_type->toArray(), 'id', ['type_name', 'short_code']),
                
                //'user' => session()->get('test-name', $full_name)
            ]
        );
    }

    public function postCreateUser(UserCreateRequest $request){
        $qs = Vii::queryStringBuilder($request->getQueryString());

        $_form = $request->only(['first_name', 'last_name', 'email', 'password', 'role_id']);

        // dd($_form);

        $data = [
            'first_name' => trim($_form['first_name']),
            'last_name' => trim($_form['last_name']),
            'password' => bcrypt($_form['password']), // \Hash::make($_form['password']),
            'email' => $_form['email'],
            'salt_key' => md5($_form['email'] . time()),
            'created_by' => $request->user($this->guardName)->id,
            'status' => 1
        ];

        // dd($data);

        $model = new User($data);

        if($model->save()){
            $model->roles()->attach(intval($_form['role_id']));
            return redirect()
                ->route('user', [str_replace('?', '', $qs)])
                ->with('success-message', "1 user is created.");
        }

        return redirect()
                ->route('user', [str_replace('?', '', $qs)])
                ->with('error-message', "Cannot create new user.");
    }

    public function putEditUser(UserEditRequest $request, $id=null){
        $qs = Vii::queryStringBuilder($request->getQueryString());

        $_form = $request->only(['id', 'first_name', 'last_name', 'email', 'password']);

        $data = [
            'first_name' => trim($_form['first_name']),
            'last_name' => trim($_form['last_name']),
            'email' => $_form['email']
        ];

        if($_form['password'] != '')
            $data['password'] =  bcrypt($_form['password']);

        $model = User::findOrFail($_form['id']);
        // dd($model->toArray());

        if($model->update($data)){ //$model->update($data);
            $rid = $request->post('role_id', null);
            
            $model->roles()->detach();
            if($rid != null)
                $model->roles()->attach(intval($rid));

            return redirect()
                ->route('user', [str_replace('?', '', $qs)])
                ->with('success-message', "User is updated.");
        }

        return redirect()
                ->route('user', [str_replace('?', '', $qs)])
                ->with('error-message', "Cannot update user.");
    }

    public function getChangeStatus(Request $request, $id=null){
        $user = User::findOrFail($id);
        $val = 1 - $user->status;
        $user->update(['status'=> $val]);

        $qs = Vii::queryStringBuilder($request->getQueryString());
        return redirect()
                ->route('user', [str_replace('?', '', $qs)]);
    }

    public function postAssignCompany(Request $request, $id=null){
        $user_id = $request->post('id');
        $coms = $request->post('company_id', []);
        // dd($request->all());
        
        $user = User::findOrFail($user_id);
        $user->companies()->detach();
        if(count($coms) > 0){
            $user->companies()->attach($coms);
        }
            
        
        $qs = Vii::queryStringBuilder($request->getQueryString());
        return redirect()
                ->route('user', [str_replace('?', '', $qs)]);
    }

    
}