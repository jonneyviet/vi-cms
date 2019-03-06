<?php

namespace Packages\Translation\Loaders;
use Illuminate\Translation\FileLoader as LaravelFileLoader;

class FileLoader extends Loader
{
	protected $defaultLocale;
	protected $laravelFileLoader;

	public function __construct($defaultLocale, LaravelFileLoader $laravelFileLoader)
    {
        parent::__construct($defaultLocale);
        $this->laravelFileLoader = $laravelFileLoader;
    }
    public function loadSource($locale, $group, $namespace = '*'){
        return $this->laravelFileLoader->load($locale, $group, $namespace);
    }
    public function addNamespace($namespace, $hint)
    {

    }
    public function addJsonPath($path)
    {

    }
    public function namespaces()
    {
        return $this->hints;
    }
}