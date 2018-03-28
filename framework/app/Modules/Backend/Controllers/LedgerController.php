<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Libs\Utils\Vii;

use App\Models\Dimension;
use App\Models\DimensionType;
use App\Models\Company;
use App\Models\Account;
use App\Models\Ledger;
use App\Models\UploadLedger;
use App\Models\UploadRevision;

use App\Modules\Backend\Requests\Ledger\LedgerImportRequest;


class LedgerController extends Controller{
    const LANG_NAME = 'ledger';

    private $company_id;

    

    public function __construct(){
        parent::__construct();

        view()->share('lang_mod', $this->mod . '/' . self::LANG_NAME);

        $actions = request()->route()->getAction();
        $this->prefixUrl = $actions['prefix'];

        $this->companyId = 1;

       
    }

    public function getLedger(Request $request, $id=null){

        $display_rows = $request->input('rows_per_page', 15);

        $aqs = $request->except(['page']); 
        // unset($aqs['page']);
        $paging_qs = Vii::queryStringBuilder($aqs);

        $revision_id = $request->get('rid', '');

        // echo($revision_id);

        $revision_entries = UploadRevision::where('company_id', $this->companyId)
            ->orderBy('created_at', 'DESC')
            ->get();
        
        $revisions = [];
        foreach($revision_entries as $entry){
           
            $revisions[] = [
                'id' => $entry->id,
                'text' => $entry->upload->upload_title . '&rarr;(Revision ' .  $entry->revision_number . ')'
            ];
        }

        // dd($revisions);
        $sql = Ledger::where('company_id', $this->companyId);
        if($revision_id != ''){
            $sql->where('revision', $revision_id);
        }
        $entries = $sql->select(['account_code', 'ledger_key', 'base_amount', 'accounting_period'])
            ->orderBy('account_code', 'ASC')
            ->orderBy('accounting_period', 'ASC')
            ->paginate($display_rows);
        
        $entries->withPath(route('ledger', [str_replace('?', '', $paging_qs)]));

        // $companies = Company::all();

        // $dim_type = DimensionType::all();
        
        //dd($entries->toArray());

        // if($id != null){
        //     $dim = Dimension::findOrFail($id);
        //     // dd($dim->toArray());
        //     return view(
        //         'Backend::ledger.list-ledger',
        //         [
        //             'form_uri' => ($id == null) ? route('dimension-post-create') : route('dimension-put-edit', [$id]),
        //             'page_title' => 'Ledger',
        //             'entries' => $entries,
        //             'qs' => Vii::queryStringBuilder($request->getQueryString()),
        //             // 'dim' => $dim,
        //             // 'companies' => Vii::createOptionData($companies->toArray(), 'id', ['company_name']),
        //             // 'type_list' => Vii::createOptionData($dim_type->toArray(), 'id', ['type_name']),
                    
                    
        //             //'user' => session()->get('test-name', $full_name)
        //         ]
        //     );

        // }
       
        return view(
            'Backend::ledger.list-ledger',
                [
                    'form_uri' => route('ledger'),
                    'page_title' => 'Ledger',
                    'entries' => $entries,
                    'qs' => Vii::queryStringBuilder($request->getQueryString()),
                    'revisions' => Vii::createOptionData($revisions, 'id', ['text']),
                    'revision_change_url' => route('ledger', [str_replace('?', '', Vii::queryStringBuilder($request->except(['page', 'rid'])))]),
                    'revision_id_selected' => $revision_id
                    // 'dim' => $dim,
                    // 'companies' => Vii::createOptionData($companies->toArray(), 'id', ['company_name']),
                    // 'type_list' => Vii::createOptionData($dim_type->toArray(), 'id', ['type_name']),
                    
                    
                    //'user' => session()->get('test-name', $full_name)
                ]
        );
    }

