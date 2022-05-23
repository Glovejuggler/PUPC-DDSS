<?php

namespace App\Http\Controllers;

use Alert;
use App\Models\File;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        $users = User::all();

        return view('users.index', compact('users', 'roles'));
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

    public function emailcheck(Request $request)
    {
        if($request->get('email')) {
            $email = $request->email;
            
            if (User::where('email','=',$email)->exists()) {
                echo 'not_unique';
            } else {
                echo 'unique';
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = new User;

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->middle_name = $request->middle_name;
        $user->address = $request->address;
        $user->role_id = $request->role;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        $user->save();

        return redirect()->route('user.index')->with('toast_success', 'User added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findorfail($id);
        $files = File::where('user_id','=',$id)->get();

        return view('users.view', compact('user', 'files'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findorfail($id);
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
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
        $user = User::findorfail($id);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->middle_name = $request->middle_name;
        $user->address = $request->address;
        $user->role_id = $request->role;
        $user->email = $request->email;

        $user->update();

        return redirect()->route('user.index')->with('toast_success', 'Updated user information successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->back()->with('toast_success', "User deleted successfully");
    }

    public function profile()
    {
        $user = Auth::user();
        $files = File::where('user_id','=',$user->id)->get();

        return view('users.profile', compact('user', 'files'));
    }

    public function profile_edit()
    {
        $user = User::findorfail(Auth::user()->id);
        $roles = Role::all();

        return view('users.profile_edit', compact('user', 'roles'));
    }

    public function profile_update(Request $request)
    {
        $user = User::findorfail(Auth::user()->id);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->middle_name = $request->middle_name;
        $user->address = $request->address;
        $user->email = $request->email;

        $user->update();

        return redirect()->route('home')->with('toast_success', 'Profile updated successfully');
    }

    public function change_password(Request $request)
    {
        $user = User::findorfail(Auth::user()->id);
        
        if(Hash::check($request->old_password, $user->password)) {
            if($request->new_password == $request->confirm_password){
                $user->update([
                    $user->password = Hash::make($request->confirm_password)
                ]);
            } else {
                return redirect(url()->previous().'#changepassword')->withErrors([
                    'confirm_password' => ['Password do not match']
                ]);
            }
        } else {
            return redirect(url()->previous().'#changepassword')->withErrors([
                'old_password' => ['Incorrect password']
            ]);
        }

        return redirect()->route('home')->with('toast_success', 'Password changed successfully');
    }
}
