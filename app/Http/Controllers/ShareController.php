<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Role;
use App\Models\Share;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ShareController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * Pepega query, I know. But hey, it works.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = Share::where('role_id','=',Auth::user()->role_id)->paginate(10);

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
        $roles = Role::where('id','!=',Auth::user()->role_id)->get();
        $shares = Share::where('file_id','=',$id)->get();

        return view('share.share_file', compact('roles', 'shares', 'file'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * Creates a new share instance of the file
     * This works like a magic for me
     * If a checkbox in the request is unchecked, it also removes the share in the database
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id)
    {
        if($request->has('role_id')) {
            foreach ($request->role_id as $role_id) {
                Share::firstOrCreate([
                    'file_id' => $id,
                    'role_id' => $role_id,
                ], [
                    'shared_by' => Auth::user()->id,
                    'shared_at' => now(),
                ]);
            }
            Share::where('file_id','=',$id)->whereNotIn('role_id', $request->role_id)->delete();
        } else {
            Share::where('file_id','=',$id)->delete();
        }


        return redirect()->route('file.index')->with('toast_success', 'Successfully shared file');
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
}
