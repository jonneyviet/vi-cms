<?php

namespace Packages\Media\Models;

use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
     /**
     *  Table name in the database.
     *  @var string
     */
    protected $table = 'media_files';

    /**
     *  List of variables that can be mass assigned
     *  @var array
     */
     protected $fillable = ['name','share','type', 'path','add_by','description',"created_at","updated_at","deleted_at"];
     
}
