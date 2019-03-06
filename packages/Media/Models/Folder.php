<?php

namespace Packages\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Folder extends Model
{
     /**
     *  Table name in the database.
     *  @var string
     */
    protected $table = 'media_folders';

    /**
     *  List of variables that can be mass assigned
     *  @var array
     */
    protected $fillable = ['name','parent_id','share', 'path','add_by','description',"created_at","updated_at","deleted_at"];

   public function parent(){
        return $this->belongsTo(Folder::class,'parent_id');
   }
   public function children(){
        return $this->hasMany(Folder::class,'parent_id');
    }
    public static function tree() {
        return static::with(implode('.', array_fill(0, 100, 'children')))->where('parent_id', null)->get();
    }
}