    public function getRevision(Request $request){

        $display_rows = $request->input('rows_per_page', 15);

        $aqs = $request->except(['page']); 
        // unset($aqs['page']);
        $paging_qs = Vii::queryStringBuilder($aqs);

        $upload_entries = UploadLedger::where('company_id', $this->companyId)
            ->orderBy('created_at', 'DESC')
            ->get();

        $upload_id = $request->get('upid');

        $sql = UploadRevision::where('company_id', $this->companyId);
             
        if($upload_id != ''){
            $sql->where('upload_id', $upload_id);
        }
        $entries = $sql->select('*')
            ->orderBy('upload_id', 'DESC')
            ->orderBy('revision_number', 'DESC')
            ->paginate($display_rows);
        
        $entries->withPath(route('revision', [str_replace('?', '', $paging_qs)]));

        // dd($upload_entries->toArray());


        return view(
            'Backend::ledger.list-revision',
                [
                    'form_uri' => route('revision'),
                    'page_title' => 'Revision of Ledger',
                    'entries' => $entries,
                    'qs' => Vii::queryStringBuilder($request->getQueryString()),
                    'upload_entries' => Vii::createOptionData($upload_entries->toArray(), 'id', ['upload_title']),
                    'revisions' => $entries,
                    'upload_change_url' => route('revision', [str_replace('?', '', Vii::queryStringBuilder($request->except(['page', 'upid'])))]),
                    'upload_id_selected' => $upload_id
                    
                ]
        );
    }

    public function getDownloadRevisionFile(Request $request, $id=null){

        $revision = UploadRevision::where('id', $id)
            ->where('company_id', $this->companyId)
            ->first();
        // dd($revision->toArray());
        
        // Headers
        $pinfo = pathinfo($revision->file_path);

        $headers = [
            'Content-Type: ' . $this->mime[$pinfo['extension']]
        ];

        // $file_path = storage_path('app/' . $revision->file_path);
        // return response()->download($file_path, $pinfo['basename'], $headers);

        return Storage::download($revision->file_path, $pinfo['basename'], $headers);
        
    }

    public function getDeleteRevision(Request $request, $id=null){
        $revision = UploadRevision::where('id', $id)
            ->where('company_id', $this->companyId)
            ->first();
        
        // Delete Ledger
        Ledger::where('revision', $revision->id)->delete();

        if(UploadRevision::where('upload_id', $revision->upload_id)->where('company_id', $this->companyId)->count() == 1){
            UploadLedger::destroy($revision->upload_id);
        }

        if(Storage::exists($revision->file_path)){
            Storage::delete($revision->file_path);
        }
        
        $revision->delete();

        $qs = Vii::queryStringBuilder($request->getQueryString());

        return redirect()
                    ->route('revision', [str_replace('?', '', $qs)]);
    }

    // public function postCreateDimension(Request $request){
       
    //     $qs = Vii::queryStringBuilder($request->getQueryString());

    //     if($request->get('show_multiple') == null){
    //         $form = $request->only(['dim_name', 'dim_code', 'company_id', 'dim_type']);
    //         $dim = new Dimension($form);
    //         $dim->status = 1;

    //         if($dim->save()){
    //             return redirect()
    //                 ->route('dimension', [str_replace('?', '', $qs)])
    //                 ->with('success-message', "1 dimension is created.");
    //         }

    //         // return redirect('/mappings-item' . $qs)->with('success-message', 'ERROR');
    //         return redirect()
    //                 ->route('dimension', [str_replace('?', '', $qs)])
    //                 ->with('error-message', 'Cannot create new dimension.');
    //     }
    //     else{
    //         // $accounts = explode("\r\n", $request->get('multiple_account'));
    //         // $data = [];
    //         // foreach($accounts as $account){
    //         //     $a = explode('|', $account);
    //         //     $_name = "";
    //         //     $_code = "";
    //         //     if(count($a) == 1){
    //         //         $_code = $a[0];
    //         //     }
    //         //     else{
    //         //         $_code = $a[0];
    //         //         $_name = $a[1];
    //         //     }

    //         //     $data[] = [
    //         //         'account_name' => trim($_name),
    //         //         'account_code' => trim($_code),
    //         //         'status' => 1
    //         //     ];

    //         // }

    //         // request()->validate([
    //         //     'data_file' => 'required|mimes:csv,xslx|max:1024',
    //         // ]);

    //         $ufile = $request->file('data_file');
    //         $arr = file($ufile->path());
    //         if($request->post('skip_first_line') != null)
    //             array_shift($arr);

