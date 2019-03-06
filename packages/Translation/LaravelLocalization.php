<?php

namespace Packages\Translation;

/**
* 
*/

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Packages\Translation\UriLocalizer;
class LaravelLocalization
{
	protected  $app;
    protected  $config;
    protected $defaultLocale;
    protected $currentLocale=false;
    protected $supportedLocales;

	public function __construct()
    {
    	$this->app=app();
        $this->request=$this->app["request"];
        $this->config = $this->app['config'];
        $this->defaultLocale=$this->config->get('app.locale');
        $supportedLocales = $this->getSupportedLocales();
        if (!in_array($this->defaultLocale,$supportedLocales)) {
           dd("erors line 27 LaravelLocalization");
            //throw new UnsupportedLocaleException('Laravel default locale is not in the supportedLocales array.');
        }
    }
	public function setLocale($locale = null)
    {
    	if (empty($locale) || !is_string($locale)){
            $locale = $this->request->segment(1);
        }
        if (in_array($locale,$this->supportedLocales)) {
            $this->currentLocale = $locale;
        } else {
            $locale=null;
            $this->currentLocale = $this->defaultLocale;
        }
        //$this->app->setLocale($this->currentLocale);

        return $locale;
    }
    public function getSupportedLocales()
    {
        if (!empty($this->supportedLocales)) {
            return $this->supportedLocales;
        }

        $locales = $this->config->get('translation.available_locales');
        if (empty($locales) || !is_array($locales)) {
            dd("erors line 55 LaravelLocalization");
        }
        foreach ($locales as $key => $value) {
            array_push($locales, $key);
        }
        $this->supportedLocales = $locales;

        return $locales;
    }
    public function getCurrentLocale()
    {
        if ($this->currentLocale) {
            return $this->currentLocale;
        }
    }
    public function getType(){
        $locale = $this->request->segment(1);
        if (in_array($locale,$this->supportedLocales)) {
            $type = $this->request->segment(2);
        } else {
           $type=$locale;
        }
        if($type===$this->config->get("core.admin-prefix")){
            return "admin";
        }
        if($type==="api"){
            return "api";
        }
        return "web";
    }
}