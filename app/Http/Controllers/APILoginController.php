<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\helpers\JsonResponse;
use Carbon\Carbon;
use App\Repositories\Eloquent\InternalEventRepository as Internal;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\JWTTrait;

class APILoginController extends Controller
{
     public function __construct(Internal $internal)
    {
        $this->internal = $internal;
    }

    use JWTTrait;

    public function login() {
        // get email and password from request
        $credentials = request(['email', 'password']);
        $id = User::where('email', $credentials["email"])->get()->all();
        
        // try to auth and get the token using api authentication
        if (!$token = auth('api')->attempt($credentials)) {
            // if the credentials are wrong we send an unauthorized error in json format
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $now = Carbon::now('America/Mexico_City');
        if($id[0]){ 
            $this->internal->create(array(
                'user_id'       => $id[0]->id,
                'evento'        => 'El usuario: '.$id[0]->name. ' ha iniciado sesión',
                'created_at'    => $now,
                'updated_at'    => $now
            ));
        }
        return response()->json([
            'token' => $token,
            'type' => 'bearer', // you can ommit this
            'expires' => auth('api')->factory()->getTTL() * 4800, // time to expiration
            "Usuario" =>($id[0]->toArray())
            
        ]);

    }
}
