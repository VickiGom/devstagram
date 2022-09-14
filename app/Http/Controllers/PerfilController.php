<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use App\Models\User;

class PerfilController extends Controller
{


    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        return view('perfil.index');  
    }

    public function store(Request $request){

        $request->request->add(['username' => Str::slug($request->username)]);


        $this->validate($request,[
            'username' => 'min:3|max:20',
            'imagen' => 'required'
        ]);
        
        if($request->imagen){
            $imagen = $request->file('imagen');

            $nombreImagem = Str::uuid() . "." . $imagen->extension();

            $imagenServidor = Image::make($imagen);
            $imagenServidor ->fit(1000, 1000);

            $imagenPath = public_path('perfiles') . '/' . $nombreImagem;
            $imagenServidor->save($imagenPath);
        }

        // Se guardan los cambios
        $usuario = User::find(auth()->user()->id); 

        $usuario->username = $request->username;

        $usuario->imagen = $nombreImagem ?? auth()->user()->imagen ?? null;

        $usuario->save();

        return redirect()->route('posts.index', $usuario->username);

    }

    

}
