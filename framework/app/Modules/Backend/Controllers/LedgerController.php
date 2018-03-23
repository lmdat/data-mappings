<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Http\Request;
use App\Libs\Utils\Vii;

use App\Models\Dimension;
use App\Models\DimensionType;
use App\Models\Company;
use Illuminate\Support\Facades\Session;

class LedgerController extends Controller{
    const LANG_NAME = 'ledger';

    public function __construct(){
        parent::__construct();

        view()->share('lang_mod', $this->mod . '/' . self::LANG_NAME);

        $actions = request()->route()->getAction();
        $this->prefixUrl = $actions['prefix'];


    }

    public function getDimension(Request $request, $id=null){

        $display_rows = $request->input('rows_per_page', 15);

        $aqs = $request->except('page'); 
        // unset($aqs['page']);
        $paging_qs = Vii::queryStringBuilder($aqs);

        $com_id = 1;

        $entries = Dimension::where('company_id', $com_id)
            ->select('*')
            ->orderBy('dim_type', 'ASC')
            ->orderBy('dim_name', 'ASC')
            ->paginate($display_rows);
        
        $entries->withPath(route('dimension', [str_replace('?', '', $paging_qs)]));

        $companies = Company::all();

        $dim_type = DimensionType::all();
        
        //dd($entries->toArray());

        if($id != null){
            $dim = Dimension::findOrFail($id);
            // dd($dim->toArray());
            return view(
                'Backend::dimension.edit-dimension',
                [
                    'form_uri' => ($id == null) ? route('dimension-post-create') : route('dimension-put-edit', [$id]),
                    'page_title' => 'Define Dimension',
                    'entries' => $entries,
                    'qs' => Vii::queryStringBuilder($request->getQueryString()),
                    'dim' => $dim,
                    'companies' => Vii::createOptionData($companies->toArray(), 'id', ['company_name']),
                    'type_list' => Vii::createOptionData($dim_type->toArray(), 'id', ['type_name']),
                    
                    
                    //'user' => session()->get('test-name', $full_name)
                ]
            );

        }
       
        return view(
            'Backend::dimension.add-dimension',
            [
                'form_uri' => ($id == null) ? route('dimension-post-create') : route('dimension-put-edit', [$id]),
                'page_title' => 'Define Dimension',
                'entries' => $entries,
                'qs' => Vii::queryStringBuilder($request->getQueryString()),
                'companies' => Vii::createOptionData($companies->toArray(), 'id', ['company_name']),
                'type_list' => Vii::createOptionData($dim_type->toArray(), 'id', ['type_name']),
                
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
        
        if($step == 1){
            return view(
                'Backend::ledger.import-ledger-' . $step,
                [
                    'form_uri' => route('ledger-post-import'),
                    'page_title' => 'Import Ledger - Step ' . $step,
                    'step' => $step,
                    'qs' => Vii::queryStringBuilder($request->getQueryString()),
                                    
                    //'user' => session()->get('test-name', $full_name)
                ]
            );
        }
        else{

            $ledger_fields = [
                'account_code' => 'Account Code',
                'ledger_code' => 'Ledger Code',
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

            
                        
            return view(
                'Backend::ledger.import-ledger-' . $step,
                [
                    'form_uri' => route('ledger-post-import'),
                    'page_title' => 'Import Ledger - Step ' . $step,
                    'step' => $step,
                    'ledger_headers' => $ledger_headers,
                    'ledger_fields' => $ledger_fields,
                    'dim_types' => $dim_types,
                    'qs' => Vii::queryStringBuilder($request->getQueryString()),
                                    
                    //'user' => session()->get('test-name', $full_name)
                ]
            );
        }

    }

    public function postImportLedger(Request $request){
        $step = $request->post('step');

        if($step == 1){

            $ufile = $request->file('data_file');
            //dd($ufile->getClientMimeType());

            $reader = $this->createReader($ufile);

            if($reader != null){
                $spreadsheet = $reader->load($ufile->path());
                $headers = $this->getHeaderColunm($request, $spreadsheet);
                
                $request->session()->put('ledger_headers', $headers);
                $request->session()->put('obj_reader', $reader);
                $request->session()->put('file_path', $ufile->path());
            }


            $qs = Vii::queryStringBuilder($request->getQueryString());

            return redirect()
                    ->route('import-ledger', ['step' => 2, str_replace('?', '', $qs)]);
            
            // $arr = file($ufile->path());
            // if($request->post('skip_first_line') != null)
            //     array_shift($arr);
        }
        else{

            // dd($request->session()->get('obj_reader'));
            // dd($request->all());
            $ledger_field = $request->post('field_name');
            $ledger_header = $request->post('ledger_header');

            $dim_type_id = $request->post('dim_type_id');
            $dim_header = $request->post('dim_header');

            $ledgers = array_combine($ledger_field, $ledger_header);
            $dims = array_combine($dim_type_id, $dim_header);

            $reader = $request->session()->get('obj_reader');
            dd($request->session()->get('file_path'));
            // $spreadsheet = $reader->load(session()->get('file_path'));
            // $this->insertNewAccount($request, $spreadsheet, $ledgers);
        }
    }

    private function createReader($ufile){
        $mime = [
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xsl' => 'application/vnd.ms-excel',
            'csv' => 'application/vnd.ms-excel'
        ];


        $reader = null;
        if($ufile->getClientMimeType() == $mime['xlsx']){   // .xlsx
            // dd('xlsx');
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }
        else{
            if($ufile->getClientOriginalExtension() == 'xls'){    // .xls
                // dd('xls');
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            }
            else{   // .csv
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();

            }
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

    private function insertNewAccount($request, $spreadsheet, $ledgers){
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        $acc_code_col = $ledgers['account_code'];
        $acc_name_col = $acc_code_col + 1;

        $accounts = [];
        for($row=1; $row<=$highestColumnIndex; $row++){
            if($row == 1 && $request->post('skip_first_line') != null)
                continue;
            $accounts[$worksheet->getCellByColumnAndRow($acc_code_col, $row)->getValue()] = $worksheet->getCellByColumnAndRow($acc_name_col, $row)->getValue();
        }
        dd($accounts);
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