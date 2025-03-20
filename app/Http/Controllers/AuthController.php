<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class AuthController extends Controller
{
    /**
     * Show the super admin login form.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showSuperAdminLoginForm()
    {
        return view('auth.superadmin-login');
    }

    /**
     * Handle super admin login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function superAdminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check if using environment credentials
        if ($request->email === env('SUPER_ADMIN_EMAIL') && $request->password === env('SUPER_ADMIN_PASSWORD')) {
            // Find or create super admin user
            $user = User::where('email', env('SUPER_ADMIN_EMAIL'))->first();
            
            if (!$user) {
                // Create super admin user
                $user = new User();
                $user->name = 'Super Admin';
                $user->email = env('SUPER_ADMIN_EMAIL');
                $user->mobile = '9876543210';
                $user->city = 'Mumbai';
                $user->password = Hash::make(env('SUPER_ADMIN_PASSWORD'));
                $user->user_id = User::generateUniqueUserId();
                $user->save();
                
                // Get or create super admin role
                $role = Role::where('name', 'superadmin')->first();
                if (!$role) {
                    $role = Role::create([
                        'name' => 'superadmin',
                        'description' => 'Super Administrator with full access',
                    ]);
                }
                
                // Assign role
                $user->roles()->attach($role->id);
            }
            
            // Login
            Auth::login($user);
            return redirect()->intended('superadmin/dashboard');
        }
        
        // Try regular authentication
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Check if user is a super admin
            if ($user->hasRole('superadmin')) {
                return redirect()->intended('superadmin/dashboard');
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'You do not have permission to access the super admin area.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle user logout.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    /**
     * Show the registration form.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showRegistrationForm()
    {
        $roles = Role::where('name', '!=', 'superadmin')->get();
        return view('auth.register', compact('roles'));
    }

    /**
     * Handle user registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15|unique:users',
            'city' => 'required|string|max:255',
            'roles' => 'required|array',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->city = $request->city;
        $user->location = $request->location;
        $user->user_id = User::generateUniqueUserId();
        $user->save();

        // Attach roles
        $user->roles()->attach($request->roles);

        // Log the user in
        Auth::login($user);

        return redirect('/dashboard');
    }

    /**
     * Show the mobile verification form.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showMobileVerificationForm()
    {
        return view('auth.mobile-verification');
    }

    /**
     * Send OTP to mobile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendOTP(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|max:15',
        ]);

        // In a real app, this would use Firebase to send an OTP
        // For now, we'll just return a success response
        
        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'verification_id' => 'mock-verification-id',
        ]);
    }

    /**
     * Verify OTP.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'verification_id' => 'required|string',
            'otp' => 'required|string|max:6',
            'mobile' => 'required|string|max:15',
        ]);

        // In a real app, this would verify the OTP with Firebase
        // For now, we'll just check if the OTP is '123456' (for testing)
        
        if ($request->otp === '123456') {
            // Check if user exists with this mobile number
            $user = User::where('mobile', $request->mobile)->first();
            
            if ($user) {
                // Log the user in
                Auth::login($user);
                
                return response()->json([
                    'success' => true,
                    'message' => 'OTP verified successfully',
                    'user_exists' => true,
                    'redirect' => '/dashboard',
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP verified successfully',
                    'user_exists' => false,
                    'redirect' => '/register',
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP',
            ], 400);
        }
    }
}

