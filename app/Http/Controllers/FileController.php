<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Role;
use App\Models\Share;
use App\Models\Folder;
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
        /**
         * Controls the pagination depending
         * if viewed as grid or list
         */
        if(request('grid') == 1) {
            $page = 15;
        } else {
            $page = 10;
        }

        /**
         * Queries only the available files for the user
         * depending on their role
         */
        if(Gate::allows('do-admin-stuff')){
            if(request('show_deleted') == 1){
                $files = File::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate($page);
                $folders = Folder::all();
            } else {
                $files = File::orderBy('created_at', 'desc')->paginate($page);
                $folders = Folder::all();
            }
        } else {
            $files = File::wherehas('user', function(Builder $query){
                $query->where('role_id','=',Auth::user()->role_id);
            })->orderBy('created_at', 'desc')->paginate($page);

            $folders = Folder::wherehas('user', function(Builder $query){
                $query->where('role_id','=',Auth::user()->role_id);
            })->get();
        }

        $roles = Role::all();

        $shares = Share::all();
        $share_roles = Role::where('id','!=',Auth::user()->role_id)->get();

        $image = ['jpg', 'jpeg', 'png', 'bmp'];

        return view('files.index', compact('files', 'folders', 'roles', 'share_roles', 'shares', 'image'));
    }

    /**
     * Restores file from trash
     */
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
            'file.*' => 'mimes:csv,txt,xlsx,xls,pdf,jpg,jpeg,png,docx,pptx,zip,rar|max:8192'
        ]);

        /**
         * Checks first if the request has file/s.
         * Checks the number of files in the request and then finally
         * uploading them.
         * 
         * TL;DR - Multiple upload :omegalul:
         */
        if($request->hasfile('file')){
            $files = $request->file('file');
            $folderName = Folder::where('id','=',$request->folder_id)->first();

            foreach($files as $file){
                $name = $file->getClientOriginalName();
                $path = $file->storeAs($folderName->folderName, $name, 'public');

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
     * Force downloads the files in the storage (not public)
     */
    public function download($id)
    {
        $file = File::find($id);
        $path = str_replace('\\', '/', storage_path()).'/app/public/'.$file->filePath;
        
        return response()->download($path);
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
        return view('files.edit', compact('file'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function rename(Request $request, File $file)
    {
        $file_path = 'public/'.$file->folder->folderName.'/'.$file->fileName;
        $extension = pathinfo(storage_path($file->filePath), PATHINFO_EXTENSION);
        $target_path = 'public/'.$file->folder->folderName.'/'.$request->fileName.'.'.$extension;

        Storage::move($file_path, $target_path);
        $file->fileName = $request->fileName.'.'.$extension;
        $file->update();

        return redirect()->route('file.index')->with('toast_success', 'Rename successful');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */

    /**
     * Permanently deletes the file in the storage
     * Decided not to use it and use archiving method instead
     */
    public function destroy(File $file)
    {
        // if(Storage::disk('public')->exists('files/'.$file->folder->folderName.'/'.$file->fileName)){
        //     Storage::disk('public')->delete('files/'.$file->folder->folderName.'/'.$file->fileName);
        // }
        $file->delete();
        

        return redirect()->back()->with('toast_success', 'File deleted successfully');
    }
}
