<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Http\Request;
use App\Libs\Utils\Vii;
use App\Models\MappingsType;
use App\Models\MappingsItem;
use App\Models\AccountData;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MappingsItemController extends Controller{

    const LANG_NAME = 'mappings-item';

    public function __construct(){
        parent::__construct();
        view()->share('lang_mod', $this->mod . '/' . self::LANG_NAME);

        $actions = request()->route()->getAction();
        $this->prefixUrl = $actions['prefix'];
    }

    public function getItem(Request $request, $id=null){
  
       
        $aqs = $request->except('page'); 
        // unset($aqs['page']);
        
        $qs = Vii::queryStringBuilder($aqs);
        
        $mappings_type = MappingsType::all();

        $root_parent = MappingsItem::getRootParentList();
       
        $fields = ['id', 'item_name', 'type_id', 'is_leaf'];

        $tree_data = [];
        if(count($root_parent) > 0){
            $tree_data = MappingsItem::createTreeList($root_parent, $fields, true);
        }

        // Paging
        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($tree_data);
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath(route('mappings-item', [str_replace('?', '', $qs)]));
        //dd($entries);
      
        if($id != null){
            $item = MappingsItem::findOrFail($id);
            //dd($item->toArray());

            return view(
                'Backend::mappings-item.edit-define-item',
                [
                    'form_uri' => ($id == null) ? route('mappings-item-post-create') : route('mappings-item-put-edit', [$id]),
                    'page_title' => 'Define Mapping Item',
                    'entries' => $entries,
                    'qs' => Vii::queryStringBuilder($request->getQueryString()),
                    'type_list' => Vii::createOptionData($mappings_type->toArray(), 'id', ['type_name', 'short_code']),
                    'tree_data' => Vii::createOptionData($tree_data, 'id', 'tmp_name', ['0'=>'---Root---']) ,
                    'item' => $item
                    //'user' => session()->get('test-name', $full_name)
                ]
            );
        }

        return view(
            'Backend::mappings-item.define-item',
            [
                'form_uri' => ($id == null) ? route('mappings-item-post-create') : route('mappings-item-put-edit', [$id]),
                'page_title' => 'Define Mapping Item',
                'entries' => $entries,
                'qs' => Vii::queryStringBuilder($request->getQueryString()),
                'type_list' => Vii::createOptionData($mappings_type->toArray(), 'id', ['type_name', 'short_code']),
                'tree_data' => Vii::createOptionData($tree_data, 'id', 'tmp_name', ['0'=>'---Root---']) ,
                
                //'user' => session()->get('test-name', $full_name)
            ]
        );
    }

    public function postCreateItem(Request $request){
        $form = $request->only(['parent_id', 'item_name', 'short_name', 'type_id', 'multiple_item']);

        // dd($form);

        if($form['parent_id'] == null){
            $form['parent_id'] = 0;
        }
        else{
            $form['parent_id'] = intval($form['parent_id']);
        }

        $qs = Vii::queryStringBuilder($request->getQueryString());

        if($request->get('show_multiple') == null){
            $item = new MappingsItem($form);
            $item->is_leaf = 1;

            if($item->save()){

                if($form['parent_id'] > 0){
                    $p = MappingsItem::findOrFail($form['parent_id']);
                    if($p->is_leaf == 1)
                        $p->update(['is_leaf' => 0]);
                }

                return redirect()
                    ->route('mappings-item', [str_replace('?', '', $qs)])
                    ->with('success-message', "1 items is created.");
            }

            // return redirect('/mappings-item' . $qs)->with('success-message', 'ERROR');
            return redirect()
                    ->route('mappings-item', [str_replace('?', '', $qs)])
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
                    'item_name' => trim($_name),
                    'short_name' => trim($_short),
                    'is_leaf' => 1
                ];

            }

            if(MappingsItem::insert($data)){

                if($form['parent_id'] > 0){
                    $p = MappingsItem::findOrFail($form['parent_id']);
                    if($p->is_leaf == 1)
                        $p->update(['is_leaf' => 0]);
                }

                $c = count($data);
                
                return redirect()
                    ->route('mappings-item', [str_replace('?', '', $qs)])
                    ->with('success-message', "{$c} items are created.");
            }

            // return redirect('/mappings-item' . $qs)->with('success-message', 'ERROR');
            return redirect()
                    ->route('mappings-item', [str_replace('?', '', $qs)])
                    ->with('error-message', 'Cannot create new items.');
        }
        
    }

    public function putEditItem(Request $request, $id=null){
        $id = $request->post('id');

        $form = $request->only(['parent_id', 'item_name', 'short_name', 'type_id']);
        
        $item = MappingsItem::findOrFail($id);

        $qs = Vii::queryStringBuilder($request->getQueryString());
        if($item->update($form)){
            return redirect()
                    ->route('mappings-item', [str_replace('?', '', $qs)])
                    ->with('success-message', "Item[with id={$id}] is updated.");
        }

        return redirect()
                    ->route('mappings-item', [str_replace('?', '', $qs)])
                    ->with('error-message', "Cannot update item[with id={$item->id}].");
    }

    public function getMountAccount(Request $request, $id=null){
        
        $aqs = $request->except('page'); 
        // unset($aqs['page']);
        
        $qs = Vii::queryStringBuilder($aqs);
        
        $mappings_type = MappingsType::all();

        $root_parent = MappingsItem::getRootParentList();
       
        $fields = ['id', 'item_name', 'type_id', 'is_leaf'];

        $tree_data = [];
        if(count($root_parent) > 0){
            $tree_data = MappingsItem::createTreeList($root_parent, $fields, true);
        }

        // Paging
        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($tree_data);
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->withPath(route('mappings-item', [str_replace('?', '', $qs)]));

        $item = MappingsItem::findOrFail($id);

        // Account List
        $accounts = AccountData::all();

        //dd($accounts);


        return view(
            'Backend::mappings-item.mount-account-item',
            [
                'form_uri' => route('account-post-mount', [$id]),
                'page_title' => 'Mount Account to Item',
                'entries' => $entries,
                'qs' => Vii::queryStringBuilder($request->getQueryString()),
                'item' => $item,
                'account_list' => Vii::createOptionData($accounts->toArray(), 'account_code', ['account_name', 'account_code'], null),
                
                //'user' => session()->get('test-name', $full_name)
            ]
        );
    }

    public function postMountAccountItem(Request $request, $id=null){
        $item_id = $request->input('item_id');
        $accounts = $request->input('mounted_account', []);
        
        $item = MappingsItem::findOrFail($item_id);
        $item->accounts()->detach();
        if(count($accounts) > 0) 
            $item->accounts()->attach($accounts);
        
        $qs = Vii::queryStringBuilder($request->getQueryString());
        return redirect()
                ->route('mappings-item', [str_replace('?', '', $qs)]);

    }
}