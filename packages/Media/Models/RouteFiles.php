<?php

namespace Packages\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class RouteFiles extends Model
{
    /**
     *  Table name in the database.
     *  @var string
     */
    protected $table = 'route_files';

    /**
     *  List of variables that can be mass assigned
     *  @var array
     */
    protected $fillable = ['media_files_id','category_id','post_id'];
    public $timestamps = false;
}
