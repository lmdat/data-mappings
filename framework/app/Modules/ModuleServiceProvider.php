<?php   namespace App\Modules;

use Illuminate\Support\Facades\Route;
// use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class ModuleServiceProvider extends ServiceProvider{

    public function register(){}

    public function boot(){
        parent::boot();        
    }

    public function map(){
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
        
        $this->namespace = join('\\', ['App', 'Modules', $mod, 'Controllers']);
        

        // include __DIR__ . '/' . $mod . '/routes.php';
        // $this->loadRoutesFrom(__DIR__ . '/' . $mod . '/routes.php');
        if($mod == $modules['api']['folder_name']){
            $this->mapApiRoutes($mod);
        }
        else{
            $this->mapWebRoutes($mod);
        }
        
    }

    protected function mapApiRoutes($mod){
        $route_dir = implode(DIRECTORY_SEPARATOR, [__DIR__,  $mod, 'Routes']);
        $entries = scandir($route_dir);
        foreach($entries as $f){
            if($f == '.' || $f == '..')
                continue;
            
            $route_file = implode(DIRECTORY_SEPARATOR, [__DIR__,  $mod, 'Routes', $f]);
            $b = explode('.', $f);
            Route::prefix($b[0])
                ->namespace($this->namespace)
                ->group($route_file);
        }
    }

    protected function mapWebRoutes($mod){
        $view_dir = implode(DIRECTORY_SEPARATOR, [__DIR__,  $mod, 'Views']);

        $route_file = implode(DIRECTORY_SEPARATOR, [__DIR__,  $mod, 'Routes', 'web.php']);
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group($route_file);

        if(is_dir($view_dir)){
            // if($mod == $modules['backend']['folder_name']){
            //     $this->loadViewsFrom(__DIR__ . '/' . $mod . '/Views', $mod);
            // }
            // else{
            //     $this->loadViewsFrom(__DIR__ . '/' . $mod . '/Views/themes/' . Theme::get(), $mod);
            // }

            $this->loadViewsFrom($view_dir, $mod);
        }
    }
}