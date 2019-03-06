<?php
namespace Packages\Translation\Loaders;

/**
* 
*/
use Packages\Translation\Repositories\TranslationRepository;

class DatabaseLoader extends Loader
{
	protected $defaultLocale;
	protected $translationRepository;

	function __construct($defaultLocale,TranslationRepository $translationRepository)
	{
		parent::__construct($defaultLocale);
		$this->translationRepository	= $translationRepository;
	}

	public function loadSource($locale, $group, $namespace = null){
		$dotArray = $this->translationRepository->loadSource($locale, $namespace, $group);
		$undot    = [];
        foreach ($dotArray as $item => $text) {
            array_set($undot, $item, $text);
        }
        return $undot;
	}
	public function addNamespace($namespace, $hint){

	}
	public function addJsonPath($path){

	}
	public function namespaces(){

	}
}