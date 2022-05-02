<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use App\Models\Share;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Database\Eloquent\Builder;

class FileController extends Controller
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
                $files = File::onlyTrashed()->get();
                $folders = Folder::all();
            } else {
                $files = File::all();
                $folders = Folder::all();
            }
        } else {
            $files = File::wherehas('user', function(Builder $query){
                $query->where('role_id','=',Auth::user()->role_id);
            })->get();

            $folders = Folder::wherehas('user', function(Builder $query){
                $query->where('role_id','=',Auth::user()->role_id);
            })->get();
        }

        return view('files.index', compact('files', 'folders'));
    }

    public function recover($id)
    {
        File::withTrashed()->find($id)->restore();

        return redirect()->back()->with('toast_success', 'File restored');
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
        $userid = Auth::user()->id;

        $request->validate([
            'file' => 'required',
            'file.*' => 'mimes:csv,txt,xlx,xls,pdf,jpg,jpeg,png,docx,pptx|max:8192'
        ]);

        if($request->hasfile('file')){
            $files = $request->file('file');
            $folderName = Folder::where('id','=',$request->folder_id)->first();

            foreach($files as $file){
                $name = $file->getClientOriginalName();
                $path = $file->storeAs('files/'.$folderName->folderName, $name, 'public');

                File::create([
                    'fileName' => $name,
                    'filePath' => $path,
                    'folder_id' => $request->folder_id,
                    'user_id' => $userid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->back()->with('toast_success', 'File(s) uploaded successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show(File $file)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function edit(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        // This permanently deletes the file in the folder
        // if(Storage::disk('public')->exists('files/'.$file->folder->folderName.'/'.$file->fileName)){
        //     Storage::disk('public')->delete('files/'.$file->folder->folderName.'/'.$file->fileName);
        // }
        $file->delete();
        

        return redirect()->back()->with('toast_success', 'File deleted successfully');
    }

    public function share(File $file)
    {
        Share::create([
            'file_id' => $file->id,
            'role_id' => $request->role_id,
            'shared_at' => now(),
        ]);
    }
}
