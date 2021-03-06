<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Http\Request;
use App\Libs\Utils\Vii;

use App\Models\Account;
use App\Models\Company;
use App\Models\Dimension;
use App\Models\DimensionType;

use App\Modules\Backend\Requests\Topic\TopicMountRequest;

use App\Modules\Backend\Requests\Dimension\DimensionCreateRequest;
use App\Modules\Backend\Requests\Dimension\DimensionEditRequest;

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

    // Account Dimension
    public function getDimension(Request $request, $id=null){

        $display_rows = $request->input('rows_per_page', 15);

        $aqs = $request->except('page'); 
        // unset($aqs['page']);
        $paging_qs = Vii::queryStringBuilder($aqs);

        
        $entries = Dimension::join('dimension_type', 'dimension_type.id', '=', 'dimension.dim_type')
            ->where('dimension_type.typical_name', 'account')
            ->where('dimension.company_id', $this->companyId)
            ->select('dimension.*')
            ->orderBy('dimension.dim_type', 'ASC')
            ->orderBy('dimension.dim_code', 'ASC')
            ->paginate($display_rows);
        
        $entries->withPath(route('account-dimension', [str_replace('?', '', $paging_qs)]));

        $companies = Company::all();

        $dim_type = DimensionType::where('typical_name', 'account')->get();
        
        //dd($entries->toArray());

        if($id != null){
            $dim = Dimension::findOrFail($id);
            // dd($dim->toArray());
            return view(
                'Backend::dimension.edit-dimension',
                [
                    'form_uri' => route('topic-dimension-put-edit', [$id]),
                    'page_title' => 'Define Dimension',
                    'entries' => $entries,
                    'qs' => Vii::queryStringBuilder($request->getQueryString()),
                    'dim' => $dim,
                    'companies' => Vii::createOptionData($companies->toArray(), 'id', ['company_name']),
                    'type_list' => Vii::createOptionData($dim_type->toArray(), 'id', ['type_name']),
                    'typical_name' => 'Account'
                    
                    
                    //'user' => session()->get('test-name', $full_name)
                ]
            );

        }
       
        return view(
            'Backend::dimension.add-dimension',
            [
                'form_uri' => route('topic-dimension-post-create'),
                'page_title' => 'Create Topic Dimension',
                'entries' => $entries,
                'qs' => Vii::queryStringBuilder($request->getQueryString()),
                'companies' => Vii::createOptionData($companies->toArray(), 'id', ['company_name']),
                'type_list' => Vii::createOptionData($dim_type->toArray(), 'id', ['type_name']),
                'typical_name' => 'Account'
                //'user' => session()->get('test-name', $full_name)
            ]
        );
    }

    public function postCreateDimension(DimensionCreateRequest $request){
       
        $qs = Vii::queryStringBuilder($request->getQueryString());

        if($request->get('show_multiple') == null){
            $form = $request->only(['dim_name', 'dim_code', 'dim_type']);
            $dim = new Dimension($form);
            $dim->company_id = $this->companyId;
            $dim->status = 1;

            if($dim->save()){
                return redirect()
                    ->route('account-dimension', [str_replace('?', '', $qs)])
                    ->with('success-message', "1 dimension is created.");
            }

            // return redirect('/mappings-item' . $qs)->with('success-message', 'ERROR');
            return redirect()
                    ->route('account-dimension', [str_replace('?', '', $qs)])
                    ->with('error-message', 'Cannot create new dimension.');
        }
        else{

            if($request->hasFile('data_file')){
                if($request->file('data_file')->isValid()){
                    $ufile = $request->file('data_file');
                    $ext = $this->getTrueFileExtension($ufile);
                    $reader = $this->createReader($ext);
                    $spreadsheet = $reader->load($ufile->path());
                    $rs = $this->importDimension($request, $spreadsheet);

                    if($rs === false){
                        return redirect()
                            ->route('account-dimension', [str_replace('?', '', $qs)])
                            ->with('error-message', 'Cannot create new dimensions.');
                    }

                    return redirect()
                        ->route('account-dimension', [str_replace('?', '', $qs)])
                        ->with('success-message', "{$rs['dim']} dimensions are created.");

                }
            }

            return redirect()
                ->route('account', [str_replace('?', '', $qs)])
                ->with('error-message', 'Invalid file upload.');

           
        }
        
    }

    public function putEditDimension(DimensionEditRequest $request, $id=null){
        $id = $request->post('id');

        $form = $request->only(['dim_code', 'dim_name', 'company_id', 'dim_type']);
        
        $dim = Dimension::findOrFail($id);

        $qs = Vii::queryStringBuilder($request->getQueryString());
        if($dim->update($form)){
            return redirect()
                    ->route('dimension', [str_replace('?', '', $qs)])
                    ->with('success-message', "Dimension[with code={$dim->dim_code}] is updated.");
        }

        return redirect()
                    ->route('dimension', [str_replace('?', '', $qs)])
                    ->with('error-message', "Cannot update dimension[with code={$dim->dim_code}].");
    }

    public function getMountDimension(Request $request, $id=null){
        
        return view(
            'Backend::account.mount-dimension-account',
            [
                'form_uri' => route('topic-dimension-post-mount'),
                'page_title' => 'Mount Diemsion To Topic',
                'qs' => Vii::queryStringBuilder($request->getQueryString()),
                
                //'user' => session()->get('test-name', $full_name)
            ]
        );
    }

    public function postMountDimension(TopicMountRequest $request, $id=null){

        $qs = Vii::queryStringBuilder($request->getQueryString());

        if($request->hasFile('data_file')){
            if($request->file('data_file')->isValid()){
                $ufile = $request->file('data_file');
                $ext = $this->getTrueFileExtension($ufile);
                $reader = $this->createReader($ext);
                $spreadsheet = $reader->load($ufile->path());
                $rs = $this->insertAccountDimension($request, $spreadsheet, ($ext == 'csv'));


                if($rs === false){
                    return redirect()
                        ->route('topic-dimension-mount', [str_replace('?', '', $qs)])
                        ->with('error-message', 'Cannot glue dimension to topic from file. Some errors are occurred!');
                }
    
                               
                return redirect()
                        ->route('topic-dimension-mount', [str_replace('?', '', $qs)])
                        ->with('success-message', "Glue {$rs['dim']} diemsion to topic from file successfully.");
            }
        }
        // $item_id = $request->input('item_id');
        // $accounts = $request->input('mounted_account', []);
        
        // $item = Topic::findOrFail($item_id);
        // $item->accounts()->detach();
        // if(count($accounts) > 0) 
        //     $item->accounts()->attach($accounts);
        
        // $qs = Vii::queryStringBuilder($request->getQueryString());
        // return redirect()
        //         ->route('topic', [str_replace('?', '', $qs)]);

    }

    private function importDimension($request, $spreadsheet){
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
                'dim_code' => $_code,
                'dim_name' => $_name,
                'dim_type' => intval($request->post('dim_type')),
                'company_id' => $this->companyId,
                'status' => 1
            ];
        }

        $rs = [
            'dim' => 0
        ];

        $done = true;
        
        if(count($data) > 0){
            foreach(collect($data)->chunk(100) as $subset){
                $done = $done & Dimension::insert($subset->toArray());
            }

            $rs['dim'] = count($data);
        }

        if($done){
            return $rs;
        }

        return $done;
    }

    private function insertAccountDimension($request, $spreadsheet, $is_csv=false){
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        $data = [];
        for($row=1; $row<=$highestRow; $row++){
            if($row == 1 && $request->post('skip_headers') != null)
                continue;

            $account_code = trim($worksheet->getCellByColumnAndRow(1, $row)->getValue());
            $dim_code = trim($worksheet->getCellByColumnAndRow(2, $row)->getValue());

            $data[] = [
                'account_code' => intval($account_code),
                'dim_code' => $dim_code,
                'company_id' => $this->companyId,
            ];
        }

        //dd($data);
        $rs = [
            'dim' => 0
        ];

        $done = true;
        
        if(count($data) > 0){
            foreach(collect($data)->chunk(100) as $subset){
                $done = $done & DB::table('account_dimension')->insert($subset->toArray());
            }

            $rs['dim'] = count($data);
        }

        if($done){
            return $rs;
        }

        return $done;
    }
}