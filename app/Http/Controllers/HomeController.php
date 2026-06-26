<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Partner;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
        // Mengambil semua event terbaru beserta kategorinya
        $events = Event::with('category')->latest()->get();
        
        // Mengambil data partner untuk bagian logonya di bawah halaman
        $partners = Partner::all(); 

        return view('home', compact('events', 'partners'));
    }
}