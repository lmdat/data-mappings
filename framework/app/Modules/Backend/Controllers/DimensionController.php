<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Http\Request;
use App\Libs\Utils\Vii;

use App\Models\Dimension;
use App\Models\DimensionType;
use App\Models\Company;

use App\Modules\Backend\Requests\Dimension\DimensionCreateRequest;
use App\Modules\Backend\Requests\Dimension\DimensionEditRequest;

class DimensionController extends Controller{
    const LANG_NAME = 'dimension';

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
            ->orderBy('dim_code', 'ASC')
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

    public function postCreateDimension(DimensionCreateRequest $request){
       
        $qs = Vii::queryStringBuilder($request->getQueryString());

        if($request->get('show_multiple') == null){
            $form = $request->only(['dim_name', 'dim_code', 'dim_type']);
            $dim = new Dimension($form);
            $dim->company_id = $this->companyId;
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

            if($request->hasFile('data_file')){
                if($request->file('data_file')->isValid()){
                    $ufile = $request->file('data_file');
                    $ext = $this->getTrueFileExtension($ufile);
                    $reader = $this->createReader($ext);
                    $spreadsheet = $reader->load($ufile->path());
                    $rs = $this->importDimension($request, $spreadsheet);

                    if($rs === false){
                        return redirect()
                            ->route('dimension', [str_replace('?', '', $qs)])
                            ->with('error-message', 'Cannot create new dimensions.');
                    }

                    return redirect()
                        ->route('dimension', [str_replace('?', '', $qs)])
                        ->with('success-message', "{$rs['dim']} dimensions are created.");

                }
            }

            return redirect()
                ->route('account', [str_replace('?', '', $qs)])
                ->with('error-message', 'Invalid file upload.');

            // $ufile = $request->file('data_file');
            // $arr = file($ufile->path());
            // if($request->post('skip_first_line') != null)
            //     array_shift($arr);

            
            // $data = [];
            // foreach($arr as $item){
            //     $a = explode(';', $item);
            //     $_code = trim($a[0]);
            //     $_name = trim($a[1]);
            //     if(array_key_exists($_code, $data) || $_code == '')
            //         continue;
                    
            //     $data[$_code] = [
            //         'dim_code' => $_code,
            //         'dim_name' => $_name,
            //         'company_id' => intval($request->post('company_id')),
            //         'dim_type' => intval($request->post('dim_type')),
            //         'status' => 1
            //     ];
            // }

            // if(Dimension::insert($data)){
            //     $c = count($data);
                
            //     return redirect()
            //         ->route('dimension', [str_replace('?', '', $qs)])
            //         ->with('success-message', "{$c} dimensions are created.");
            // }

            // // return redirect('/mappings-item' . $qs)->with('success-message', 'ERROR');
            // return redirect()
            //         ->route('dimension', [str_replace('?', '', $qs)])
            //         ->with('error-message', 'Cannot create new dimensions.');
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

    public function getChangeStatus(Request $request, $id=null){
        $model = Dimension::findOrFail($id);
        $val = 1 - $model->status;
        $model->update(['status'=> $val]);

        $qs = Vii::queryStringBuilder($request->getQueryString());
        return redirect()
                ->route('dimension', [str_replace('?', '', $qs)]);
    }
}