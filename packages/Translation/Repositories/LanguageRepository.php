<?php
namespace Packages\Translation\Repositories;

/**
* 
*/
use Illuminate\Config\Repository as Config;
use Illuminate\Foundation\Application;
use Illuminate\Validation\Factory as Validator;
use Packages\Translation\Models\Language;
use Carbon\Carbon;
class LanguageRepository extends Repository
{
	/**
     * The model being queried.
     *
     * @var \Waavi\Translation\Models\Language
     */
    protected $model;

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

    /**
     *  Default locale.
     *
     *  @var string
     */
    protected $defaultLocale;

    /**
     *  Default available locales in case of filesystem source.
     *
     *  @var string
     */
    protected $defaultAvailableLocales;

    /**
     *  Config repository.
     *
     *  @var Config
     */
    protected $config;


    protected $app;
    /**
     *  Constructor
     *  @param  \Waavi\Translation\Models\Language      $model  Bade model for queries.
     *  @param  \Illuminate\Validation\Validator        $validator  Validator factory
     *  @return void
     */

	public function __construct(Language $model, Application $app)
    {
        $this->model                   = $model;
        $config                        = $app['config'];
        $this->defaultLocale           = $config->get('app.locale');
        $this->defaultAvailableLocales = array_keys($config->get('translation.available_locales', []));
        $this->config                  = $config;
        $this->app                     = $app;
    }
    /**
     *  Created Language
     *
     *  @return boolean
     */
    public function create($attribute){
        return $this->validate($attribute) ? Language::create($attribute):null;
    }
    /**
     *  Insert a new language entry into the database.
     *  If the attributes are not valid, a null response is given and the errors can be retrieved through validationErrors()
     *
     *  @param  array   $attributes     Model attributes
     *  @return boolean
     */
    public function update(array $attributes)
    {
        return $this->validate($attributes)? (boolean) Language::where("id",$attributes["id"])->update($attribute) : false;
    }


    /**
     *  Returns a list of all available locales.
     *
     *  @return array
     */
    public function availableLocales()
    {
        if ($this->config->has('translation.locales')) {
            return $this->config->get('translation.locales');
        }
        if ($this->config->get('translation.source') !== 'files') {
            if ($this->tableExists()) {
                $locales = $this->model->distinct()->get()->pluck('locale')->toArray();
                $this->config->set('translation.locales', $locales);
                return $locales;
            }
        }
        return $this->defaultAvailableLocales;
    }
    /**
        * Find by Locale
        * return Language
        * @return string

    **/
    public function finbyLocale($locale){
        return $this->model->where('locale', $locale)->first();
    }
    public function allExcept($locale){
        return $this->model->where("locale","!=",$locale)->get();
    }
    /**
     *  Checks if a language with the given locale exists.
     *
     *  @return boolean
     */
    public function isValidLocale($locale)
    {
        return $this->model->whereLocale($locale)->count() > 0;
    }

   /**
     *  Validate the given attributes
     *
     *  @param  array    $attributes
     *  @return boolean
     */
    public function validate(array $attributes)
    {
        $id    = array_get($attributes, 'id', 'NULL');
        $table = $this->model->getTable();
        $rules = [
            'locale' => "required|unique:{$table},locale,{$id}",
            'name'   => "required|unique:{$table},name,{$id}",
        ];
        $validator = $this->app['validator']->make($attributes, $rules);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }
    public function validationErrors()
    {
        return $this->errors;
    }
    public function loadArray(){
        $lines =$this->config->get('translation.available_locales', []);
        foreach($lines as $key=>$line) {
            $created_at=Carbon::now()->format('Y-m-d H:i:s');
            $updated_at=Carbon::now()->format('Y-m-d H:i:s');
            $name=$line['name'];
            $locale=$key;
            $this->create(compact('locale', 'name','created_at','updated_at'));
        }
    }
}









