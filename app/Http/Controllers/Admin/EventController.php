<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index() {
        $events = Event::with('category')->latest()->get();
        return view('admin.events.index', compact('events'));
    }

    public function create() {
        $categories = Category::all();
        return view('admin.events.create', compact('categories'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'required',
            'date'        => 'required|date',
            'location'    => 'required',
            'price'       => 'required|numeric',
            'stock'       => 'required|numeric',
            'poster'      => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        if ($request->hasFile('poster')) {
            // Kita simpan ke variabel poster_path sesuai dengan pemanggilan di Blade kamu
            $data['poster_path'] = $request->file('poster')->store('posters', 'public');
        }

        Event::create($data);
        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dibuat.');
    }

    // TAMBAHKAN FUNGSI INI agar user biasa bisa melihat detail event tanpa error
    public function show(Event $event) 
    {
        // Mengambil data kategori untuk dikirim ke view jika dibutuhkan relasinya
        $event->load('category'); 

        // Mengembalikan ke file resources/views/event_detail.blade.php
        return view('event_detail', compact('event'));
    }

    public function edit(Event $event) {
        $categories = Category::all();
        return view('admin.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event) {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'required',
            'date'        => 'required|date',
            'location'    => 'required',
            'price'       => 'required|numeric',
            'stock'       => 'required|numeric',
            'poster'      => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        if ($request->hasFile('poster')) {
            if ($event->poster_path) Storage::disk('public')->delete($event->poster_path);
            $data['poster_path'] = $request->file('poster')->store('posters', 'public');
        }

        $event->update($data);
        return redirect()->route('admin.events.index')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event) {
        if ($event->poster_path) Storage::disk('public')->delete($event->poster_path);
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dihapus.');
    }
}