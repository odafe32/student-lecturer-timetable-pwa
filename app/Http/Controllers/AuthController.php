<?php

namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class AuthController extends Controller

{
    public function Welcome()
    {
            return view('welcome', [
                'title' => 'Affan Student Timetable',
                'description' => 'A smart and user-friendly timetable management tool for students',
                'ogImage' => url('favicon.ico'),
            
            ]);
    }
    
       //login
    public function showLogin(){
        return view('auth.login', [
            'title' => 'Login - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('favicon.ico'),
        ]);
    }
 
       //forget password
    public function ShowForgotPassword(){
        return view('auth.forget-password', [
            'title' => 'Forget Password - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('favicon.ico'),
        ]);
    }
 
    
    public function Otp(){
        return view('auth.otp', [
            'title' => 'Verify Account - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('favicon.ico'),
        ]);
    }
 
    
}