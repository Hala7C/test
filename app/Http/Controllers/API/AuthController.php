<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\AUTH;
use App\Models\User;
use Laravel\Fortify\Rules\Password;
use Laravel\Jetstream\Jetstream;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;


class AuthController extends Controller
{

    /////////////////////////////////////////Register

    public function register(Request $request)
    {


        $validator = Validator::make($request->all(),[
            'name' => ['required', 'string', 'max:255','unique:users'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users'],
            'password' => ['required', 'string', (new Password)->length(10)->requireNumeric()],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],

        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role'=>'user',
        ]);


         if (isset($request->photo)) {
            $user->updateProfilePhoto($request->photo);

        }

        $token = $user->createToken('auth_token')->plainTextToken;


        return response()
            ->json(['data' => $user,'access_token' => $token, 'token_type' => 'Bearer' ],201);
    }


    /////////////////////////////////////////login



    public function login(Request $request)
    {

        if (!Auth::attempt($request->only('email', 'password')))
        {
            return response()
                ->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['message' => 'Hi '.$user->name.', welcome to home','access_token' => $token, 'token_type' => 'Bearer','user'=>$user  ],200);

    }



        /////////////////////////////////////////logout




    public function logout(User $user)
    {
        $user->tokens()->delete();

        return response()
            ->json(['message' =>'You have successfully logged out and the token was successfully deleted' ],200);
    }


        /////////////////////////////////////////updateProfile


     public function updateProfile(Request $request){


        $user = auth()->user();

            $validator = Validator::make($request->all(),[
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
              ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

       if (isset($request->photo)) {
            $user->updateProfilePhoto($request->photo);
        }

        $user->forceFill([
            'name' =>  $request->name,
            'email' => $request->email,
        ])->save();

           return response()
            ->json(['message' =>'You have successfully update ' ],200);

     }

         /////////////////////////////////////////updatepassword



    public function updatepassword(Request $request){

         $user = auth()->user();

         $validator = Validator::make($request->all(),[

            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', (new Password)->length(10)->requireNumeric(), 'confirmed'],

        ])->after(function ($validator) use ($user, $request) {

            if (! isset($request->current_password) || ! Hash::check($request->current_password, $user->password)) {
                $validator->errors()->add('current_password', __('The provided password does not match your current password.'));
            }

        });

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        if (request()->hasSession()) {
            request()->session()->put([
                'password_hash_'.Auth::getDefaultDriver() => Auth::user()->getAuthPassword(),
            ]);
        }

         return response()
            ->json(['message' =>'password successfully update ' ],200);

        }

         /////////////////////////////////////////profile



     public function profile()
     {
        return  auth()->user();
     }


}
