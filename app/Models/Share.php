<?php

namespace App\Models;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Share extends Model
{
    use HasFactory;

    public function file(){
        return $this->hasMany(File::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'file_id',
        'role_id',
        'shared_at',
        'shared_by',
    ];
}
