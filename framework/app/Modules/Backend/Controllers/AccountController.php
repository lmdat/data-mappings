<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Http\Request;
use App\Libs\Utils\Vii;

use App\Models\Account;
use App\Models\Company;


class AccountController extends Controller{
    const LANG_NAME = 'account';

    public function __construct(){
        parent::__construct();

        view()->share('lang_mod', $this->mod . '/' . self::LANG_NAME);

        $actions = request()->route()->getAction();
        $this->prefixUrl = $actions['prefix'];


    }

    public function getAccount(Request $request, $id=null){

        $display_rows = $request->input('rows_per_page', 15);

        $aqs = $request->except('page'); 
        // unset($aqs['page']);
        $paging_qs = Vii::queryStringBuilder($aqs);

        $entries = Account::select('*')
            ->orderBy('account_name', 'ASC')
            ->paginate($display_rows);
        
        $entries->withPath(route('account', [str_replace('?', '', $paging_qs)]));

        $companies = Company::all();
        
        //dd($entries->toArray());

        if($id != null){
            $account = Account::findOrFail($id);
            // dd($account);
            return view(
                'Backend::account.edit-account',
                [
                    'form_uri' => ($id == null) ? route('account-post-create') : route('account-put-edit', [$id]),
                    'page_title' => 'Define Account',
                    'entries' => $entries,
                    'qs' => Vii::queryStringBuilder($request->getQueryString()),
                    'account' => $account,
                    'companies' => Vii::createOptionData($companies->toArray(), 'id', ['company_name']),
                    //'type_list' => Vii::createOptionData($mappings_type->toArray(), 'id', ['type_name', 'short_code']),
                    
                    
                    //'user' => session()->get('test-name', $full_name)
                ]
            );

        }
       
        return view(
            'Backend::account.add-account',
            [
                'form_uri' => ($id == null) ? route('account-post-create') : route('account-put-edit', [$id]),
                'page_title' => 'Define Account',
                'entries' => $entries,
                'qs' => Vii::queryStringBuilder($request->getQueryString()),
                'companies' => Vii::createOptionData($companies->toArray(), 'id', ['company_name']),
                //'type_list' => Vii::createOptionData($mappings_type->toArray(), 'id', ['type_name', 'short_code']),
                
                //'user' => session()->get('test-name', $full_name)
            ]
        );
    }

    public function postCreateAccount(Request $request){
       
        $qs = Vii::queryStringBuilder($request->getQueryString());

        if($request->get('show_multiple') == null){
            $form = $request->only(['account_name', 'account_code']);
            $account = new Account($form);
            $account->status = 1;

            if($account->save()){
                return redirect()
                    ->route('account', [str_replace('?', '', $qs)])
                    ->with('success-message', "1 account is created.");
            }

            // return redirect('/mappings-item' . $qs)->with('success-message', 'ERROR');
            return redirect()
                    ->route('account', [str_replace('?', '', $qs)])
                    ->with('error-message', 'Cannot create new account.');
        }
        else{
            // $accounts = explode("\r\n", $request->get('multiple_account'));
            // $data = [];
            // foreach($accounts as $account){
            //     $a = explode('|', $account);
            //     $_name = "";
            //     $_code = "";
            //     if(count($a) == 1){
            //         $_code = $a[0];
            //     }
            //     else{
            //         $_code = $a[0];
            //         $_name = $a[1];
            //     }

            //     $data[] = [
            //         'account_name' => trim($_name),
            //         'account_code' => trim($_code),
            //         'status' => 1
            //     ];

            // }

            // request()->validate([
            //     'data_file' => 'required|mimes:csv,xslx|max:1024',
            // ]);

            $ufile = $request->file('data_file');
            $arr = file($ufile->path());
            if($request->post('skip_first_line') != null)
                array_shift($arr);

            // $arr = array_unique($arr);
            
            $data = [];
            foreach($arr as $item){
                $a = explode(';', $item);
                $_code = trim($a[0]);
                $_name = trim($a[1]);
                if(array_key_exists($_code, $data))
                    continue;
                    
                $data[$_code] = [
                    'account_code' => $_code,
                    'account_name' => $_name,
                    'status' => 1
                ];
            }

            if(Account::insert($data)){
                $c = count($data);
                
                return redirect()
                    ->route('account', [str_replace('?', '', $qs)])
                    ->with('success-message', "{$c} accounts are created.");
            }

            // return redirect('/mappings-item' . $qs)->with('success-message', 'ERROR');
            return redirect()
                    ->route('account', [str_replace('?', '', $qs)])
                    ->with('error-message', 'Cannot create new accounts.');
        }
        
    }

    public function putEditAccount(Request $request, $id=null){
        $id = $request->post('id');

        $form = $request->only(['account_code', 'account_name']);
        
        $account = Account::findOrFail($id);

        $qs = Vii::queryStringBuilder($request->getQueryString());
        if($account->update($form)){
            return redirect()
                    ->route('account', [str_replace('?', '', $qs)])
                    ->with('success-message', "Account[with code={$account->account_code}] is updated.");
        }

        return redirect()
                    ->route('account', [str_replace('?', '', $qs)])
                    ->with('error-message', "Cannot update account[with code={$account->account_code}].");
    }

    // public function welcome(){

    //     $full_name = 'Vincent Valentine';//$this->guard->user()->first_name . ' ' . $this->guard->user()->surname;
        
    //     return view(
    //         'Backend::dashboard.welcome',
    //         [
    //             'page_title' => 'Dashboard',
    //             'user' => session()->get('test-name', $full_name)
    //         ]
    //     );
    // }
}