<?php

namespace App\Models;

use App\Models\User;
use App\Models\Folder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Folder extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function children(){
        return $this->hasMany(Folder::class, 'parent_folder_id');
    }

    public function parent()
    {
        return $this->hasOne(Folder::class, 'id','parent_folder_id')
                    ->withDefault(['folderName' => 'Root']);
    }
}
