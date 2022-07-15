<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Avatar;
use Rolandstarke\Thumbnail\Facades\Thumbnail;

class AvatarHelper
{
    /**
     * Requires the id of the user to get the avatar of,
     * and the desired width of the avatar thumbnail
     * 
     * Returns an instance rolandstarke/laravel-thumbnail of the avatar if it exists
     * Otherwise, returns Thumbnail of default avatar
     */
    public static function getAvatar($id, $width)
    {
        $user = User::find($id);
        $avatar = Avatar::where('user_id','=',$id)->latest()->first();
        $default = public_path('images/default_avatar.jpg');

        if($user->avatar != NULL){
            return Thumbnail::src('/'.$avatar->path, 'public')->crop($width,$width)->url();
        } else {
            return Thumbnail::src($default)->crop($width,$width)->url();
        }
    }
}