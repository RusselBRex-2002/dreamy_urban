<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Carbon\Carbon;
use App\Http\Requests\User\LoginRequest;
use App\Http\Repositories\User\UserRepository;

class LoginController extends Controller
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function Login(LoginRequest $request)
    {
        try {
            // Attempt to log the user in
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
    
                // Regenerate session to prevent session fixation attacks
                $request->session()->regenerate();
    
                // Check if the authenticated user is an Admin
                if (auth()->user()->roles->first()->name == 'Admin') {
                    toastr()->success('Login successful');
                    return redirect()->route('admin.dashboard'); // Redirect to the admin dashboard
                } else {
                    Auth::logout(); // Log out the user if they are not an admin
                    toastr()->error('Access denied');
                    return redirect()->route('login');
                }
    
            } else {
                // Authentication failed
                toastr()->error('Invalid Email or Password');
                return redirect()->route('login')->withInput($request->only('email'));
            }
        } catch (\Exception $e) {
            // Log the error and rollback the transaction
            Log::error('Login Error: ' . $e->getMessage());
            toastr()->error('An error occurred. Please try again later.');
            return redirect()->route('login');
        }
    }
    

    public function Logout(Request $request)
    {
        DB::beginTransaction();
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            DB::commit();
            toastr()->success('Logout Successfully!');
            return redirect()->route('login');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('LOGOUT_EXCEPTION_ERROR : ' . $e->getMessage());
            toastr()->error('Error while processing!');
            return redirect()->back()->with(['error' => 'Error while processing']);
        } catch (\Error $e) {
            Log::info('LOGOUT_ERROR : ' . $e->getMessage());
            DB::rollBack();
            toastr()->error('Error while processing!');
            return redirect()->back()->with(['error' => 'Error while processing']);
        }
    }
}
