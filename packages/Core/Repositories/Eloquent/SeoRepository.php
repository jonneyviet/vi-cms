<?php

namespace Packages\Core\Repositories\Eloquent;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Application;
use Illuminate\Database\Eloquent\Collection;
use League\Flysystem\Exception;
use Packages\Core\Repositories\BaseRepository;
use Carbon\Carbon;
use Validator;
use Auth;
use Packages\Core\Models\Seo;
use Packages\Core\Repositories\Contracts\SeoRepositoryInterface;

class SeoRepository extends BaseRepository implements SeoRepositoryInterface{
     /**
     *  Validator
     *
     *  @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     *  Validation errors.
     *
     *  @var \Illuminate\Support\MessageBag
     */
    protected $errors;

	public function __construct(Application $app)
    {
        parent::__construct($app);
    }
    public function getModel()
    {
        return Seo::class;
    }
    public function createSeo($name){
        $link=$this->link($name);
        $data=[
            "title" =>$name,
            "link"  =>$link,
        ];
        return $this->create($data);
    }
    private function link($name){
        $slug=vi_helpers_to_slug($name);
        while($this->_model->where("link",$slug)->exists()){
            $slug=$slug."-".str_random(10);
        }
        return $slug;
    }
}