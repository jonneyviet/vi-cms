<?php

namespace Packages\Media\Repositories\Eloquent;

use Illuminate\Support\Facades\Storage;
use Packages\Media\Models\Folder;

class MediaRepository{
    protected $folder;
    public function __construct(Folder $folder)
    {
        $this->folder=$folder;
    }


}