    //         // $arr = array_unique($arr);
    //         // dd($arr);
    //         $data = [];
    //         foreach($arr as $item){
    //             $a = explode(';', $item);
    //             $_code = trim($a[0]);
    //             $_name = trim($a[1]);
    //             if(array_key_exists($_code, $data) || $_code == '')
    //                 continue;
                    
    //             $data[$_code] = [
    //                 'dim_code' => $_code,
    //                 'dim_name' => $_name,
    //                 'company_id' => intval($request->post('company_id')),
    //                 'dim_type' => intval($request->post('dim_type')),
    //                 'status' => 1
    //             ];
    //         }

    //         if(Dimension::insert($data)){
    //             $c = count($data);
                
    //             return redirect()
    //                 ->route('dimension', [str_replace('?', '', $qs)])
    //                 ->with('success-message', "{$c} dimensions are created.");
    //         }

    //         // return redirect('/mappings-item' . $qs)->with('success-message', 'ERROR');
    //         return redirect()
    //                 ->route('dimension', [str_replace('?', '', $qs)])
    //                 ->with('error-message', 'Cannot create new dimensions.');
    //     }
        
    // }

    // public function putEditDimension(Request $request, $id=null){
    //     $id = $request->post('id');

    //     $form = $request->only(['dim_code', 'dim_name', 'company_id', 'dim_type']);
        
    //     $dim = Dimension::findOrFail($id);

    //     $qs = Vii::queryStringBuilder($request->getQueryString());
    //     if($dim->update($form)){
    //         return redirect()
    //                 ->route('dimension', [str_replace('?', '', $qs)])
    //                 ->with('success-message', "Dimension[with code={$dim->dim_code}] is updated.");
    //     }

    //     return redirect()
    //                 ->route('dimension', [str_replace('?', '', $qs)])
    //                 ->with('error-message', "Cannot update dimension[with code={$dim->dim_code}].");
    // }

    public function getImportLedger(Request $request, $step=1){
        
        //dd( Vii::queryStringBuilder($request->getQueryString()));
        $qs = Vii::queryStringBuilder($request->getQueryString());

        if($step == 1){
            $entries = UploadLedger::where('company_id', $this->companyId)
                ->orderBy('created_at', 'DESC')
                ->get(['id', 'upload_title']);

                      
            $upload_ledgers = [];
            foreach($entries as $entry){
                $tmp = $entry;
                $total_revision = $entry->upload_revisions()->count();
                if($total_revision == 1){
                    $tmp->upload_title = $tmp->upload_title . ' (Has ' . $total_revision . ' Revision)';
                }                    
                else if($total_revision > 1){
                    $tmp->upload_title = $tmp->upload_title . ' (Has ' . $total_revision . ' Revisions)';
                }

                $upload_ledgers[] =  $tmp->toArray();
            }

            // dd($upload_ledgers);

            return view(
                'Backend::ledger.import-ledger-' . $step,
                [
                    'form_uri' => route('ledger-post-import'),
                    'page_title' => 'Import Ledger - Step ' . $step,
                    'step' => $step,
                    'qs' => $qs,
                    'total_upload' => $entries->count(),
                    'upload_ledgers' => Vii::createOptionData($upload_ledgers, 'id', ['upload_title'], false)                                    
                    //'user' => session()->get('test-name', $full_name)
                ]
            );
        }
        else if($step == 2){

            if(!$request->session()->has('process_token')){
                return redirect()
                    ->route('import-ledger', ['step' => 1, str_replace('?', '', $qs)]);
            }


            $ledger_fields = [
                'account_code' => 'Account Code',
                'ledger_key' => 'Ledger Key',
                'base_amount' => 'Base Amount',
                'accounting_period' => 'Accounting Period'
            ];

            $dim_types = DimensionType::all(['id', 'type_name']);
            // dd($dim_types->toArray());

            $headers = $request->session()->get('ledger_headers');
            
            $ledger_headers[] = 'Select one...';
            for($i=0; $i<count($headers); $i++){
                $ledger_headers[] = $headers[$i];
            }

            $skip_headers = 0;
            if($request->session()->has('skip_headers')){
                $skip_headers = $request->session()->get('skip_headers', 0);
                $request->session()->forget('skip_headers');
            }
            

                        
            return view(
                'Backend::ledger.import-ledger-' . $step,
                [
                    'form_uri' => route('ledger-post-import'),
                    'page_title' => 'Import Ledger - Step ' . $step,
                    'step' => $step,
                    'ledger_headers' => $ledger_headers,
                    'ledger_fields' => $ledger_fields,
                    'dim_types' => $dim_types,
                    'qs' => $qs,
                    'skip_headers' => $skip_headers
                                    
                    //'user' => session()->get('test-name', $full_name)
                ]
            );
        }
        else{

            if(!$request->session()->has('process_token')){
                return redirect()
                    ->route('import-ledger', ['step' => 1, str_replace('?', '', $qs)]);
            }

            $result = $request->session()->get('final_result', null);

            // Delete all session of these step
            $request->session()->forget('insert_result');
            $request->session()->forget('file_info');
            $request->session()->forget('ledger_headers');
            $request->session()->forget('skip_headers');
            $request->session()->forget('process_token');
            $request->session()->forget('upload_revision');

            return view(
                'Backend::ledger.import-ledger-' . $step,
                [
                    'form_uri' => route('ledger-post-import'),
                    'page_title' => 'Import Ledger - Step ' . $step,
                    'step' => $step,
                    'qs' => $qs,
                    'result' => $request->session()->get('final_result', null)
                ]
            );
        }

    }

