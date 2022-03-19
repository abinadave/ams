<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use Auth;
class UserController extends Controller
{
	public function currentUser(){
		return response()->json(Auth::user());
	}
	public function initload(){
        if (Auth::user()) {
            return view('authenticated');
        }else {
            return redirect('/login');
        }
    }
    public function register(Request $request){
    	$request->validate([
		    'name' => 'required|unique:users|max:255',
		    'username' => 'required|unique:users',
		    'password' => 'required|min:3|max:100|confirmed'
		]);
		$user = new User;
		$user->name = $request->name;
		$user->username = $request->username;
		$user->password = Hash::make($request->password);
		$user->admin = 0;
		$user->save();
		return redirect()->back()->with('message', 'Registration Complete');
    }
    public function login(Request $request){
    	$username = $request->username;
    	$password = $request->password;
    	if (Auth::attempt(['username' => $username, 'password' => $password, 'admin' => 1])) {
		    // The user is active, not suspended, and exists.
		    return redirect('/');
		}else {
			return redirect()->back()->with('message', 'Invalid username or password');
		}
    }
}
