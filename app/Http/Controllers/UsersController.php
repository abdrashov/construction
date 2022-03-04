<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UsersController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            'filters' => Request::all('search', 'trashed'),
            'users' => User::filter(Request::only('search', 'trashed'))
                ->get()
                ->transform(fn ($user) => [
                    'id' => $user->id,
                    'firstname' => $user->first_name,
                    'lastname' => $user->last_name,
                    'login' => $user->login,
                    'role' => $user->role,
                    'deleted_at' => $user->deleted_at,
                ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store()
    {
        Request::validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'login' => ['required', 'min:6', 'max:50', Rule::unique('users')],
            'password' => ['required', 'min:6', 'confirmed'],
            'role' => ['required'],
        ]);

        User::create([
            'first_name' => Request::get('first_name'),
            'last_name' => Request::get('last_name'),
            'login' => Request::get('login'),
            'password' => Hash::make(Request::input('password')),
            'role' => Request::get('role'),
        ]);

        return Redirect::route('users')->with('success', 'Пользователь создано.');
    }

    public function auth(User $user)
    {
        abort_if(Auth::id() != $user->id, 404);

        return Inertia::render('Users/Auth', [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'login' => $user->login,
                'deleted_at' => $user->deleted_at,
            ],
        ]);
    }

    public function password(User $user)
    {
        abort_if(Auth::id() != $user->id, 404);

        Request::validate([
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        if ( !Hash::check(Request::input('old_password'), Auth::user()->password) ) {
            return Redirect::back()->withError('Старый пароль не соответствует нашим записям.');
        }

        $user->update(['password' => Hash::make(Request::input('password'))]);

        return Redirect::route('auth', $user->id)->with('success', 'Пароль успешно изменен.');
    }

    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'login' => $user->login,
                'role' => $user->role,
                'deleted_at' => $user->deleted_at,
            ],
        ]);
    }

    public function update(User $user)
    {
        Request::validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'role' => ['required'],
            'login' => ['required', 'min:6', 'max:50', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'min:6', 'confirmed'],
        ]);

        $user->update(Request::only('first_name', 'last_name', 'login', 'role'));

        if (Request::get('password')) {
            $user->update(['password' => Hash::make(Request::input('password'))]);
        }

        return Redirect::back()->with('success', 'Пользователь обновлен.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return Redirect::back()->with('success', 'Пользователь удален.');
    }

    public function restore(User $user)
    {
        $user->restore();

        return Redirect::back()->with('success', 'Пользователь восстановлен.');
    }
}
