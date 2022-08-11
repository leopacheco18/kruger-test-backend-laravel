<?php

namespace App\Http\Controllers;

use App\Mail\SendCredentials;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

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
        \DB::table('user_vaccine')
            ->where('id_user', $request->id_user)
            ->delete();
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


        if(isset($request->id_user)){
            $user = User::find($request->id_user);
        }else{
            $name = trim($request->name);
            $lastname = trim($request->lastname);
            $lastnameArr = explode(' ',$lastname);
            $username = $name[0] . $lastnameArr[0];
            $username= strtolower($username);
            $password = uniqid();
            //Validate Username

            $countUsername = User::where('user', $username)->count();
            $index = 1;
            if($countUsername > 0) {
                $usernameInitial = $username;
                while($countUsername !== 0) {
                    $username = $usernameInitial.$index;
                    $countUsername = User::where('user', $username)->count();
                    $index++;
                }
            }
            $user = new User();
            $user->user= $username;
            $user->password= Hash::make($password);

            $details = [
                "name" => $request->name,
                "username" => $username,
                "password" => $password,
            ];
            \Mail::to($request->email)->send(new SendCredentials($details));

        }


        $user->name= $request->name;
        $user->lastname= $request->lastname;
        $user->email= $request->email;
        $user->cedula= $request->cedula;
        $user->save();

        return response()->json(['user' => $user]);
    }


    public function getVaccines(){
        $vaccines = \DB::table("vaccines")->get();
        return response()->json(['vaccines' => $vaccines]);
    }

    public function update(Request  $request){
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $user = User::find($request->id_user);

        $user->birthday= $request->birthday;
        $user->address= $request->address;
        $user->phone= $request->phone;
        $user->isVaccinated= $request->isVaccinated;
        \DB::table('user_vaccine')
            ->where('id_user', $request->id_user)
            ->delete();
        if($request->isVaccinated == 1) {
            \DB::table('user_vaccine')
                ->insert([
                    'id_user' => $request->id_user,
                    'id_vaccine' => $request->id_vaccine,
                    'dose' => $request->dose,
                    'date' => $request->date,
                ]);;
        }
        $user->save();

        return response()->json(['user' => $user]);
    }
}
