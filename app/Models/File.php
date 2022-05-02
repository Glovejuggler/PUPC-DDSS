<?php

namespace App\Models;

use App\Models\User;
use App\Models\Share;
use App\Models\Folder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'fileName',
        'filePath',
        'folder_id',
        'user_id',
    ];
    
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function folder(){
        return $this->belongsTo(Folder::class);
    }
}
