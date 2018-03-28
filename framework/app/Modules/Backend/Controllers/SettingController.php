<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Http\Request;
use App\Libs\Utils\Vii;

class SettingController extends Controller{

    const LANG_NAME = 'setting';

    public function __construct(){
        parent::__construct();
    }

    public function getTruncateTable(Request $request){

        $table_names = [];
        $table_names[] = (new \App\Models\Ledger())->getTable();
        $table_names[] = (new \App\Models\UploadLedger())->getTable();
        $table_names[] = (new \App\Models\UploadRevision())->getTable();
        $table_names[] = 'ledger_item';
        $table_names[] = (new \App\Models\Account())->getTable();
        $table_names[] = (new \App\Models\Dimension())->getTable();
        

        // dd($table_names);

        $qs = Vii::queryStringBuilder($request->getQueryString());

        return view(
            'Backend::setting.truncate-table',
            [
                'form_uri' => route('truncate-table-post'),
                'page_title' => 'Setting',
                'table_names' => $table_names,
                'qs' => $qs
            ]
        );
    }

    public function postTruncateTable(Request $request){
        // dd($request->post('table'));
        $tables = $request->post('table', []);

        $qs = Vii::queryStringBuilder($request->getQueryString());

        if(count($tables) > 0){
            foreach($tables as $t){
                \Illuminate\Support\Facades\DB::table($t)->truncate();
            }

            return redirect()->route('truncate-table', [str_replace('?', '', $qs)])
            ->with('success-message', 'Truncate tables successfully.');
        }
        
        return redirect()->route('truncate-table', [str_replace('?', '', $qs)])
            ->with('warning-message', 'No tables truncated.');

    }
}