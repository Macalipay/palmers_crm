<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the post login / authenticated user redirect path.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return string
     */
    protected function authenticated(Request $request, $user)
    {
        // Redirect based on the branch_id of the authenticated user
        switch ($user->branch_id) {
            case 1:
                return redirect('/sale'); // Update with your actual route
            case 2:
                return redirect('telemarketing'); // Update with your actual route
            case 3:
                return redirect('sale'); // Update with your actual route
            default:
                return redirect('/home'); // Default redirect if branch_id doesn't match
        }
    }
}