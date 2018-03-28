<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Http\Request;
use App\Libs\Utils\Vii;

use App\Models\Company;
use App\Models\User;
use App\Models\Role;

use App\Modules\Backend\Requests\Company\CompanyCreateRequest;
use App\Modules\Backend\Requests\Company\CompanyEditRequest;


class CompanyController extends Controller{
    const LANG_NAME = 'company';

    public function __construct(){
        parent::__construct();

        view()->share('lang_mod', $this->mod . '/' . self::LANG_NAME);

        $actions = request()->route()->getAction();
        $this->prefixUrl = $actions['prefix'];


    }

    public function getCompany(Request $request, $id=null){

        $display_rows = $request->input('rows_per_page', 15);

        $aqs = $request->except('page'); 
        // unset($aqs['page']);
        $paging_qs = Vii::queryStringBuilder($aqs);

        $entries = Company::select('*')
            ->orderBy('company_name', 'ASC')
            ->paginate($display_rows);
        
        $entries->withPath(route('company', [str_replace('?', '', $paging_qs)]));

        $role = Role::where('power', 9999)->first();

        $user_list = User::join('user_role', 'user.id', '=', 'user_role.user_id')
            ->where('user_role.role_id', '!=', $role->id)
            ->where('status', 1)
            ->orderBy('first_name', 'ASC')
            ->select(['user.id', 'user.first_name', 'user.last_name', 'user.email'])
            ->get();
        
        foreach($user_list as &$user){
            $user->full_name = $user->first_name . ' ' . $user->last_name;
        }
        
        //dd($entries->toArray());

        if($id != null){
            $com = Company::findOrFail($id);
            // dd($account);
            return view(
                'Backend::company.edit-company',
                [
                    'form_uri' => route('company-put-edit'),
                    'page_title' => 'Edit Company',
                    'entries' => $entries,
                    'qs' => Vii::queryStringBuilder($request->getQueryString()),
                    'com' => $com,
                    'users' => Vii::createOptionData($user_list->toArray(), 'id', ['full_name', 'email'])
                    // 'companies' => Vii::createOptionData($companies->toArray(), 'id', ['company_name']),
                    //'type_list' => Vii::createOptionData($mappings_type->toArray(), 'id', ['type_name', 'short_code']),
                    
                    
                    //'user' => session()->get('test-name', $full_name)
                ]
            );

        }
       
        return view(
            'Backend::company.add-company',
            [
                'form_uri' => route('company-post-create'),
                'page_title' => 'Create Company',
                'entries' => $entries,
                'qs' => Vii::queryStringBuilder($request->getQueryString()),
                'users' => Vii::createOptionData($user_list->toArray(), 'id', ['full_name', 'email'], false)
                // 'companies' => Vii::createOptionData($companies->toArray(), 'id', ['company_name']),
                //'type_list' => Vii::createOptionData($mappings_type->toArray(), 'id', ['type_name', 'short_code']),
                
                //'user' => session()->get('test-name', $full_name)
            ]
        );
    }

    public function getSelectCompany(Request $request){

        $user_coms = $request->user($this->guardName)->companies()->get();
        $role_alias = $request->user($this->guardName)->getMaxRoleAlias();
        
        $ids = [];
        foreach($user_coms as $v){
            $ids[] = $v->pivot->company_id;
        }

        // $com_entries = Company::join('user_company', 'company.id', '=', 'user_company.company_id')
        //     ->where('user_company.user_id', $request->user($this->guardName)->id)
        //     ->where('company.status', 1)
        //     ->orderBy('company.company_name')
        //     ->select(['company.id', 'company.company_name', 'company.short_name'])
        //     ->get();
        $sql = Company::where('status', 1);
        // if(!($role_alias == 'SA' || $role_alias = 'A')){
        if($role_alias != 'SA'){
            $sql->whereIn('id', $ids);
        }

        $com_entries = $sql->orderBy('company_name')
            ->select(['id', 'company_name', 'short_name'])
            ->get();

       
        // dd($com_entries->toArray());
        
        $selected_id = $request->session()->get('selected_company', 0);
        $selected_com = Company::find($selected_id);
        
        return view(
            'Backend::company.select-company',
            [
                'form_uri' => route('company-post-select'),
                'page_title' => 'Select Company',
                'qs' => Vii::queryStringBuilder($request->getQueryString()),
                'companies' => Vii::createOptionData($com_entries->toArray(), 'id', ['company_name', 'short_name']),
                'selected_company' => $selected_com
                // 'companies' => Vii::createOptionData($companies->toArray(), 'id', ['company_name']),
                //'type_list' => Vii::createOptionData($mappings_type->toArray(), 'id', ['type_name', 'short_code']),
                
                //'user' => session()->get('test-name', $full_name)
            ]
        );
    }

    public function postSelectCompany(Request $request){
        $cid = $request->post('company_id');
        // dd($cid);
        $com = Company::findOrFail($cid);
        session()->put('selected_company', $com->id);
        session()->put('selected_company_name', $com->company_name);
        
        return redirect()
                ->route('dashboard');
    }

    public function postCreateCompany(CompanyCreateRequest $request){
       
        $qs = Vii::queryStringBuilder($request->getQueryString());
        $form = $request->only(['company_name', 'short_name', 'phone', 'mobile']);

        $com = new Company($form);

        if($com->save()){
            $ids = $request->post('user_id', []);
            if(count($ids) > 0)
                $com->users()->attach($ids);

            return redirect()
                ->route('company', [str_replace('?', '', $qs)])
                ->with('success-message', "Company is created.");
        }

        return redirect()
                    ->route('company', [str_replace('?', '', $qs)])
                    ->with('error-message', 'Cannot create new company.');

      
        
    }

    public function putEditCompany(CompanyEditRequest $request, $id=null){
        $id = $request->post('id');

        $form = $request->only(['company_name', 'short_name', 'phone', 'mobile']);
        // dd($request->all());
        $com = Company::findOrFail($id);

        $qs = Vii::queryStringBuilder($request->getQueryString());
       
        if($com->update($form)){
            $ids = $request->post('user_id', []);
            
            $com->users()->detach();
            if(count($ids) > 0){
                $com->users()->attach($ids);
            }
               
            return redirect()
                    ->route('company', [str_replace('?', '', $qs)])
                    ->with('success-message', "Company is updated.");
        }

        return redirect()
                    ->route('company', [str_replace('?', '', $qs)])
                    ->with('error-message', "Cannot update Company.");
    }

    public function getChangeStatus(Request $request, $id=null){
        $model = Company::findOrFail($id);
        $val = 1 - $model->status;
        $model->update(['status'=> $val]);

        $qs = Vii::queryStringBuilder($request->getQueryString());
        return redirect()
                ->route('company', [str_replace('?', '', $qs)]);
    }
}