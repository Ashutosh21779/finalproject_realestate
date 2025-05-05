<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Method to show the combined login and registration form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Method to handle user login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('login')->withErrors($validator)->withInput();
        }

        $loginField = $request->input('login');
        $password = $request->input('password');

        // Determine if the login field is an email, username, or phone
        $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : (is_numeric($loginField) ? 'phone' : 'username');

        $credentials = [
            $fieldType => $loginField,
            'password' => $password
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect based on role
            if ($user->role === 'admin') {
                 $notification = array(
                    'message' => 'Admin Login Successfully',
                    'alert-type' => 'success'
                );
                return redirect()->route('admin.dashboard')->with($notification);
            } elseif ($user->role === 'agent') {
                 $notification = array(
                    'message' => 'Agent Login Successfully',
                    'alert-type' => 'success'
                );
                return redirect()->route('agent.dashboard')->with($notification);
            } else {
                 $notification = array(
                    'message' => 'User Login Successfully',
                    'alert-type' => 'success'
                );
                // Redirect users to their dashboard or homepage
                return redirect('/dashboard')->with($notification); 
            }
        }

        // Authentication failed
        return back()->with('status', 'The provided credentials do not match our records.')
                     ->withInput($request->only('login', 'remember'));
    }

    // Method to handle user registration
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', 'string', Rule::in(['user', 'agent'])], // Only allow user or agent registration via form
        ]);

        if ($validator->fails()) {
            // Redirect back to login page, maybe force the register tab to be active?
            // For now, just redirect back with errors.
            return redirect()->route('login')->withErrors($validator)->withInput(); 
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'active', // Default status
        ]);

        // Optional: Send email verification if needed

        // Log the user in
        Auth::login($user);

        $request->session()->regenerate();

        // Redirect based on role
        if ($user->role === 'agent') {
             $notification = array(
                'message' => 'Agent Registration Successful',
                'alert-type' => 'success'
            );
            return redirect()->route('agent.dashboard')->with($notification);
        } else { // Default to user redirect
             $notification = array(
                'message' => 'User Registration Successful',
                'alert-type' => 'success'
            );
            return redirect('/dashboard')->with($notification);
        }
    }

    // Method to handle user logout
    public function logout(Request $request)
    {
        $role = Auth::user()->role; // Get role before logging out if needed for specific messages
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $notification = array(
            'message' => 'Logout Successful',
            'alert-type' => 'success'
        );

        return redirect('/login')->with($notification);
    }
} 