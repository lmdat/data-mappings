<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Http\Request;
use App\Libs\Utils\Vii;

use App\Models\TopicType;
use App\Models\Topic;
use App\Models\Account;
use App\Models\Dimension;
use App\Models\DimensionType;
use App\Models\Company;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\Modules\Backend\Requests\Topic\TopicCreateRequest;
use App\Modules\Backend\Requests\Topic\TopicEditRequest;
use App\Modules\Backend\Requests\Topic\TopicMountRequest;

use App\Modules\Backend\Requests\Dimension\DimensionCreateRequest;
use App\Modules\Backend\Requests\Dimension\DimensionEditRequest;

class TopicController extends Controller{

    const LANG_NAME = 'topic';

    // private $companyId;
   

    public function __construct(){
        parent::__construct();
        view()->share('lang_mod', $this->mod . '/' . self::LANG_NAME);

        $actions = request()->route()->getAction();
        $this->prefixUrl = $actions['prefix'];

       
    }

    public function getTopic(Request $request, $id=null){
  
        $aqs = $request->except('page'); 
        // unset($aqs['page']);
        
        $qs = Vii::queryStringBuilder($aqs);
        
        $topic_type = TopicType::all();

        $root_parent = Topic::getRootParentList($this->companyId);
       
        $fields = ['id', 'topic_name', 'type_id', 'is_leaf'];

        $tree_data = [];
        if(count($root_parent) > 0){
            $tree_data = Topic::createTreeList($root_parent, $fields, $this->companyId, true);
        }

        // Paging
        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($tree_data);
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath(route('topic', [str_replace('?', '', $qs)]));
        //dd($entries);
      
        if($id != null){
            $item = Topic::findOrFail($id);
            //dd($item->toArray());

            return view(
                'Backend::topic.edit-topic',
                [
                    'form_uri' => route('topic-put-edit', [$id]),
                    'page_title' => 'Edit Topic',
                    'entries' => $entries,
                    'qs' => Vii::queryStringBuilder($request->getQueryString()),
                    'type_list' => Vii::createOptionData($topic_type->toArray(), 'id', ['type_name']),
                    'tree_data' => Vii::createOptionData($tree_data, 'id', 'tmp_name', ['0'=>'---Root---']) ,
                    'item' => $item
                    //'user' => session()->get('test-name', $full_name)
                ]
            );
        }

        return view(
            'Backend::topic.create-topic',
            [
                'form_uri' => route('topic-post-create'),
                'page_title' => 'Create Topic',
                'entries' => $entries,
                'qs' => Vii::queryStringBuilder($request->getQueryString()),
                'type_list' => Vii::createOptionData($topic_type->toArray(), 'id', ['type_name']),
                'tree_data' => Vii::createOptionData($tree_data, 'id', 'tmp_name', ['0'=>'---Root---']) ,
                
                //'user' => session()->get('test-name', $full_name)
            ]
        );
    }
   

    public function postCreateTopic(TopicCreateRequest $request){
        $form = $request->only(['parent_id', 'topic_name', 'short_name', 'type_id', 'multiple_item']);

        // dd($form);

        if($form['parent_id'] == null){
            $form['parent_id'] = 0;
        }
        else{
            $form['parent_id'] = intval($form['parent_id']);
        }

        $qs = Vii::queryStringBuilder($request->getQueryString());

        if($request->get('show_multiple') == null){
            $item = new Topic($form);
            $item->company_id = $this->companyId;
            $item->is_leaf = 1;

            if($item->save()){

                if($form['parent_id'] > 0){
                    $p = Topic::findOrFail($form['parent_id']);
                    if($p->is_leaf == 1)
                        $p->update(['is_leaf' => 0]);
                }

                return redirect()
                    ->route('topic', [str_replace('?', '', $qs)])
                    ->with('success-message', "1 items is created.");
            }

            // return redirect('/topic' . $qs)->with('success-message', 'ERROR');
            return redirect()
                    ->route('topic', [str_replace('?', '', $qs)])
                    ->with('error-message', 'Cannot create new item.');
        }
        else{
            
            $items = explode("\r\n", $form['multiple_item']);
            $data = [];
            foreach($items as $item){
                $a = explode('|', $item);
                $_name = "";
                $_short = "";
                if(count($a) == 1){
                    $_name = $a[0];
                }
                else{
                    $_name = $a[0];
                    $_short = $a[1];
                }

                $data[] = [
                    'parent_id' => $form['parent_id'],
                    'type_id' => $form['type_id'],
                    'topic_name' => trim($_name),
                    'short_name' => trim($_short),
                    'company_id' => $this->companyId,
                    'is_leaf' => 1
                ];

            }

            

            if(Topic::insert($data)){

                if($form['parent_id'] > 0){
                    $p = Topic::findOrFail($form['parent_id']);
                    if($p->is_leaf == 1)
                        $p->update(['is_leaf' => 0]);
                }

                $c = count($data);
                
                return redirect()
                    ->route('topic', [str_replace('?', '', $qs)])
                    ->with('success-message', "{$c} items are created.");
            }

            // return redirect('/topic' . $qs)->with('success-message', 'ERROR');
            return redirect()
                    ->route('topic', [str_replace('?', '', $qs)])
                    ->with('error-message', 'Cannot create new items.');
        }
        
    }

    public function putEditTopic(TopicEditRequest $request, $id=null){
        $id = $request->post('id');

        $form = $request->only(['parent_id', 'topic_name', 'short_name', 'type_id']);
        
        $item = Topic::findOrFail($id);

        $qs = Vii::queryStringBuilder($request->getQueryString());
        if($item->update($form)){
            return redirect()
                    ->route('topic', [str_replace('?', '', $qs)])
                    ->with('success-message', "Item[with id={$id}] is updated.");
        }

        return redirect()
                    ->route('topic', [str_replace('?', '', $qs)])
                    ->with('error-message', "Cannot update item[with id={$item->id}].");
    }

