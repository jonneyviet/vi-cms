<?php

namespace Packages\Category\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Packages\Media\Models\FileUpload as FileUpload;

class Category extends Model
{
     /**
     *  Table name in the database.
     *  @var string
     */
    protected $table = 'category';

    /**
     *  List of variables that can be mass assigned
     *  @var array
     */
    protected $fillable = ['created_at', 'updated_at'];
    protected $hidden=['created_at','updated_at','deleted_at','avatar','add_by','id'];
    protected $appends = ['date','avatar_url'];

    public function children(){
        return $this->hasMany(Category::class, 'parent_id', 'id' );
    }
    public function parent(){
        return $this->hasOne(Category::class, 'id', 'parent_id' );
    }
    public function posts(){
        return $this->hasMany("Packages\Post\Models\Post");
    }
    public function avatar(){
        return $this->hasOne('Packages\Media\Models\FileUpload','id','avatar');
    }
    public function getDateAttribute()
    {
        $dt = Carbon::createFromFormat('Y-m-d H:i:s',$this->created_at);
        return $dt->toIso8601String();
    }
    public function getAvatarUrlAttribute()
    {
        $path=FileUpload::where("id",$this->avatar)->value("path");
         if(is_null($path)){
             return null;
         }
        return config("filesystems.disks.public.url").'/'.$path;
    }
}