    public function postImportLedger(Request $request){
        $step = $request->post('step', 0);

        if($step == 0){
            return redirect()
                    ->route('import-ledger', ['step' => 1, str_replace('?', '', $qs)]);
        }

        $qs = Vii::queryStringBuilder($request->getQueryString());

        if($step == 1){

            // Validation
            $request->validate([
                'data_file' => 'required|max:10240|mimetypes:text/plain,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                // 'data_file' => 'required|max:10240|mimes:csv,txt,xls,xlsx',
                'upload_title' => 'required_with:upload_type,1'
            ],[
                'data_file.required' => '[Data File] select a file to upload',
                'data_file.max' => '[Data File] file size must be <= 10Mb',
                'data_file.mimetypes' => '[Data File] file type must be csv, xls or xlsx',
                'upload_title.required_with' => '[Upload Title] cannot be blank'
            ]);

            $upload_revision = $request->only(['upload_type', 'upload_title', 'upload_id']);

            $ufile = $request->file('data_file');
            $ext = $this->getTrueFileExtension($ufile);
            
            // dd($ufile->getClientOriginalExtension(), $ufile->clientExtension(), $ufile->getClientMimeType(), $ufile->getMimeType());

            $reader = $this->createReader($ext);

            if($reader == null){
                return redirect()
                    ->route('import-ledger', ['step' => 1, str_replace('?', '', $qs)]);
            }

            $spreadsheet = $reader->load($ufile->path());
            $headers = $this->getHeaderColunm($request, $spreadsheet);
            
            $tmp = '.' . $ext;
            $file_name = str_replace($tmp, '', $ufile->getClientOriginalName()) . '_' . time() . $tmp;
            $file_path = $ufile->storeAs('upload', $file_name);

            $request->session()->put('file_info', ['file_path' => $file_path, 'ext' => $ext]);
            $request->session()->put('ledger_headers', $headers);
            $request->session()->put('skip_headers', $request->post('skip_first_line', 0));

            $request->session()->put('process_token', md5(time()));

            $request->session()->put('upload_revision', $upload_revision);

            return redirect()
                    ->route('import-ledger', ['step' => 2, str_replace('?', '', $qs)]);
            
            
        }
        else if($step == 2){

            // dd($request->session()->get('file_info'));
            // dd($request->all());
            $ledger_field = $request->post('field_name');
            $ledger_header = $request->post('ledger_header');

            $dim_type_id = $request->post('dim_type_id');
            $dim_header = $request->post('dim_header');

            $ledgers = array_combine($ledger_field, $ledger_header);
            $dims = array_combine($dim_type_id, $dim_header);


            $file_info = $request->session()->get('file_info');
                        
            $reader = $this->createReader($file_info['ext']);
            $spreadsheet = $reader->load(storage_path('app/' . $file_info['file_path']));

            $is_csv = ($file_info['ext'] == 'csv') ? true : false;

            $rs = $this->insertLedgerKey($request, $spreadsheet, $ledgers, $dims, $is_csv);
            // dd($rs);
            
            if($rs === false){
                return redirect()
                    ->route('import-ledger', ['step' => 3, str_replace('?', '', $qs)])
                    ->with('error-message', 'Cannot import ledger data from file. Some errors are occurred!');
            }

            $request->session()->put('final_result', $rs);
            
            return redirect()
                    ->route('import-ledger', ['step' => 3, str_replace('?', '', $qs)])
                    ->with('success-message', 'Import ledger data from file successfully.');
        }
        // else{
        //     $request->session()->forget('final_result');
        //     $request->session()->forget('file_info');
        //     $request->session()->forget('ledger_headers');
        //     $request->session()->forget('skip_headers');
        //     $request->session()->forget('process_token');
        //     $request->session()->forget('upload_revision');


        //     return redirect()
        //             ->route('ledger', [str_replace('?', '', $qs)]);
        // }
    }

        
    private function getHeaderColunm($request, $spreadsheet){
        // $arr = file($ufile->path());
        // $first_line = '';
        // if($request->post('skip_first_line') != null){
        //     $first_line = array_shift($arr);
        // }
        // else{
        //     $first_line = $arr[0];
        // }

        // $rs = [];
        // $items = explode(",", str_replace(";", ",", $first_line));
        // // dd($items);
       
        // foreach($items as $item){
        //     $rs [] = trim($item);
        // }

        // return $rs;
        $rs = [];
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        for ($col=1; $col<=$highestColumnIndex; $col++){
            $rs[] = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
        }

        // for ($row = 1; $row <= $highestRow; ++$row) {
           
        //     // for ($col = 1; $col <= $highestColumnIndex; ++$col) {
        //     //     $value = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
        //     //     echo '<td>' . $value . '</td>' . PHP_EOL;
        //     // }
        //     $rs[] = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
        // }

        return $rs;
    }

