<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Http\Request;
use App\Libs\Utils\Vii;

use App\Models\Dimension;
use App\Models\DimensionType;
use App\Models\Company;
use App\Models\Account;
use App\Models\Ledger;


class LedgerController extends Controller{
    const LANG_NAME = 'ledger';

    private $company_id;

    public function __construct(){
        parent::__construct();

        view()->share('lang_mod', $this->mod . '/' . self::LANG_NAME);

        $actions = request()->route()->getAction();
        $this->prefixUrl = $actions['prefix'];

        $this->company_id = 1;
    }

    public function getLedger(Request $request, $id=null){

        $display_rows = $request->input('rows_per_page', 15);

        $aqs = $request->except('page'); 
        // unset($aqs['page']);
        $paging_qs = Vii::queryStringBuilder($aqs);

        $com_id = 1;

        $entries = Ledger::where('company_id', $com_id)
            ->select(['account_code', 'ledger_key', 'base_amount', 'accounting_period'])
            ->orderBy('account_code', 'ASC')
            ->orderBy('accounting_period', 'ASC')
             ->paginate($display_rows);
        
        $entries->withPath(route('ledger', [str_replace('?', '', $paging_qs)]));

        // $companies = Company::all();

        // $dim_type = DimensionType::all();
        
        //dd($entries->toArray());

        if($id != null){
            $dim = Dimension::findOrFail($id);
            // dd($dim->toArray());
            return view(
                'Backend::ledger.list-ledger',
                [
                    'form_uri' => ($id == null) ? route('dimension-post-create') : route('dimension-put-edit', [$id]),
                    'page_title' => 'Ledger',
                    'entries' => $entries,
                    'qs' => Vii::queryStringBuilder($request->getQueryString()),
                    // 'dim' => $dim,
                    // 'companies' => Vii::createOptionData($companies->toArray(), 'id', ['company_name']),
                    // 'type_list' => Vii::createOptionData($dim_type->toArray(), 'id', ['type_name']),
                    
                    
                    //'user' => session()->get('test-name', $full_name)
                ]
            );

        }
       
        return view(
            'Backend::ledger.list-ledger',
                [
                    'form_uri' => ($id == null) ? route('dimension-post-create') : route('dimension-put-edit', [$id]),
                    'page_title' => 'Ledger',
                    'entries' => $entries,
                    'qs' => Vii::queryStringBuilder($request->getQueryString()),
                    // 'dim' => $dim,
                    // 'companies' => Vii::createOptionData($companies->toArray(), 'id', ['company_name']),
                    // 'type_list' => Vii::createOptionData($dim_type->toArray(), 'id', ['type_name']),
                    
                    
                    //'user' => session()->get('test-name', $full_name)
                ]
        );
    }

    public function postCreateDimension(Request $request){
       
        $qs = Vii::queryStringBuilder($request->getQueryString());

        if($request->get('show_multiple') == null){
            $form = $request->only(['dim_name', 'dim_code', 'company_id', 'dim_type']);
            $dim = new Dimension($form);
            $dim->status = 1;

            if($dim->save()){
                return redirect()
                    ->route('dimension', [str_replace('?', '', $qs)])
                    ->with('success-message', "1 dimension is created.");
            }

            // return redirect('/mappings-item' . $qs)->with('success-message', 'ERROR');
            return redirect()
                    ->route('dimension', [str_replace('?', '', $qs)])
                    ->with('error-message', 'Cannot create new dimension.');
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
            // dd($arr);
            $data = [];
            foreach($arr as $item){
                $a = explode(';', $item);
                $_code = trim($a[0]);
                $_name = trim($a[1]);
                if(array_key_exists($_code, $data) || $_code == '')
                    continue;
                    
                $data[$_code] = [
                    'dim_code' => $_code,
                    'dim_name' => $_name,
                    'company_id' => intval($request->post('company_id')),
                    'dim_type' => intval($request->post('dim_type')),
                    'status' => 1
                ];
            }

            if(Dimension::insert($data)){
                $c = count($data);
                
                return redirect()
                    ->route('dimension', [str_replace('?', '', $qs)])
                    ->with('success-message', "{$c} dimensions are created.");
            }

            // return redirect('/mappings-item' . $qs)->with('success-message', 'ERROR');
            return redirect()
                    ->route('dimension', [str_replace('?', '', $qs)])
                    ->with('error-message', 'Cannot create new dimensions.');
        }
        
    }

