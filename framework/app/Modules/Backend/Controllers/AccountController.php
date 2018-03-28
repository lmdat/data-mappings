<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Http\Request;
use App\Libs\Utils\Vii;

use App\Models\Account;
use App\Models\Company;

use App\Modules\Backend\Requests\Account\AccountCreateRequest;
use App\Modules\Backend\Requests\Account\AccountEditRequest;

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

        $entries = Account::where('company_id', $this->companyId)
            ->select('*')
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

    public function postCreateAccount(AccountCreateRequest $request){
       
        $qs = Vii::queryStringBuilder($request->getQueryString());

        if($request->get('show_multiple') == null){
            $form = $request->only(['account_name', 'account_code']);
            $account = new Account($form);
            $account->company_id = $this->companyId;
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
            
            if($request->hasFile('data_file')){
                if($request->file('data_file')->isValid()){
                    $ufile = $request->file('data_file');
                    $ext = $this->getTrueFileExtension($ufile);
                    $reader = $this->createReader($ext);
                    $spreadsheet = $reader->load($ufile->path());
                    $rs = $this->importAccount($request, $spreadsheet);

                    if($rs === false){
                        return redirect()
                            ->route('account', [str_replace('?', '', $qs)])
                            ->with('error-message', 'Cannot create new accounts.');
                    }

                    return redirect()
                        ->route('account', [str_replace('?', '', $qs)])
                        ->with('success-message', "{$rs['account']} accounts are created.");
                }
            }

            return redirect()
                ->route('account', [str_replace('?', '', $qs)])
                ->with('error-message', 'Invalid file upload.');
           
        }
        
    }

    public function putEditAccount(AccountEditRequest $request, $id=null){
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

    private function importAccount($request, $spreadsheet){
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        $data = [];
        for($row=1; $row<=$highestRow; $row++){
            if($row == 1 && $request->post('skip_first_line') != null)
                continue;

            $_code = trim($worksheet->getCellByColumnAndRow(1, $row)->getValue());
            $_name = trim($worksheet->getCellByColumnAndRow(2, $row)->getValue());

            if(array_key_exists($_code, $data) || $_code == '')
                continue;

            $data[$_code] = [
                'account_code' => $_code,
                'account_name' => $_name,
                'company_id' => $this->companyId,
                'status' => 1
            ];
        }

        $rs = [
            'account' => 0
        ];

        $done = true;
        
        if(count($data) > 0){
            foreach(collect($data)->chunk(100) as $subset){
                $done = $done & Account::insert($subset->toArray());
            }

            $rs['account'] = count($data);
        }

        if($done){
            return $rs;
        }

        return $done;
    }

    public function getChangeStatus(Request $request, $id=null){
        $model = Account::findOrFail($id);
        $val = 1 - $model->status;
        $model->update(['status'=> $val]);

        $qs = Vii::queryStringBuilder($request->getQueryString());
        return redirect()
                ->route('account', [str_replace('?', '', $qs)]);
    }
}