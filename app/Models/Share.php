<?php

namespace App\Models;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Share extends Model
{
    use HasFactory;

    public function file(){
        return $this->hasOne(File::class, 'id', 'file_id');
    }

    public function folder(){
        return $this->hasOne(Folder::class, 'id', 'folder_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'shared_by');
    }

    protected $fillable = [
        'file_id',
        'folder_id',
        'role_id',
        'shared_at',
        'shared_by',
    ];
}
