<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(){
        $allEvent = Event::all();
        return response()->json($allEvent);
    }

    public function store(Request $request){
        $validatedData = $request->validate([
            'nama_event' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:255',
            'tanggal_mulai' => 'required|string|max:255',
            'tanggal_selesai' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
        ]);

        $userId = Auth::id();

        $event = Event::create([
            'id_user' => $userId,
            'nama_event' => $validatedData['nama_event'],
            'deskripsi' => $validatedData['deskripsi'],
            'tanggal_mulai' => $validatedData['tanggal_mulai'],
            'tanggal_selesai' => $validatedData['tanggal_selesai'],
            'lokasi' => $validatedData['lokasi'],
        ]);

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $event,
        ],201);
    }

    public function update(Request $request, string $id){
        $validateData = $request->validate([
            'nama_event' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:255',
            'tanggal_mulai' => 'required|string|max:255',
            'tanggal_selesai' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
        ]);

        $userId = Auth::id();

        $event = Event::find($id);

        if(!$event|| $event->id_user != $userId){
            return response()->json(['message' => 'Event tidak ditemukan'], 403);
        }

        $event->update($validateData);

        return response()->json($event);
    }

    public function destroy(string $id){
        $userId = Auth::id();

        $event = Event::find($id);

        if(!$event || $event->id_user !== $userId){
            return response()->json(['message' => 'Event tidak ditemukan atau anda tidak login'], 403);
        }

        $event->delete();

        return response()->json(['message' => 'Event berhasil dihapus']);
    }
}