    public function putEditDimension(Request $request, $id=null){
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

    public function getImportLedger(Request $request, $step=1){
        
        //dd( Vii::queryStringBuilder($request->getQueryString()));
        $qs = Vii::queryStringBuilder($request->getQueryString());

        if($step == 1){
            return view(
                'Backend::ledger.import-ledger-' . $step,
                [
                    'form_uri' => route('ledger-post-import'),
                    'page_title' => 'Import Ledger - Step ' . $step,
                    'step' => $step,
                    'qs' => $qs,
                                    
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

        if($step == 1){

            $qs = Vii::queryStringBuilder($request->getQueryString());

            $ufile = $request->file('data_file');
            $ext = $this->getTrueFileExtension($ufile);
            
            // dd($ufile->getClientOriginalExtension());

            $reader = $this->createReader($ext);

            if($reader != null){
                $spreadsheet = $reader->load($ufile->path());
                $headers = $this->getHeaderColunm($request, $spreadsheet);
                
                $tmp = '.' . $ext;
                $file_name = str_replace($tmp, '', $ufile->getClientOriginalName()) . '_' . time() . $tmp;
                $file_path = $ufile->storeAs('upload', $file_name);
    
                $request->session()->put('file_info', ['file_path' => $file_path, 'ext' => $ext]);
                $request->session()->put('ledger_headers', $headers);
                $request->session()->put('skip_headers', $request->post('skip_first_line', 0));

                $request->session()->put('process_token', md5(time()));
            }
            else{
                return redirect()
                    ->route('import-ledger', ['step' => 1, str_replace('?', '', $qs)]);
            }

            
            return redirect()
                    ->route('import-ledger', ['step' => 2, str_replace('?', '', $qs)]);
            
            // $arr = file($ufile->path());
            // if($request->post('skip_first_line') != null)
            //     array_shift($arr);
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
            $qs = Vii::queryStringBuilder($request->getQueryString());

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
        else{
            $request->session()->forget('insert_result');
            $request->session()->forget('file_info');
            $request->session()->forget('ledger_headers');
            $request->session()->forget('skip_headers');
            $request->session()->forget('process_token');


        }
    }

    private function getTrueFileExtension($ufile){
        $mime = [
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls' => 'application/vnd.ms-excel',
            'csv' => 'application/vnd.ms-excel'
        ];

        $ext = null;
        if($ufile->getClientMimeType() == $mime['xlsx']){   // .xlsx
            $ext = 'xlsx';
        }
        else{
            if($ufile->getClientOriginalExtension() == $mime['xls']){    // .xls
                $ext = 'xls';
            }
            else{   // .csv
                $ext = 'csv';
            }
        }
        return $ext;
    }

    private function createReader($ext){
       
        $reader = null;
       
        if($ext == 'xlsx'){   // .xlsx
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }
        else if($ext == 'xls'){
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        }
        else{
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        }

        return $reader;
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

       
        $acc_entries = Account::where('company_id', $this->company_id)->get(['account_code']);

        $accounts = [];
        if($acc_entries != null){
            foreach($acc_entries as $v){
                $accounts[] = $v->account_code;
            }
        }

        $dim_entries = Dimension::where('company_id', $this->company_id)->get(['dim_code']);
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
        for($row=1; $row<=$highestRow; $row++){
            if($row == 1 && $request->post('skip_headers') != null)
                continue;
            
            // For Account
            $key = trim($worksheet->getCellByColumnAndRow($ledgers['account_code'], $row)->getValue());

            if($key != "" && !in_array($key, $accounts)){

                $val = trim($worksheet->getCellByColumnAndRow($ledgers['account_code'] + 1, $row)->getValue());
                $acc_data[$key] = [
                    'account_code' => $key,
                    'account_name' => $val,
                    'status' => 1,
                    'company_id' => $this->company_id
                ];
            }
            
            // For Dimension
            foreach($dims as $type_id => $v){
                if($v > 0){
                    $dim_code_col = $v;
                    $dim_name_col = $v + 1;
                    $key2 = trim($worksheet->getCellByColumnAndRow($dim_code_col, $row)->getValue());
                    
                    if($key2 != "" && !in_array($key2, $dimensions)){
                        $val2 = trim($worksheet->getCellByColumnAndRow($dim_name_col, $row)->getValue());
                        $dim_data[$key2] = [
                            'dim_code' => $key2,
                            'dim_name' => $val2,
                            'dim_type' => $type_id,
                            'status' => 1,
                            'company_id' => $this->company_id
                        ];
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
                }
                else{
                    $base_amount = 0;
                }
            }

            if($base_amount != 0)
                $base_amount = round($base_amount * 1);
            
            
            
            $accounting_period = trim($worksheet->getCellByColumnAndRow($ledgers['accounting_period'], $row)->getValue());
            $year = intval(substr($accounting_period, 0, 4));
            $month = intval(substr($accounting_period, 4));
            $ledger_data[] = [
                'company_id' => $this->company_id,
                'account_code' => $key,
                'ledger_key' => $ledger_key,
                'base_amount' => doubleval($base_amount),
                'accounting_period' => $year . '-' . (($month < 10) ? '0'.$month : $month) . '-01',
                'year' => $year,
                'month' => $month,
                'quarter_number' => $this->getQuarter($month),
                'created_at' => date('Y-m-d H:i:s')
            ];
        }

        // dd($acc_data, $dim_data, $ledger_data);
        
        $rs = [
            'account' => 0,
            'dim' => 0,
            'ledger' => 0
        ];

        $b = true;
        if(count($acc_data) > 0){
            if(Account::insert($acc_data))
                $rs['account'] = count($acc_data);
            else
                $b = $b & false;
        }

        if(count($dim_data) > 0){
            if(Dimension::insert($dim_data))
                $rs['dim'] = count($dim_data);
            else
                $b = $b & false;
        }

        if(count($ledger_data) > 0){
            if(Ledger::insert($ledger_data))
                $rs['ledger'] = count($ledger_data);
            else
                $b = $b & false;
        }

        if($b)
            return $rs;

        return $b;
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