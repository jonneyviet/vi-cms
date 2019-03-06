<?php

namespace Packages\Core\Controllers\Api;

use Packages\Core\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Packages\Core\Models\Users;
use Auth;
use Packages\Core\Repositories\Contracts\CoreRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ApiCoreController extends Controller
{
    protected $coreRepository;
    public function __construct(CoreRepositoryInterface $coreRepository)
    {
        $this->coreRepository=$coreRepository;
    }
    public function setting(Request $request){
        $strings = Cache::rememberForever('setting.js', function () {
            $strings = [];
            $strings=array_add($strings,'locale',config('app.locale'));
            $strings=array_add($strings,'langList',config("translation.available_locales"));
            $strings=array_add($strings,"typeCategory",config("category.type_category"));
            $strings=array_add($strings,"sizeAvatar",config("category.sizeAvatar"));
            return $strings;
        });

        header('Content-Type: text/javascript');
        echo('window.i18n_setting = ' . json_encode($strings) . ';');
        exit();
    }
}
