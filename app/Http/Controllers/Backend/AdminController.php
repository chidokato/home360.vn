<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CustomerInquiry;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function login()
    {
        return view('backend.auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->route('backend.admin.dashboard');
        }

        return back()
            ->withErrors(['email' => 'Thong tin dang nhap khong chinh xac.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('backend.admin.login');
    }

    public function index()
    {
        $stats = [
            'properties' => Post::query()->where('type', Post::TYPE_PRODUCT)->count(),
            'agents' => User::query()->count(),
            'customers' => CustomerInquiry::query()->count(),
            'pending_posts' => Post::query()->where('type', Post::TYPE_PRODUCT)->where('is_active', false)->count(),
        ];

        $latestInquiries = CustomerInquiry::query()
            ->latest()
            ->limit(3)
            ->get();

        $activities = $latestInquiries->map(function (CustomerInquiry $inquiry) {
            return [
                'title' => 'Khach hang moi: ' . $inquiry->name,
                'detail' => trim($inquiry->phone . ($inquiry->project_title ? ' | ' . $inquiry->project_title : '')),
            ];
        })->all();

        if ($activities === []) {
            $activities = [
                ['title' => 'Chua co khach hang moi', 'detail' => 'Form thong tin khach hang se hien thi tai day sau khi co nguoi gui.'],
            ];
        }

        return view('backend.admin.dashboard_content', compact('stats', 'activities'));
    }
}
