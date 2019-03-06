<?php

namespace Packages\Translation\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
     /**
     *  Table name in the database.
     *  @var string
     */
    protected $table = 'translator_translations';

    /**
     *  List of variables that can be mass assigned
     *  @var array
     */
    protected $fillable = ['locale', 'namespace', 'group', 'item', 'text', 'unstable'];

    /**
     *  Each translation belongs to a language.
     */
    public function language()
    {
        return $this->belongsTo(Language::class, 'locale', 'locale');
    }
    public function lock(){
        return $this->locked=1;
    }
    public function isLocked(){
        return (boolean)$this->lock();
    }
}
