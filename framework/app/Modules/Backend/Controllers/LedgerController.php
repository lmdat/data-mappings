<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Http\Request;
use App\Libs\Utils\Vii;

use App\Models\Dimension;
use App\Models\DimensionType;
use App\Models\Company;


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

    }

    public function postImportLedger(Request $request){
        $step = $request->post('step');

        if($step == 1){
            $mime = [
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'xsl' => 'application/vnd.ms-excel',
                'csv' => 'application/vnd.ms-excel'
            ];

            $ufile = $request->file('data_file');
            
            // dd($ufile->guessClientExtension());

            if($ufile->getClientMimeType() == $mime['xlsx']){   // .xlsx
                dd('xlsx');
            }
            else{
                if($ufile->getClientOriginalExtension() == 'xls'){    // .xls
                    dd('xls');
                }
                else{   // .csv
                    $headers = $this->getHeaderColunmFromCsv($request, $ufile);
                    dd($headers);
                }
            }
            
            // $arr = file($ufile->path());
            // if($request->post('skip_first_line') != null)
            //     array_shift($arr);
        }
    }

    private function getHeaderColunmFromExcel($request, $file){

    }

    private function getHeaderColunmFromCsv($request, $ufile){
        $arr = file($ufile->path());
        $first_line = '';
        if($request->post('skip_first_line') != null){
            $first_line = array_shift($arr);
        }
        else{
            $first_line = $arr[0];
        }

        $rs = [];
        $items = explode(';', $first_line);
        if(count($items) == 0){
            $items = explode(',', $first_line);
        }
        foreach($items as $item){
            $rs [] = trim($item);
        }

        return $rs;
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