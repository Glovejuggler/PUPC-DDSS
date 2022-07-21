<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Share;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Database\Eloquent\Builder;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Gate::allows('do-admin-stuff')){
            if(request('show_deleted') == 1){
                $folders = Folder::onlyTrashed()->get();
            } else {
                $folders = Folder::all();
            }
        } else {
            $folders = Folder::wherehas('user', function(Builder $query){
                $query->where('role_id','=',Auth::user()->role_id);
            })->get();
        }

        return view('folders.index', compact('folders'));
    }

    /** 
     * Recovers the whole folder (including the files) from trash
     * But it does not recover the files deleted before the folder is deleted
     * 
     * Got me? Kinda confusing, right? But's that's how it is :omegalul:
     */
    public function recover($id)
    {
        $folder = Folder::onlyTrashed()->find($id);
        
        File::withTrashed()
            ->where('folder_id','=',$id)
            ->whereBetween('deleted_at', [$folder->deleted_at, now()])
            ->restore();
        $folder = Folder::withTrashed()->find($id);
        $folder->restore();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($folder)
            ->event('restored')
            ->withProperties(['name' => $folder->folderName])
            ->log('restored a folder');

        return redirect()->back()->with('toast_success', 'Folder restored');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $folder = new Folder;

        $folder->folderName = $request->folderName;
        $folder->user_id = Auth::user()->id;
        $folder->parent_folder_id = $request->parent_folder_id;
        if(!Storage::exists($request->folderName)){
            Storage::disk('public')->makeDirectory($request->folderName);
        }

        $folder->save();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($folder)
            ->event('created')
            ->withProperties(['name' => $folder->folderName])
            ->log('created a folder');

        return redirect()->back()->with('toast_success', 'Folder added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $folder = Folder::findorfail($id);
        $files = File::where('folder_id','=',$id)->get()    ;

        return view('folders.view', compact('folder', 'files'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(Storage::disk('public')->exists($request->folderName)){
            return redirect()->back()->with('toast_error', 'Folder already exists');
        }

        $folder = Folder::find($id);

        $oldDir = $folder->folderName;
        $folder->folderName = $request->folderName;

        $files = File::where('folder_id','=',$id)->get();
        foreach($files as $file){
            $file->filePath = $folder->folderName.'/'.$file->fileName;
            $file->update();
        }

        $folder->update();
        Storage::disk('public')->move($oldDir, $folder->folderName);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($folder)
            ->event('renamed')
            ->withProperties(['old_name' => $oldDir, 'new_name' => $folder->folderName])
            ->log('renamed a folder');

        return redirect()->back()->with('toast_success', 'Folder renamed');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function destroy(Folder $folder)
    {
        // This permanently deletes the folder in the storage including the files
        // if(Storage::disk('public')->exists('files/'.$folder->folderName)){
        //     Storage::disk('public')->deleteDirectory('files/'.$folder->folderName);
        // }

        activity()
            ->causedBy(Auth::user())
            ->performedOn($folder)
            ->event('deleted')
            ->withProperties(['name' => $folder->folderName])
            ->log('deleted a folder');

        $folder->delete();
        File::where('folder_id','=',$folder->id)->delete();

        return redirect()->back()->with('toast_success', 'Folder deleted successfully');
    }

    /**
     * Shares the folder to other roles
     */
    public function share(Request $request, $id)
    {
        if($request->has('role_id')) {
            foreach ($request->role_id as $role_id) {
                Share::firstOrCreate([
                    'folder_id' => $id,
                    'role_id' => $role_id,
                ], [
                    'shared_by' => Auth::user()->id,
                    'shared_at' => now(),
                ]);
            }
            Share::where('folder_id','=',$id)->whereNotIn('role_id', $request->role_id)->delete();
        } else {
            Share::where('folder_id','=',$id)->delete();
        }

        return redirect()->back()->with('toast_success', 'Successfully shared folder');
    }
}
