<?php

namespace Packages\Translation\Commands;


/**
* 
*/
use Illuminate\Console\Command;
use Packages\Translation\Cache\CacheRepositoryInterface as CacheRepository;

class CacheFlushCommand extends Command
{
	
	protected $name="translator:flush";

	protected $description="Flush the translation cache";

	public function __construct(CacheRepository $cacheRepository,$cacheEnabled){
		parent::__construct();
		$this->cacheRepository 			= 		$cacheRepository;
		$this->cacheEnabled 			=		$cacheEnabled;
	}
	public function handle(){
		$this->fire();
	}
	public function fire(){
		if(!$this->cacheEnabled){
			$this->info("The translation cache is disable.");
		}else{
			$this->cacheRepository->flushAll();
			$this->info("The translation cache cleared.");
		}
	}
}