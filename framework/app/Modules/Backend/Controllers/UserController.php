<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Http\Request;
use App\Libs\Utils\Vii;

use App\Models\User;
use App\Models\Company;
use App\Models\Role;

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

        $entries = User::select('*')
            ->orderBy('first_name', 'ASC')
            ->paginate($display_rows);

        $company_entries = Company::where('status', 1)
            ->orderBy('company_name')
            ->select('*')
            ->get();

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
        // dd($disabled_roles);
        
        return view(
            'Backend::user.add-user',
            [
                'form_uri' => ($id == null) ? route('user-post-create') : route('user-put-edit', [$id]),
                'page_title' => 'Create User',
                'entries' => $entries,
                'qs' => Vii::queryStringBuilder($request->getQueryString()),
                'companies' => Vii::createOptionData($company_entries->toArray(), 'id', ['company_name']),
                'roles' => $role_entries,
                'disabled_roles' => $disabled_roles,
                //'type_list' => Vii::createOptionData($mappings_type->toArray(), 'id', ['type_name', 'short_code']),
                
                //'user' => session()->get('test-name', $full_name)
            ]
        );
    }

    public function postCreateUser(Request $request){
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

    public function putEditUser(Request $request, $d=null){
        $qs = Vii::queryStringBuilder($request->getQueryString());

        $_form = $request->only(['id', 'first_name', 'last_name', 'email', 'password', 'role_id']);

        $data = [
            'first_name' => trim($_form['first_name']),
            'last_name' => trim($_form['last_name']),
            'email' => $_form['email']
        ];

        if($_form['password'] != '')
            $data['password'] =  bcrypt($_form['password']);

        $model = User::findOrFail($_form['id']);

        if($model->update($data)){ //$model->update($data);
            $model->roles()->detach();
            $model->roles()->attach(intval($_form['role_id']));

            return redirect()
                ->route('user', [str_replace('?', '', $qs)])
                ->with('success-message', "User is updated.");
        }

        return redirect()
                ->route('user', [str_replace('?', '', $qs)])
                ->with('error-message', "Cannot update user.");
    }

    
}