    public function getMountLedger(Request $request, $id=null){
        
        // $aqs = $request->except('page'); 
        // // unset($aqs['page']);
        
        // $qs = Vii::queryStringBuilder($aqs);
        
        // $topic_type = TopicType::all();

        // $root_parent = Topic::getRootParentList();
       
        // $fields = ['id', 'topic_name', 'type_id', 'is_leaf'];

        // $tree_data = [];
        // if(count($root_parent) > 0){
        //     $tree_data = Topic::createTreeList($root_parent, $fields, true);
        // }

        // // Paging
        // $perPage = 15;
        // $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // $col = new Collection($tree_data);
        // $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        // $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        // $entries->withPath(route('topic', [str_replace('?', '', $qs)]));

        // $item = Topic::findOrFail($id);

        // // Account List
        // $accounts = Account::all();

        //dd($accounts);


        return view(
            'Backend::topic.mount-ledger-topic',
            [
                'form_uri' => route('ledger-post-mount'),
                'page_title' => 'Mount Ledger Key to Item',
                'qs' => Vii::queryStringBuilder($request->getQueryString()),
                
                //'user' => session()->get('test-name', $full_name)
            ]
        );
    }

    public function postMountLedger(TopicMountRequest $request, $id=null){

        $qs = Vii::queryStringBuilder($request->getQueryString());

        if($request->hasFile('data_file')){
            if($request->file('data_file')->isValid()){
                $ufile = $request->file('data_file');
                $ext = $this->getTrueFileExtension($ufile);
                $reader = $this->createReader($ext);
                $spreadsheet = $reader->load($ufile->path());
                $rs = $this->insertTopicLedger($request, $spreadsheet, ($ext == 'csv'));


                if($rs === false){
                    return redirect()
                        ->route('ledger-mount', [str_replace('?', '', $qs)])
                        ->with('error-message', 'Cannot glue ledger key to item from file. Some errors are occurred!');
                }
    
                               
                return redirect()
                        ->route('ledger-mount', [str_replace('?', '', $qs)])
                        ->with('success-message', "Glue {$rs['ledger']} ledger key to item from file successfully.");
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
    
    private function insertTopicLedger($request, $spreadsheet, $is_csv=false){
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        $data = [];
        for($row=1; $row<=$highestRow; $row++){
            if($row == 1 && $request->post('skip_headers') != null)
                continue;

            $item_id = trim($worksheet->getCellByColumnAndRow(1, $row)->getValue());
            $ledger_key = str_replace(['_#BLANK#'], '', trim($worksheet->getCellByColumnAndRow(2, $row)->getValue()));

            $data[] = [
                'mappings_code' => $item_id,
                'ledger_code' => $ledger_key,
                'company_id' => $this->companyId
            ];
        }

        //dd($data);
        $rs = [
            'ledger' => 0
        ];

        $done = true;
        
        if(count($data) > 0){
            foreach(collect($data)->chunk(100) as $subset){
                $done = $done & DB::table('ledger_item')->insert($subset->toArray());
            }

            $rs['ledger'] = count($data);
        }

        if($done){
            return $rs;
        }

        return $done;
    }

    // Topic Dimension
    public function getDimension(Request $request, $id=null){

        $display_rows = $request->input('rows_per_page', 15);

        $aqs = $request->except('page'); 
        // unset($aqs['page']);
        $paging_qs = Vii::queryStringBuilder($aqs);

        

        $entries = Dimension::join('dimension_type', 'dimension_type.id', '=', 'dimension.dim_type')
            ->where('dimension_type.typical_name', 'topic')
            ->where('dimension.company_id', $this->companyId)
            ->select('dimension.*')
            ->orderBy('dimension.dim_type', 'ASC')
            ->orderBy('dimension.dim_code', 'ASC')
            ->paginate($display_rows);
        
        $entries->withPath(route('topic-dimension', [str_replace('?', '', $paging_qs)]));

        $companies = Company::all();

        $dim_type = DimensionType::where('typical_name', 'topic')->get();
        
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
                    'typical_name' => 'Topic'
                    
                    
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
                'typical_name' => 'Topic'
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
                    ->route('topic-dimension', [str_replace('?', '', $qs)])
                    ->with('success-message', "1 dimension is created.");
            }

            // return redirect('/mappings-item' . $qs)->with('success-message', 'ERROR');
            return redirect()
                    ->route('topic-dimension', [str_replace('?', '', $qs)])
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
                            ->route('topic-dimension', [str_replace('?', '', $qs)])
                            ->with('error-message', 'Cannot create new dimensions.');
                    }

                    return redirect()
                        ->route('topic-dimension', [str_replace('?', '', $qs)])
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
            'Backend::topic.mount-dimension-topic',
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
                $rs = $this->insertTopicDimension($request, $spreadsheet, ($ext == 'csv'));


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

    private function insertTopicDimension($request, $spreadsheet, $is_csv=false){
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        $data = [];
        for($row=1; $row<=$highestRow; $row++){
            if($row == 1 && $request->post('skip_headers') != null)
                continue;

            $topic_id = trim($worksheet->getCellByColumnAndRow(1, $row)->getValue());
            $dim_code = trim($worksheet->getCellByColumnAndRow(2, $row)->getValue());

            $data[] = [
                'topic_id' => intval($topic_id),
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
                $done = $done & DB::table('topic_dimension')->insert($subset->toArray());
            }

            $rs['dim'] = count($data);
        }

        if($done){
            return $rs;
        }

        return $done;
    }
}
