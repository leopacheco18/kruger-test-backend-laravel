<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends Controller
{

    public function get(){
        $items = User::all();
        return response()->json(['users' => $items]);
    }


    public function delete(Request  $request){
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $user = User::find($request->id_user);
        $user->delete();
        return response()->json(['success' => true]);
    }

    public function store(Request  $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|string|email',
            'cedula' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $name = trim($request->name);
        $lastname = trim($request->lastname);
        $lastnameArr = explode(' ',$lastname);
        $username = $name[0] . $lastnameArr[0];
        $password = uniqid();
        if(isset($request->id_user)){
            $user = User::find($request->id_user);
        }else{
            $user = new User();
            $user->user= $username;
            $user->password= Hash::make($password);
        }


        $user->name= $request->name;
        $user->lastname= $request->lastname;
        $user->email= $request->email;
        $user->cedula= $request->cedula;
        $user->save();

        return response()->json(['user' => $user]);
    }
}
