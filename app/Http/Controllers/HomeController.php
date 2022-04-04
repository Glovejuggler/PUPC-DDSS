<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Database\Eloquent\Builder;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userCount = User::count();
        if(Gate::allows('do-admin-stuff')){
            $filecount = File::all()->count();
        } else {
            $filecount = File::wherehas('user', function(Builder $query){
                $query->where('role_id','=',Auth::user()->role_id);
            })->count();
        }

        $roles = Role::all();

        $count = [];
        foreach($roles as $role){
            $count[] = File::wherehas('user', function(Builder $query) use($role){
                $query->where('role_id','=',$role->id);
            })->count();
        }
        
        // return $count;
        return view('home', compact('userCount','filecount', 'roles', 'count'));
    }
}
