<?php

namespace App\Helpers;

use App\Models\File;
use App\Models\User;
use App\Models\Avatar;
use App\Models\Folder;
use Illuminate\Support\Arr;
use Spatie\Activitylog\Models\Activity;
use Rolandstarke\Thumbnail\Facades\Thumbnail;

class DDSS
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

    /**
     * Requires an Activity model
     * Returns a styled description of the activity
     * 
     * Must be used within this {!!  !!}
     * Idk what that's called lmao
     */
    public static function activity(Activity $activity)
    {
        $folder_icon = '<i class="fas fa-folder text-yellow mx-1"></i>';
        
        if($activity->subject_type == "App\Models\File"){
            if($activity->event == "renamed"){
                $desc = $activity->description.' from '.'<strong>'.
                $activity->getExtraProperty('old_name').'</strong>'.' to '.'<strong>'.
                $activity->getExtraProperty('new_name').'</strong>';
            } else {
                $desc = $activity->description.': '.'<strong>'.
                $activity->getExtraProperty('name').'</strong>';
            }
        } else {
            if($activity->event == "renamed"){
                $desc = $activity->description.' from '.'<strong>'.
                $activity->getExtraProperty('old_name').'</strong>'.' to '.'<strong>'.
                $activity->getExtraProperty('new_name').'</strong>';
            } else {
                $desc = $activity->description.': '.$folder_icon.
                '<strong>'.$activity->getExtraProperty('name').'</strong>';
            }
        }

        return $desc;
    }

    /**
     * Returns thumbnail
     */
    public static function file_thumb(File $file)
    {
        $image = ['png', 'jpg', 'jpeg', 'bmp'];

        $ext = pathinfo(storage_path($file->filePath), PATHINFO_EXTENSION);

        $default = public_path('images/filetypes/'.$ext.'.png');

        if(in_array($ext, $image)){
            return Thumbnail::src('/'.$file->filePath, 'public')->crop(200, 200)->url();
        } else {
            return Thumbnail::src($default)->crop(200,200)->url();
        }
    }

    /**
     * Getting root folder
     * 
     * Will use this when needed lmao
     */
    public static function getRootFolder(Folder $folder)
    {
        $parent_folders = [];

        while (1) {
            if ($folder->parent_folder_id) {
                $folder = Folder::find($folder->parent_folder_id);
                $parent_folders[] = $folder;
            } else {
                break;
            }
        }

        $return = array_reverse($parent_folders);

        // dd(array_reverse($parent_folders));
        // $bread = '';

        // foreach ($return as $parent) {
        //     $bread .= $parent->folderName; 
        // }

        // return $bread;
    }
}