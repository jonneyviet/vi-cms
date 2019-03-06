<?php

namespace Packages\Media\Repositories\Contracts;

interface FileRepositoryInterface
{
    public function uploadImage($files,$name=null);
    public function getPathFile($id);
}