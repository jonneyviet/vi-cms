<?php
namespace Packages\Translation\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Packages\Translation\Repositories\LanguageRepository;
use Packages\Translation\Repositories\TranslationRepository;
class FileLoaderCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translator:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Load language files into the database.";

    public function __construct(LanguageRepository $languageRepository, TranslationRepository $translationRepository,Filesystem $files,$tranlsationPath,$defaultLocale){
        parent::__construct();
        $this->languageRepository       =  $languageRepository;
        $this->translationRepository    =  $translationRepository;
        $this->files                    =  $files;
        $this->path                     =  $tranlsationPath;
        $this->defaultLocale            =  $defaultLocale;
    }
    public function handle(){
        return $this->fire();
    }
    public function fire(){
        $this->loadLocaleDerectories($this->path);
    }
    /*
        load file lang to db
     */
    public function loadLocaleDerectories($path,$namespace="*"){
        $this->languageRepository->loadArray();
        $availableLocales    =   $this->languageRepository->availableLocales();//get all language
        $directories        =   $this->files->directories($path);//get directories
        foreach ($directories as $directory) {
           $locale          =   basename($directory);//get name directory
           if(in_array($locale,$availableLocales)){
                $this->loadDirectory($directory, $locale, $namespace);
           }
        }
    }
    public function loadDirectory($path, $locale, $namespace = '*', $group = ''){
        //load all files in sub folder
        $directories    =   $this->files->directories($path);
        foreach ($directories as $directory){
            $directoryName = str_replace($path . '/', '', $directory);
            $dirGroup      = $group . basename($directory) . '/';
            $this->loadDirectory($directory, $locale, $namespace, $dirGroup);
        }
        //load all files in root
        $files      = $this->files->files($path);
        //dd($files);
        foreach($files as $file) {
            $this->loadFile($file, $locale, $namespace, $group);
        }
    }
    public function loadFile($file, $locale, $namespace = '*', $group = '')
    {
        $group        = $group . basename($file, '.php');
        $translations = $this->files->getRequire($file);
        $this->translationRepository->loadArray($translations, $locale, $group, $namespace, $locale == $this->defaultLocale);//save to database;
    }
}















