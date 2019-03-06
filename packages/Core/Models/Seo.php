<?php

namespace Packages\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
     /**
     *  Table name in the database.
     *  @var string
     */
    protected $table = 'seo';

    /**
     *  List of variables that can be mass assigned
     *  @var array
     */
     protected $fillable = ['name','link','title','description',"created_at","updated_at","deleted_at"];
 }
