<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Auth;

class AuthController extends Controller {

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|password',
            'confirm_password' => 'required|same:password',
            'fmc_token' => 'required',
            'phone_number' => 'required',
            'address' => ''
        ]);

        if($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('DonorConnect')->plainTextToken;
        $response = [
            'success' => true,
            'data' => $success,
            'message' => 'User Registered Successfully'
        ];
        return response()->json($response, 200);
    }


    public function login(Request $request) {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('DonorConnect')->plainTextToken;
            $success['name'] = $user->name;

            $response = [
                'success' => true,
                'data' => $success,
                'message' => 'User Logged In Successfully'
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'success' => false,
                'message' => 'UnAuthorized Error'
            ];

            return response()->json($response);
        }
    }

}
