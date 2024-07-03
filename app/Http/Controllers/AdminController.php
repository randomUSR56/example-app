<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\AuthenticationException;


class AdminController extends Controller
{
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $notification = array(
            'message' => 'Logged out',
            'alert-type' => 'warning'
        );

        return redirect('/login') -> with($notification);
    }

    public function profile()
    {
        $id = Auth::user()->id;
        $adminData = User::find($id);
        return view('admin.body.admin_profile_view', compact('adminData'));
    }//End method

    public function editProfile()
    {
        $id = Auth::user()->id;
        $editData = User::find($id);
        return view('admin.body.admin_profile_edit', compact('editData'));
    }//End method

    public function storeProfile(Request $request)
    {
        $id = Auth::user()->id;
        $data = User::find($id);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->username = $request->username;

        if ($request->file('profile_image')) {
            $file = $request -> file('profile_image');

            $fileName = date('YmdHi').$file->getClientOriginalName();
            $file -> move(public_path('upload/admin_images'), $fileName);
            $data['profile_image'] = $fileName;
        }
        $data->save();

        $notification = array(
            'message' => 'Admin Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect() -> route('admin.profile') -> with($notification);

    }//End method

    public function changePassword()
    {
        return view('admin.body.admin_change_password');
    }//End method

    public function updatePassword(Request $request)
    {
        //dd($request);

        //Validate Data
        $request -> validate([
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ]);

        $hashedPassword = Auth::user()->password;
        if (Hash::check($request -> old_password, $hashedPassword))
        {
            //dd(false);
            $users = User::find(Auth::id());
            $users -> password = bcrypt($request -> new_password);
            $users -> save();

            $notification = array(
                'message' => 'Password Updated Successfully',
                'alert-type' => 'success'
            );
            return redirect() -> back() -> with($notification);
        }
        else
        {
            //dd(true);
            $notification = array(
                'message' => 'Current Password is Invalid',
                'alert-type' => 'error'
            );
            return redirect() -> back() -> with($notification);
        }

    }//End method

    public function displayLogin(): View
    {
        return view('auth.login');
    }

    public function displayRegister(): View
    {
        //Log::info('Display Register Page');
        return view('auth.register');
    }

    public function displayRecover(): View
    {
        return view('auth.forgot-password');
    }

    public function storeRegister(Request $request): RedirectResponse
    {
        //dd($request->all());
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    public function storeLogin(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $notification = [
                'message' => 'Logged in successfully',
                'alert-type' => 'success'
            ];

            return redirect()->intended(RouteServiceProvider::HOME)->with($notification);
        } else {
            $notification = [
                'message' => 'Failed to log in',
                'alert-type' => 'error' // Assuming 'error' is the type for failure messages
            ];

            return redirect()->back()->withInput($request->only('username', 'remember'))->with($notification);
        }

    }

    public function storeRecover(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );
        //dd('request status: ', $status);

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}
