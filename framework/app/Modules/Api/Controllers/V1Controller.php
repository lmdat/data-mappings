<?php
namespace App\Modules\Api\Controllers;


use Illuminate\Http\Request;

use App\Models\Account;
use App\Models\Dimension;
use App\Models\Ledger;
use App\Models\Topic;

use App\Libs\Utils\Vii;

class V1Controller extends Controller{
    
    public function __construct(){
        parent::__construct();
        // $this->middleware(['jwt.auth']);
    }

    public function getLedgers(Request $request){

        $company_id = $request->input('company_id', 1);
        $ledger_code = $request->input('ledger_code', 'A');

        // $leaf_items = Topic::where('is_leaf', 1);
        $entries = Topic::createParentToChildren($company_id);
        
        $list = [];
        // Ledger::where('company_id', $company_id)
        // Ledger::with(['topics' => function($q){$q->select(['topic.id']);}])
        Ledger::with(['topics' => function($q){
            $q->select(['topic.id'])->distinct();
        }])
            ->where('company_id', $company_id)
            ->where('ledger_code', $ledger_code)
            ->chunk(500, function($subset) use(&$entries){
                foreach($subset as $ledger){
                    // foreach($ledger->topics()->select('topic.id')->get() as $topic){
                    foreach($ledger->topics as $topic){
                        if(array_key_exists($topic->id, $entries)){
                            // $entries[$topic->id]['amount_list'][] = $ledger->base_amount . ' | ' . $ledger->ledger_key;
                            $entries[$topic->id]['amount_list'][] = $ledger->base_amount;
                        }
                        
                    }
                    
                }
            });

        // dd($entries);
        // return response()->json(array_values($entries), 200, [], JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
        return response()->json(array_values($entries), 200, [], JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
    }
}