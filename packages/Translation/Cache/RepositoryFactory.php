<?php 

namespace Packages\Translation\Cache;

use Illuminate\Contracts\Cache\Store;

class RepositoryFactory
{
    public static function make(Store $store, $cacheTag)
    {
        return new SimpleRepository($store, $cacheTag);
    }
}
