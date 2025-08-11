<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display the application settings.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // You might fetch application settings from a database or config here
        // $settings = \App\Models\Setting::first();
        // return view('admin.settings.index', compact('settings'));

        return view('admin.settings.index');
    }
}