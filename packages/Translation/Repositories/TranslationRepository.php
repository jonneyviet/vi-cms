<?php
namespace Packages\Translation\Repositories;

/**
* 
*/
use Illuminate\Database\Query\JoinClause;
use Illuminate\Foundation\Application;
use Illuminate\Support\NamespacedItemResolver;
use Packages\Translation\Models\Translation;

class TranslationRepository extends Repository
{
	
	/**
     * @var \Illuminate\Database\Connection
     */
    protected $database;

    /**
     * The model being queried.
     *
     * @var \Waavi\Translation\Models\Translation
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
     *  Constructor
     *  @param  \Waavi\Translation\Models\Translation   $model  Bade model for queries.
     *  @param  \Illuminate\Validation\Validator        $validator  Validator factory
     *  @return void
     */
    public function __construct(Translation $model, Application $app)
    {
        $this->model         = $model;
        $this->app           = $app;
        $this->defaultLocale = $app['config']->get('app.locale');
        $this->database      = $app['db'];
    }
    public function create($attributes){
        return $this->validate($attributes)? Translation::create($attributes):null;
    }
    public function update($id,$text){
        $translation=$this->find($id);
        if(!$translation && $translation->isLocked()){
            return false;
        }
        $translation->text  =$text;
        $saved              =$translation->save();
        if($saved && $translation->locale===$this->defaultLocale){
           $this->flagAsUnstable($translation->namespace, $translation->group, $translation->item);
        }
        return $saved;
    }
    public function updateAndLock($id,$text){
        $translation=$this->find($id);
        if(!$translation){
            return false;
        }
        $translation->text  =$text;
        $translation->lock();
        $saved              =$translation->save();
        if($saved && $translation->locale===$this->defaultLocale){
           $this->flagAsUnstable($translation->namespace, $translation->group, $translation->item);
        }
        return $saved;
    }
    public function updateDefaultByCode(){

    }
    public function delete($id){

    }
    public function deleteByCode($code){

    }
	/**
     *  Loads a localization array from a localization file into the databas.
     *
     *  @param  array   $lines
     *  @param  string  $locale
     *  @param  string  $group
     *  @param  string  $namespace
     *  @return void
     */
    public function loadArray(array $lines, $locale, $group, $namespace = '*')
    {
        // Transform the lines into a flat dot array:
        $lines = array_dot($lines);
        foreach ($lines as $item => $text) {
            if (is_string($text)) {
                // Check if the entry exists in the database:
                $translation = Translation::whereLocale($locale)
                    ->whereNamespace($namespace)
                    ->whereGroup($group)
                    ->whereItem($item)
                    ->first();

                // If the translation already exists, we update the text:
                if ($translation && !$translation->isLocked()) {
                    $translation->text = $text;
                    $saved             = $translation->save();
                    if ($saved && $translation->locale === $this->defaultLocale) {
                        $this->flagAsUnstable($namespace, $group, $item);
                    }
                }
                // If no entry was found, create it:
                else {
                    //dd(compact('locale', 'namespace', 'group', 'item', 'text'));
                    $this->create(compact('locale', 'namespace', 'group', 'item', 'text'));
                }
            }
        }
    }
    public function validate(array $attributes){
        $table      =$this->model->getTable();
        $locale     =array_get($attributes,"locale");
        $namespace  =array_get($attributes,"namespace");
        $group      =array_get($attributes,"group");
        $rules      =[
            "locale"=>"required",
            "namespace"=>"required",
            'item'      => "required|unique:{$table},item,NULL,id,locale,{$locale},namespace,{$namespace},group,{$group}",
            'text'      => '', // Translations may be empty
        ];
        $validator=$this->app['validator']->make($attributes,$rules);
        if($validator->fails()){
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }
    public function validationErrors()
    {
        return $this->errors;
    }
    /**
     *  Flag all entries with the given namespace, group and item and locale other than default as pending review.
     *  This is used when an entry for the default locale is updated.
     *
     *  @param Translation $entry
     *  @return boolean
     */
    public function flagAsUnstable($namespace, $group, $item)
    {
        $this->model->whereNamespace($namespace)->whereGroup($group)->whereItem($item)->where('locale', '!=', $this->defaultLocale)->update(['unstable' => '1']);
    }
    //get all translation to locale 
    public function allByLocale($locale, $perPage = 0){
        $translation = $this->model->where("locale",$locale);
        return $perPage ? $translation->paginate($perPage): $translation->get();
    }
    public function getItem($locale,$namespace,$group){
        return $this->model
                    ->whereLocale($locale)
                    ->whereNamespace($namespace)
                    ->whereGroup($group)
                    ->get()
                    ->toArray();
    }
     /**
     *  Return all items formatted as if coming from a PHP language file.
     *
     *  @param  string $locale
     *  @param  string $namespace
     *  @param  string $group
     *  @return array
     */
    public function loadSource($locale, $namespace, $group){
        return $this->model
            ->whereLocale($locale)
            ->whereNamespace($namespace)
            ->whereGroup($group)
            ->get()
            ->keyBy('item')
            ->map(function ($translation) {
                return $translation['text'];
            })
            ->toArray();
    }
    /**
     *  Retrieve translations pending review for the given locale.
     *
     *  @param  string  $locale
     *  @param  int     $perPage    Number of elements per page. 0 if all are wanted.
     *  @return Translation
     */
    public function pendingReview($locale, $perPage = 0)
    {
        $underReview = $this->model->whereLocale($locale)->whereUnstable(1);
        return $perPage ? $underReview->paginate($perPage) : $underReview->get();
    }
    /**
     *  Search for entries given a partial code and a locale
     *
     *  @param  string  $locale
     *  @param  string  $partialCode
     *  @param  integer $perPage        0 if all, > 0 if paginated list with that number of elements per page.
     *  @return Translation
     */
    public function search($locale, $partialCode, $perPage = 0)
    {
        // Get the namespace, if any:
        $colonIndex = stripos($partialCode, '::');
        $query      = $this->model->whereLocale($locale);
        if ($colonIndex === 0) {
            $query = $query->where('namespace', '!=', '*');
        } elseif ($colonIndex > 0) {
            $namespace   = substr($partialCode, 0, $colonIndex);
            $query       = $query->where('namespace', 'like', "%{$namespace}%");
            $partialCode = substr($partialCode, $colonIndex + 2);
        }

        // Divide the code in segments by .
        $elements = explode('.', $partialCode);
        foreach ($elements as $element) {
            if ($element) {
                $query = $query->where(function ($query) use ($element) {
                    $query->where('group', 'like', "%{$element}%")->orWhere('item', 'like', "%{$element}%")->orWhere('text', 'like', "%{$element}%");
                });
            }
        }

        return $perPage ? $query->paginate($perPage) : $query->get();
    }
}










