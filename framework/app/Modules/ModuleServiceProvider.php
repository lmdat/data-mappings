<?php   namespace App\Modules;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider{

    public function register(){}

    public function boot(){
        $modules = config('module');

        $mod = $modules['frontend']['folder_name'];
        $base_url = request()->getBaseUrl();

        $backend_slug = $modules['backend']['slug_name'];
        //base url end with cpanel or cpanel/
        $cpanel_pattern = '/(\/'.$backend_slug.'|\/'.$backend_slug.'\/)$/';

        $api_slug = $modules['api']['slug_name'];
        //base url end with api or api/
        $api_pattern = '/(\/'.$api_slug.'|\/'.$api_slug.'\/)$/';

        if(preg_match($cpanel_pattern, $base_url, $match)) {
            $mod = $modules['backend']['folder_name'];
        }
        elseif(preg_match($api_pattern, $base_url, $match)){
            $mod = $modules['api']['folder_name'];
        }
        else{
            $mod = $modules['frontend']['folder_name'];
        }

        if(file_exists(__DIR__ . '/' . $mod . '/routes.php')){
            include __DIR__ . '/' . $mod . '/routes.php';
        }

        if(is_dir(__DIR__ . '/' . $mod . '/Views')){

            // if($mod == $modules['backend']['folder_name']){
            //     $this->loadViewsFrom(__DIR__ . '/' . $mod . '/Views', $mod);
            // }
            // else{
            //     $this->loadViewsFrom(__DIR__ . '/' . $mod . '/Views/themes/' . Theme::get(), $mod);
            // }

            $this->loadViewsFrom(__DIR__ . '/' . $mod . '/Views', $mod);

        }
    }
}