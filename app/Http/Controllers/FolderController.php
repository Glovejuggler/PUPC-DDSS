<?php

namespace App\Http\Controllers;

use App\Models\File;
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

    public function recover($id)
    {
        $folder = Folder::onlyTrashed()->find($id);
        
        File::withTrashed()
            ->where('folder_id','=',$id)
            ->whereBetween('deleted_at', [$folder->deleted_at, now()])
            ->restore();
        Folder::withTrashed()->find($id)->restore();

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
        if(!Storage::exists('files/'.$request->folderName)){
            Storage::disk('public')->makeDirectory('files/'.$request->folderName);
        }
        

        $folder->save();

        return redirect()->route('folder.index')->with('toast_success', 'Folder added successfully');
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
        //
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
        $folder->delete();
        File::where('folder_id','=',$folder->id)->delete();

        return redirect()->route('folder.index')->with('toast_success', 'Folder deleted successfully');
    }
}
