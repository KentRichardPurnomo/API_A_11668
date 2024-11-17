<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SponsorController extends Controller
{
    public function index()
    {
        $allSponsor = Sponsor::all();
        return response()->json($allSponsor);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_event' => 'required|exists:events,id',
            'nama_sponsor' => 'required|string|max:255',
            'kontribusi' => 'required|in:tunai,barang,tunai-barang',
            'level' => 'required',
            'tanggal_transaksi' => 'required|date',
        ], [
            'kontribusi.in' => 'Kontribusi hanya boleh berupa tunai, barang, atau tunai-barang.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $userId = Auth::id();
        $event = Event::find($validatedData['id_event']);

        if (!$event || $event->id_user !== $userId) {
            return response()->json(['message' => 'Event tidak ditemukan atau Anda tidak memiliki akses'], 403);
        }

        $kontribusiValue = match ($validatedData['kontribusi']) {
            'tunai' => 1,
            'barang' => 2,
            'tunai-barang' => 3,
        };

        $sponsor = Sponsor::create([
            'id_user' => $userId,
            'id_event' => $validatedData['id_event'],
            'nama_sponsor' => $validatedData['nama_sponsor'],
            'kontribusi' => $kontribusiValue,
            'level' => $validatedData['level'],
            'tanggal_transaksi' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Berhasil membuat Sponsor',
            'post' => $sponsor,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $sponsor = Sponsor::find($id);
        if (!$sponsor) {
            return response()->json(['message' => 'Sponsor tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_event' => 'required|exists:events,id',
            'nama_sponsor' => 'required|string|max:255',
            'kontribusi' => 'required|in:tunai,barang,tunai-barang',
            'level' => 'required',
            'tanggal_transaksi' => 'required|date',
        ], [
            'kontribusi.in' => 'Kontribusi hanya boleh berupa tunai, barang, atau tunai-barang.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $userId = Auth::id();
        $event = Event::find($validatedData['id_event']);

        if (!$event || $event->id_user !== $userId) {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk mengubah sponsor ini'], 403);
        }

        $kontribusiValue = match ($validatedData['kontribusi']) {
            'tunai' => 1,
            'barang' => 2,
            'tunai-barang' => 3,
        };

        $sponsor->update([
            'id_event' => $validatedData['id_event'],
            'nama_sponsor' => $validatedData['nama_sponsor'],
            'kontribusi' => $kontribusiValue,
            'level' => $validatedData['level'],
            'tanggal_transaksi' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Sponsor berhasil diperbarui',
            'post' => $sponsor,
        ], 200);
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $sponsor = Sponsor::find($id);

        if (!$sponsor) {
            return response()->json(['message' => 'Sponsor tidak ditemukan atau Anda tidak login'], 404);
        }

        $sponsor->delete();
        return response()->json(['message' => 'Sponsor berhasil dihapus'], 200);
    }

    public function search($nama_sponsor)
    {
        $sponsors = Sponsor::where('nama_sponsor', 'LIKE', "%{$nama_sponsor}%")->paginate(10);

        if ($sponsors->isEmpty()) {
            return response()->json(['message' => 'Sponsor tidak ditemukan'], 404);
        }

        return response()->json([
            'post' => $sponsors->items(),
        ]);
    }
}
