<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Role;
use App\Models\Share;
use App\Models\Folder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Database\Eloquent\Builder;
use RahulHaque\Filepond\Facades\Filepond;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id = NULL)
    {
        $current_folder = Folder::find($id);
        if(Gate::denies('do-admin-stuff')) {
            if(Gate::denies('open-folder', $current_folder)) {
                return redirect()->back()->with('toast_error', 'Access denied');
            }
        }
        /**
         * Controls the pagination depending
         * if viewed as grid or list
         */
        if(request('grid') == 1) {
            Cookie::queue('view', 'grid', 2628000);
            $files = $this->queryFiles()
                        ->where('folder_id','=',$id)
                        ->paginate(15);
        } else {
            Cookie::queue('view', 'list', 2628000);
            $files = $this->queryFiles()
                        ->where('folder_id','=',$id)
                        ->get();
        }
        
        $folders = $this->queryFolders()
                        ->where('parent_folder_id','=',$id)
                        ->get();

        $roles = Role::all();

        $shares = Share::all();
        $share_roles = Role::where('id','!=',Auth::user()->role_id)->get();

        $image = ['jpg', 'jpeg', 'png', 'bmp'];
        
        return view('files.drive', compact('files', 'folders', 'roles', 'share_roles', 'shares', 'image', 'current_folder'));
    }

    /**
     * Restores file from trash
     */
    public function recover($id)
    {
        $file = File::withTrashed()->find($id);
        $file->restore();

        activity()
                ->causedBy(Auth::user())
                ->performedOn($file)
                ->event('restored')
                ->withProperties(['name' => $file->fileName])
                ->log('restored a file');

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

        // $request->validate([
        //     'file' => 'required',
        //     'file.*' => 'mimes:csv,txt,xlsx,xls,pdf,jpg,jpeg,png,docx,pptx,zip,rar|max:8192'
        // ]);

        /**
         * Checks first if the request has file/s.
         * Checks the number of files in the request and then finally
         * uploading them.
         * 
         * TL;DR - Multiple upload :omegalul:
         */
            $files = Filepond::field($request->file)->getFile();

            $folder = Folder::where('id','=',$request->folder_id)->first();

            foreach($files as $file){
                $name = $file->getClientOriginalName();
                if(!$folder == NULL){
                    $path = $file->storeAs($folder->folderName, $name, 'public');
                } else {
                    $path = $file->storeAs('/', $name, 'public');
                }

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
                    ->withProperties(['name' => $newFile->fileName])
                    ->log('uploaded a file');
            }

        return redirect()->back()->with('toast_success', 'File(s) uploaded successfully');
    }

    /**
     * Force downloads the files in the storage (not public)
     */
    public function download($id)
    {
        if(Gate::allows('do-admin-stuff')){
            $file = File::withTrashed()->find($id);
        } else {
            $file = File::find($id);
        }

        if(Gate::denies('do-admin-stuff')){
            if(Gate::denies('can-download', $file)){
                return redirect()->back()->with('toast_error', 'Access denied');
            }
        }


        $path = str_replace('\\', '/', storage_path()).'/app/public/'.$file->filePath;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($file)
            ->event('downloaded')
            ->withProperties(['name' => $file->fileName])
            ->log('downloaded a file');
        
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
        if($file->folder == NULL){
            $folderName = NULL;
        } else {
            $folderName = $file->folder->folderName.'/';
        }
        $oldName = $file->fileName;
        $file_path = 'public/'.$folderName.$file->fileName;
        $extension = pathinfo(storage_path($file->filePath), PATHINFO_EXTENSION);
        $target_path = 'public/'.$folderName.$request->fileName.'.'.$extension;

        $file->fileName = $request->fileName.'.'.$extension;
        $file->filePath = Str::after($target_path, 'public/');
        $file->update();
        Storage::move($file_path, $target_path);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($file)
            ->event('renamed')
            ->withProperties(['old_name' => $oldName, 'new_name' => $file->fileName])
            ->log('renamed a file');

        return redirect()->back()->with('toast_success', 'Rename successful');
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
        activity()
            ->causedBy(Auth::user())
            ->performedOn($file)
            ->event('deleted')
            ->withProperties(['name' => $file->fileName])
            ->log('deleted a file');

        $file->delete();

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
            if($request->search != NULL){
                $files = $this->queryFiles()
                                ->where('folder_id','=',$request?->folder)
                                ->where('fileName','LIKE','%'.$request->search.'%')
                                ->paginate(15);
                $folders = $this->queryFolders()
                                ->where('folderName','LIKE','%'.$request->search.'%')
                                ->where('parent_folder_id','=',$request?->folder)
                                ->get();
            } else {
                $files = $this->queryFiles()
                                ->where('folder_id','=',$request?->folder)
                                ->paginate(15);
                $folders = $this->queryFolders()
                                ->where('parent_folder_id','=',$request?->folder)
                                ->get();
            }

            if($files->count() > 0 || $folders->count() > 0) {
                return view('files.partials.gridview',
                        compact('files', 'folders', 'roles', 'share_roles', 'shares', 'image'))
                        ->render();
            } else {
                return 'File not found';
            }
        }
    }

    /**
     * Function to query the files according to the role of the user.
     * Queries are intentionally left incomplete so it can be used in
     * different situations.
     * 
     * $files = File::onlyTrashed()->orderBy('deleted_at', 'desc');
     */
    public function queryFiles()
    {
        if(Gate::allows('do-admin-stuff')){
            $files = File::orderBy('created_at', 'desc');
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
            $folders = Folder::orderBy('created_at', 'desc');
        } else {
            $folders = Folder::wherehas('user', function(Builder $query){
                $query->where('role_id','=',Auth::user()->role_id);
            });
        }

        return $folders;
    }

    /**
     * Displays all the files that are trashed
     */
    public function trash_index()
    {
        if(request('grid') == 1) {
            Cookie::queue('tview', 'grid', 2628000);
            $files = $this->queryFiles()->onlyTrashed()->paginate(15);
        } else {
            Cookie::queue('tview', 'list', 2628000);
            $files = $this->queryFiles()->onlyTrashed()->get();
        }
        
        $folders = $this->queryFolders()->onlyTrashed()->get();

        $image = ['jpg', 'jpeg', 'png', 'bmp'];

        return view('files.trash', compact('files', 'folders', 'image'));
    }


    /**
     * This function searches for the files in the trash
     * It's an AJAX call like the search()
     * But for some reason that same function cannot be used for this
     */
    public function search_trash(Request $request)
    {
        $image = ['jpg', 'jpeg', 'png', 'bmp'];

        if($request->ajax()) {
            if($request->search != NULL){
                $files = $this->queryFiles()
                                ->onlyTrashed()
                                ->where('fileName','LIKE','%'.$request->search.'%')
                                ->paginate(15);
                $folders = $this->queryFolders()
                                ->onlyTrashed()
                                ->where('folderName','LIKE','%'.$request->search.'%')
                                ->get();
            } else {
                $files = $this->queryFiles()->onlyTrashed()->paginate(15);
                $folders = $this->queryFolders()->onlyTrashed()->get();
            }

            if($files->count() > 0) {
                return view('files.partials.trash_gridview', compact('files', 'folders', 'image'))->render();
            } else {
                return 'File not found';
            }
        }
    }
}
