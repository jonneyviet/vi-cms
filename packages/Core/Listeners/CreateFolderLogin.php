<?php

namespace Packages\Core\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Packages\Core\Events\EventLoginAdmin;
use Packages\Media\Repositories\Contracts\FolderRepositoryInterface;

class CreateFolderLogin
{
    protected $_folder;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(FolderRepositoryInterface $folder)
    {
        $this->_folder  = $folder;
    }

    /**
     * Handle the event.
     *
     * @param  Event  $event
     * @return void
     */
    public function handle(EventLoginAdmin $event)
    {
        // $this->_folder->checkFolderDefault();
        // Log::info("check default Folder success ");
    }
}
