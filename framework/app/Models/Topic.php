<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Topic extends Model{

    protected $table = 'topic';
    public $timestamps = false;
    //public $incrementing = false;

    protected $fillable = [
        'parent_id',
        'topic_name',
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
        return $this->belongsTo('App\Models\Topic', 'parent_id');
    }

    public function children(){
        return $this->hasMany('App\Models\Topic', 'parent_id');
    }

    public function topic_type(){
        return $this->belongsTo('App\Models\TopicType', 'type_id');
    }

    // public function accounts(){
    //     return $this->belongsToMany('App\Models\AccountData', 'account_item', 'mappings_code', 'account_code', null, 'account_code');
    // }

    public function ledgers(){
        return $this->belongsToMany('App\Models\Ledger', 'ledger_topic', 'topic_code', 'ledger_code', null, 'ledger_key');
    }

    public function dimensions(){
        return $this->belongsToMany('App\Models\Dimension', 'topic_dimension', 'topic_id', 'dim_code', null, 'dim_code');//->withPivot('company_id');
    }

    public function company(){
        return $this->belongsTo('App\Models\Company', 'company_id');
    }


    /*----- BACKEND FUNCTION -----*/
    public static function getRootParentList($fields=['*'], $com_id=null){

        // $fields = ['id', 'parent_id', 'topic_name', 'type_id', 'is_leaf'];

        $sql = Topic::with('topic_type:topic_type.id,topic_type.type_name')
            ->with(['ledgers' => function($q){
                $q->select(['ledger.ledger_key'])->distinct();
            }])
            ->with(['dimensions' => function($q){
                $q->select(['dimension.dim_code'])->distinct();
            }])
            ->where('parent_id', 0);
        if($com_id != null)
            $sql->where('company_id', $com_id);
        
        $root_parent = $sql->select($fields)
            ->orderBy('id', 'ASC')
            ->get();
        
        return $root_parent;
    }
    
    public static function createTreeList($parents, $fields=['*'], $com_id=null, $is_trait=false){
        
        if(empty($parents))
            return [];
        
        $list = [];
        foreach($parents as $k => &$parent){

            $parent->tmp_name = $parent->topic_name;

            $list[] = $parent;

            $children = null;
            if($com_id == null){
                $children = $parent->children()
                    ->with('topic_type:topic_type.id,topic_type.type_name')
                    ->with(['ledgers' => function($q){
                        $q->select(['ledger.ledger_key'])->distinct();
                    }])
                    ->with(['dimensions' => function($q){
                        $q->select(['dimension.dim_code'])->distinct();
                    }])
                    ->select($fields)
                    ->orderBy('id', 'ASC')
                    ->get();
            }
            else{
                $children = $parent->children()
                    ->with('topic_type:topic_type.id,topic_type.type_name')
                    ->with(['ledgers' => function($q){
                        $q->select(['ledger.ledger_key'])->distinct();
                    }])
                    ->with(['dimensions' => function($q){
                        $q->select(['dimension.dim_code'])->distinct();
                    }])
                    ->where('company_id', $com_id)
                    ->select($fields)
                    ->orderBy('id', 'ASC')
                    ->get();
            }
            

            $arr_children =  self::createChildrenList($children, $parent, $com_id, $fields, $is_trait);

            $list = array_merge($list, $arr_children);

        }

        //dd($list);

        return $list;
        
    }
    
    private static function createChildrenList($children=[], $parent, $com_id, $fields, $is_trait=false, $h=1){
        
        if(empty($children))
            return [];

        $list = [];
        foreach($children as $k => &$item){
            $item->tmp_name = $item->topic_name;
            if(!$is_trait){
                $str_indent = "";
                for($i=1; $i<$h ;$i++)
                    $str_indent .= "&nbsp;&nbsp;&nbsp;&nbsp;";

                $str_indent .= "&#8627;&nbsp;&nbsp;&nbsp;&nbsp;";
                // $str_indent .= "&#10551;&nbsp;&nbsp;&nbsp;&nbsp;";
                

                $item->tmp_name = $str_indent . $item->tmp_name;

            }
            else{
                //$item->cat_name = $parent->cat_name . '&nbsp;&#10097;&nbsp;' . $item->cat_name;
                // $item->tmp_name = $parent->tmp_name . '&nbsp;&#10148;&nbsp;' . $item->tmp_name;
                $item->tmp_name = $parent->tmp_name . '&nbsp;&rarr;&nbsp;' . $item->tmp_name;
            }

            $list[] = $item;

            $temp_children = null;
            if($com_id == null){
                $temp_children = $item->children()
                    ->with('topic_type:topic_type.id,topic_type.type_name')
                    ->with(['ledgers' => function($q){
                        $q->select(['ledger.ledger_key'])->distinct();
                    }])
                    ->with(['dimensions' => function($q){
                        $q->select(['dimension.dim_code'])->distinct();
                    }])
                    ->select($fields)
                    ->get();
            }
            else{
                $temp_children = $item->children()
                    ->with('topic_type:topic_type.id,topic_type.type_name')
                    ->with(['ledgers' => function($q){
                        $q->select(['ledger.ledger_key'])->distinct();
                    }])
                    ->with(['dimensions' => function($q){
                        $q->select(['dimension.dim_code'])->distinct();
                    }])
                    ->where('company_id', $com_id)
                    ->select($fields)
                    ->get();
            }
            

            if($temp_children != null){
                $arr_children = self::createChildrenList($temp_children, $item, $com_id, $fields, $is_trait, $h + 1);
                $list = array_merge($list, $arr_children);
            }

        }

        return $list;
    }

    // API Function

    public static function createParentToChildren($com_id){

        $entries = Topic::with('topic_type:id,type_name')
            ->with(['dimensions' => function($q){
                $q->select(['dimension.dim_code'])->distinct();
            }])
            ->where('status', 1)
            ->where('company_id', $com_id)
            ->where('is_leaf', 1)
            ->where('parent_id', '>', 0)
            // ->select(['id', 'parent_id', 'type_name'])
            ->get();

        // dd($entries->toArray());

        $p_entries = Topic::where('status', 1)
            ->where('company_id', $com_id)
            ->where('is_leaf', 0)
            ->select(['id', 'parent_id'])
            ->get();

        $p_list = [];
        foreach($p_entries as $p){
            $p_list[$p->id] = $p->parent_id;                
        }

        // dd($p_list);

        $list = [];
        foreach($entries as $c){
            $a = self::findUntilReachRoot($c, $p_list);
            // $pivot = DB::table('topic_dimension')->where('topic_id', $c->id)
            //             ->where('company_id', $com_id)
            //             ->get();
            $list[$a[0]] = [
                'topics' => array_reverse($a),
                'type' => strtolower($c->topic_type->type_name),
                'dimensions' => self::getTopicDimensions($c->dimensions),
                
                'amount_list' => []
            ];
        }
        // dd($list);
        
        return $list;        
    }

    private static function findUntilReachRoot($child, $p_list){
        $a = [];
        $a[] = $child->id;
        $pid = $child->parent_id;
        $a[] = $pid;
        while($p_list[$pid] != 0){
            // if($p_list[$pid] == 0)
            //     break;
            $a[] = $p_list[$pid];
            $pid = $p_list[$pid];
                
        }
        
        return $a;
    }

    private static function getTopicDimensions($dim_list){
        // $pivot = DB::table('topic_dimension')->where('topic_id', $topic_id)
        //             ->where('company_id', $com_id)
        //             ->get();
        $a = [];
        foreach($dim_list as $item){
            $a[] = $item->dim_code;
        }

        return $a;
    }

    // private static function findUntilReachRoot($child){
    //     $a = [];
    //     $a[] = $child->id;
    //     do{
    //         $p = $child->parent()->first();
    //         if($p == null)
    //             break;

    //         $a[] = $p->id;
    //         $child = $p;
                
    //     }while(true);
        
    //     return $a;
    // }

    // private static function findUntilReachLeaf($parent, $ids=[]){
        
    //     if($parent->is_leaf == 1){
    //         return [];
    //     }
       
    //     $children = $parent->children()->get();
        
    //     $list = [];
    //     foreach($children as $child){

    //         $tmp = $ids;
    //         $rs = self::findUntilReachLeaf($child, $tmp);
    //         if(empty($rs)){
    //             $tmp[] = $child->id;
    //             $list[] = $tmp;
    //         }
    //         else{
    //             $list[] = $rs;
    //         }
            
                  
        
    //     }
        
    //     return $list;
    // }
}