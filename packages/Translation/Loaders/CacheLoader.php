<?php 

namespace Packages\Translation\Loaders;

/**
* 
*/
use Packages\Translation\Cache\CacheRepositoryInterface as Cache;

class CacheLoader extends Loader{
	protected $defaultLocale;

	protected $cache;
	protected $fallback;
	protected $cacheTimeout;

	public function __construct($defaultLocale, Cache $cache, Loader $fallback, $cacheTimeout)
    {
        parent::__construct($defaultLocale);
        $this->cache        = $cache;
        $this->fallback     = $fallback;
        $this->cacheTimeout = $cacheTimeout;
    }
     public function loadSource($locale, $group, $namespace = '*')
    {
        if ($this->cache->has($locale, $group, $namespace)) {
            return $this->cache->get($locale, $group, $namespace);
        } else {
            $source = $this->fallback->load($locale, $group, $namespace);
            $this->cache->put($locale, $group, $namespace, $source, $this->cacheTimeout);
            return $source;
        }
    }
    public function addNamespace($namespace, $hint){

    }
    public function addJsonPath($path){

    }
    public function namespaces(){

    }
}