    private function insertLedgerKey($request, $spreadsheet, $ledgers, $dims, $is_csv=false){
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

       
        $acc_entries = Account::where('company_id', $this->companyId)->get(['account_code']);

        $accounts = [];
        if($acc_entries != null){
            foreach($acc_entries as $v){
                $accounts[] = $v->account_code;
            }
        }

        $dim_entries = Dimension::where('company_id', $this->companyId)->get(['dim_code']);
        $dimensions = [];
        if($dim_entries != null){
            foreach($dim_entries as $v){
                $dimensions[] = $v->dim_code;
            }
        }

        // dd($dimensions);
        
        $acc_data = [];
        $dim_data = [];
        $ledger_data = [];
        $revision_id = $this->createRevision($request);

        for($row=1; $row<=$highestRow; $row++){
            if($row == 1 && $request->post('skip_headers') != null)
                continue;
            
            // For Account
            $key = trim($worksheet->getCellByColumnAndRow($ledgers['account_code'], $row)->getValue());

            if($key != "" && !in_array($key, $accounts)){

                $val = trim($worksheet->getCellByColumnAndRow($ledgers['account_code'] + 1, $row)->getValue());
                if(!array_key_exists($key, $acc_data)){
                    $acc_data[$key] = [
                        'account_code' => $key,
                        'account_name' => $val,
                        'status' => 1,
                        'company_id' => $this->companyId
                    ];
                }
            }
            
            // For Dimension
            foreach($dims as $type_id => $v){
                if($v > 0){
                    $dim_code_col = $v;
                    $dim_name_col = $v + 1;
                    $key2 = trim($worksheet->getCellByColumnAndRow($dim_code_col, $row)->getValue());
                    
                    if($key2 != "" && !in_array($key2, $dimensions)){
                        $val2 = trim($worksheet->getCellByColumnAndRow($dim_name_col, $row)->getValue());
                        
                        if(!array_key_exists($key2, $dim_data)){
                            $dim_data[$key2] = [
                                'dim_code' => $key2,
                                'dim_name' => $val2,
                                'dim_type' => $type_id,
                                'status' => 1,
                                'company_id' => $this->companyId
                            ];
                        }
                    }
                        
                    
                }
            }

            // For Ledger
            $ledger_key = str_replace(['_#BLANK#'], '', trim($worksheet->getCellByColumnAndRow($ledgers['ledger_key'], $row)->getValue()));
            $base_amount = trim($worksheet->getCellByColumnAndRow($ledgers['base_amount'], $row)->getValue());
            if($is_csv){
                if($base_amount != ''){
                    $base_amount = str_replace([',', '.'], '', $base_amount);
                    if($base_amount[0] == '(' && $base_amount[strlen($base_amount) - 1] == ')'){
                        $base_amount = substr($base_amount, 1, strlen($base_amount) - 2) * (-1);
                    }
                    else{
                        $base_amount = $base_amount * 1;
                    }
                }
                else{
                    $base_amount = 0;
                }
            }

            if($base_amount != 0){
                $base_amount = round($base_amount);            
            }
            
            $accounting_period = trim($worksheet->getCellByColumnAndRow($ledgers['accounting_period'], $row)->getValue());
            $year = intval(substr($accounting_period, 0, 4));
            $month = intval(substr($accounting_period, 4));
           
            $ledger_data[] = [
                'company_id' => $this->companyId,
                'account_code' => $key,
                'ledger_key' => $ledger_key,
                'base_amount' => doubleval($base_amount),
                'accounting_period' => $year . '-' . (($month < 10) ? '0'.$month : $month) . '-01',
                'year' => $year,
                'month' => $month,
                'quarter_number' => $this->getQuarter($month),
                'created_at' => date('Y-m-d H:i:s'),
                'revision' => $revision_id
            ];
        }

        // dd($acc_data, $dim_data, $ledger_data);
        
        $rs = [
            'account' => 0,
            'dim' => 0,
            'ledger' => 0
        ];

        $done = true;

        if(count($acc_data) > 0){
            $a = true;
            foreach( collect($acc_data)->chunk(100) as $subset){
                $a = $a & Account::insert($subset->toArray());
            }

            if($a){
                $rs['account'] = count($acc_data);
            }
            
            $done = $done & $a;            
        }

        if(count($dim_data) > 0){
            $b = true;
            
            foreach(collect($dim_data)->chunk(100) as $subset){
                $b = $b & Dimension::insert($subset->toArray());
            }

            if($b){
                $rs['dim'] = count($dim_data);
            }
            
            $done = $done & $b;
        }

        if(count($ledger_data) > 0){

            $c = true;
            
            foreach(collect($ledger_data)->chunk(1000) as $subset){
                $c = $c & Ledger::insert($subset->toArray());
            }
           
            if($c){
                $rs['ledger'] = count($ledger_data);
            }

            $done = $done & $c;    
            
        }

        if($done)   return $rs;

        return $done;
    }

