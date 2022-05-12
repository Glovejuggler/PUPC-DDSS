<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Role;
use App\Models\Share;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class ShareController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shared = Share::where('role_id','=',Auth::user()->role_id)->get();

        $files = [];
        foreach ($shared as $shared_file) {
            $files[] = File::where('id','=',$shared_file->file_id)->first();
        }

        // dd($files);

        return view('share.index', compact('files'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function share($id)
    {
        $file = File::FindOrFail($id);
        $roles = Role::all();
        $shares = Share::where('file_id','=',$id)->get();

        return view('share.share_file', compact('roles', 'shares', 'file'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $files = Share::FindOrFail($request->file_id);

        foreach ($request->role_id as $role_id) {
            foreach ($files as $file) {
                if ($file->role_id != $role_id) {
                    Share::create([
                        'file_id' => $request->file_id,
                        'role_id' => $role_id,
                        'shared_at' => now(),
                    ]);
                }
            }
        }

        return redirect()->route('user.index')->with('toast_success', 'Successfully shared file');

        // catch (\Illuminate\Database\QueryException $exception) {
        //     $errorInfo = $exception->errorInfo;

        //     return dd($errorInfo);
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function shareThisFile(File $file)
    {
        $share = Share::where('file_id','=',$file->id);

        return response()->json([
            'data' => $share
        ]);
    }
}
