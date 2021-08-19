<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegistrarController extends BaseController
{
    public function registrar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('Meu app')->plainTextToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'Usuário registrado com sucesso!');
    }
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] = $user->createToken('Meu App')->plainTextToken;
            $success['name'] = $user->name;
            
            return $this->sendResponse($success, 'Usuário logado com sucesso!');

        }else{
            return $this->sendError('Unauthorised', ['error'=>'Unauthorised']);
        }
    }
}