    private function createRevision($request){
        // Create Revision
        $file_info = session()->get('file_info');
        $upload_revision = session()->get('upload_revision');
        $upload_ledger = null;

        if(intval($upload_revision['upload_type']) == 1){
            $created_at =  date('Y-m-d H:i');
            $data = [
                'company_id' => $this->companyId,
                'upload_title' => $upload_revision['upload_title'] . ' (' . $created_at .')',
                'created_at' => $created_at,
                'salt_key' => md5(time()),
                'status' => 1
            ];
            $upload_ledger = UploadLedger::create($data);
        }

        if($upload_ledger == null)
            $upload_ledger = UploadLedger::findOrFail(intval($upload_revision['upload_id']));
        
        $count = UploadRevision::where('upload_id', $upload_ledger->id)
            ->where('company_id', $this->companyId)
            ->count();

        $data = [
            'id' => uniqid($this->companyId . $upload_ledger->id),
            'upload_id' => $upload_ledger->id,
            'company_id' => $this->companyId,
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 1,
            'revision_number' => $count + 1,
            'file_path' => $file_info['file_path']
        ];
        
        $upload_revision = UploadRevision::create($data);

        return $upload_revision->id;
    }

    private function getQuarter($month){
        if(!is_int($month))
            $month = intval($month);
        
        if($month < 4)
            return 1;
        if($month > 3 && $month < 7)
            return 2;
        if($month > 6 && $month < 10)
            return 3;
        return 4;
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