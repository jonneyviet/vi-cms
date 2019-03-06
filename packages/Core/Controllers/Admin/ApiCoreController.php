<?php

namespace Packages\Core\Controllers\Admin;

use Packages\Core\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Packages\Core\Models\Users;
use Auth;
use Packages\Core\Repositories\Contracts\CoreRepositoryInterface;
class ApiCoreController extends Controller
{
    protected $coreRepository;
    public function __construct(CoreRepositoryInterface $coreRepository)
    {
        $this->coreRepository=$coreRepository;
    }
}
