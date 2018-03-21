<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MappingsItem extends Model{

    protected $table = 'mappings_item';
    public $timestamps = false;
    //public $incrementing = false;

    protected $fillable = [
        'parent_id',
        'item_name',
        'short_name',
        'extra_attribute',
        'type_id',
        'is_leaf',
        'company_id',
        'status'
    ];

    protected $guarded = [
        'id'
    ];

    public function parent(){
        return $this->belongsTo('App\Models\MappingsItem', 'parent_id');
    }

    public function children(){
        return $this->hasMany('App\Models\MappingsItem', 'parent_id');
    }

    public function mappings_type(){
        return $this->belongsTo('App\Models\MappingsType', 'type_id');
    }

    // public function accounts(){
    //     return $this->belongsToMany('App\Models\AccountData', 'account_item', 'mappings_code', 'account_code', null, 'account_code');
    // }

    public function ledgers(){
        return $this->belongsToMany('App\Models\Ledger', 'ledger_item', 'mappings_code', 'ledger_code', null, 'ledger_code');
    }

    public function company(){
        return $this->belongsTo('App\Models\Company', 'company_id');
    }


    /*----- BACKEND FUNCTION -----*/
    public static function getRootParentList(){

        $fields = ['id', 'parent_id', 'item_name', 'type_id'];

        $root_parent = MappingsItem::where('parent_id', 0)
            //->where('published', '>=', 0)
            ->select($fields)
            ->orderBy('item_name', 'ASC')
            ->get();

        return $root_parent;
    }
    
    public static function createTreeList($parents, $fields=[], $is_trait=false){
        
        if(empty($parents))
            return [];
        
        $list = [];
        foreach($parents as $k => &$parent){

            $parent->tmp_name = $parent->item_name;

            $list[] = $parent;

            $children = $parent->children()->select($fields)
                ->orderBy('item_name', 'ASC')
                ->get();

            $arr_children =  self::createChildrenList($children, $parent, $is_trait);

            $list = array_merge($list, $arr_children);

        }

        //dd($list);

        return $list;
        
    }
    
    private static function createChildrenList($children=[], $parent, $is_trait=false, $h=1){
        
        if(empty($children))
            return [];

        $list = [];
        foreach($children as $k => &$item){
            $item->tmp_name = $item->item_name;
            if(!$is_trait){
                $str_indent = "";
                for($i=1; $i<$h ;$i++)
                    $str_indent .= "&nbsp;&nbsp;&nbsp;&nbsp;";

                $str_indent .= "&#8627;&nbsp;&nbsp;&nbsp;&nbsp;";
                //$str_indent .= "&#10551;&nbsp;&nbsp;&nbsp;&nbsp;";

                $item->tmp_name = $str_indent . $item->tmp_name;

            }
            else{
                //$item->cat_name = $parent->cat_name . '&nbsp;&#10097;&nbsp;' . $item->cat_name;
                $item->tmp_name = $parent->tmp_name . '&nbsp;&#10148;&nbsp;' . $item->tmp_name;
            }

            $list[] = $item;

            $temp_children = $item->children();
            if($temp_children != null){
                $arr_children = self::createChildrenList($temp_children, $item->id, $h + 1);
                $list = array_merge($list, $arr_children);
            }

        }

        return $list;
    }
}