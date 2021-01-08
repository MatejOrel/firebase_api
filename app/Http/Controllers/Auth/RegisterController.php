<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Auth;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Database;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\FirebaseException;
class RegisterController extends Controller {
   use RegistersUsers;
   protected $auth;
   protected $redirectTo = RouteServiceProvider::HOME;
   public function __construct(FirebaseAuth $auth) {
      $this->middleware('guest');
      $this->auth = $auth;
   }
   protected function validator(array $data) {
      return Validator::make($data, [
         'name' => ['required', 'string', 'max:255'],
         'surname' => ['required', 'string', 'max:255'],
         'email' => ['required', 'string', 'email', 'max:255'],
         'password' => ['required', 'string', 'min:8', 'confirmed'],
      ]);
   }
   protected function register(Request $request) {
      try{
         $fullname = $request->input('name') . " " . $request->input('surname');
         $userProperties = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'displayName' => $fullname,
            'sex' => $request->input('gender'),
         ];
         $database = app('firebase.database');
         $oppositeSex;
         if($userProperties['sex'] == "Male")
            $oppositeSex = "Female";
         else
            $oppositeSex = "Male";
         $database->getReference('Users/'.$uID.'')
            ->set(
               [
                  'name' => $userProperties['displayName'],
                  'profileImageUrl' => "https://firebasestorage.googleapis.com/v0/b/dateish-5d381.appspot.com/o/profileImages%2Fno-profile-picture-300x216.jpg?alt=media&token=be771306-e3fe-4826-bac7-606508fe64da",
                  'sex' => $userProperties['sex'],
                  'showSex' => $oppositeSex,
                  'minAge' => 18,
                  'maxAge' => 100,
                  'distance' => 142
            ]);
         return response("Sign up successful", 200);
      }
      catch(Exception $e){
         return response('failed', 422);
      }
   }
}