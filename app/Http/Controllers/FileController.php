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
    public function index(Request $request)
    {
        /**
         * Controls the pagination depending
         * if viewed as grid or list
         */
        if(request('grid') == 1) {
            $files = $this->queryFiles()->paginate(15);
        } else {
            $files = $this->queryFiles()->get();
        }
        
        $folders = $this->queryFolders();

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
        $file = File::withTrashed()->find($id)->restore();

        activity()
                    ->causedBy(Auth::user())
                    ->performedOn($file)
                    ->event('restored')
                    ->log('Restored a file');

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

                $newFile = File::create([
                    'fileName' => $name,
                    'filePath' => $path,
                    'folder_id' => $request->folder_id,
                    'user_id' => $userid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($newFile)
                    ->event('uploaded')
                    ->log('Uploaded a file');
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

        activity()
                    ->causedBy(Auth::user())
                    ->performedOn($file)
                    ->event('downloaded')
                    ->log('Downloaded a file');
        
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

        activity()
                    ->causedBy(Auth::user())
                    ->performedOn($file)
                    ->event('renamed')
                    ->log('Renamed a file');

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

        activity()
                    ->causedBy(Auth::user())
                    ->performedOn($file)
                    ->event('deleted')
                    ->log('Deleted a file');
        

        return redirect()->back()->with('toast_success', 'File deleted successfully');
    }


    /**
     * The most inefficient way just for searching the files in grid view
     * This could be improved or there really is an easier way of doing this
     * But I have to make it work before presentation
     * No one's gonna see this tho, I think lmao
     * 
     * Update: I kinda made it look clean :omegalul:
     */
    public function search(Request $request)
    {
        $roles = Role::all();

        $shares = Share::all();
        $share_roles = Role::where('id','!=',Auth::user()->role_id)->get();

        $image = ['jpg', 'jpeg', 'png', 'bmp'];

        if($request->ajax()) {
            if($request->search != 0){
                $files = $this->queryFiles()->where('fileName','LIKE','%'.$request->search.'%')->paginate(15);
                $folders = $this->queryFolders();
            } else {
                $files = $this->queryFiles()->paginate(15);
                $folders = $this->queryFolders();
            }

            if($files->count() > 0) {
                return view('files.partials.gridview', compact('files', 'folders', 'roles', 'share_roles', 'shares', 'image'))->render();
            } else {
                return 'File not found';
            }
        }
    }

    /**
     * Function to query the files according to the role of the user.
     * Queries are intentionally left incomplete so it can be used in
     * different situations.
     */
    public function queryFiles()
    {
        if(Gate::allows('do-admin-stuff')){
            if(request('show_deleted') == 1){
                $files = File::onlyTrashed()->orderBy('deleted_at', 'desc');
            } else {
                $files = File::orderBy('created_at', 'desc');
            }
        } else {
            $files = File::wherehas('user', function(Builder $query){
                $query->where('role_id','=',Auth::user()->role_id);
            })->orderBy('created_at', 'desc');
        }

        return $files;
    }


    /**
     * Function to query the folders according to the role of the user.
     * Unlike above function, the queries are complete as no further
     * requirements are to be met when fetching the data.
     */
    public function queryFolders()
    {
        if(Gate::allows('do-admin-stuff')){
            if(request('show_deleted') == 1){
                $folders = Folder::all();
            } else {
                $folders = Folder::all();
            }
        } else {
            $folders = Folder::wherehas('user', function(Builder $query){
                $query->where('role_id','=',Auth::user()->role_id);
            })->get();
        }

        return $folders;
    }
}
