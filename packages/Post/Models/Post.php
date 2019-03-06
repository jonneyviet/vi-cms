<?php

namespace Packages\Post\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
     /**
     *  Table name in the database.
     *  @var string
     */
    protected $table = 'post';

    /**
     *  List of variables that can be mass assigned
     *  @var array
     */
    protected $fillable = ['category_id'];
    public function avatar(){
        return $this->hasOne('Packages\Media\Models\FileUpload','id','avatar');
    }
    public function category(){
        return $this->belongsTo('Packages\Category\Models\category');
    }
}
