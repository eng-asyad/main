<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Medicine;
use Illuminate\Http\Request;
use App\Models\User;

class MedicineController extends Controller
{
    public function index()
    {
        return response()->json([
            'heading' => 'Latest Medicines',
            'medicine' => Medicine::latest()->filter(request(['classification', 'search']))->get()
        ]);
    }

    public function show($id)
    {
        $search = Medicine::find($id);
        if ($search) {
            return response()->json([
                'message' => 'success',
                "medicine" => $search
            ]);
        } else {
            return response()->json([
                "message" => "error"
            ]);
        }
    }

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'name' => ['required', 'unique:medicines'],
            'scientific_name' => 'required',
            'classification' => 'required',
            'company' => 'required',
            'quantity' => 'required',
            'expiration_date' => ['required', 'date'],
            'price' => 'required'
        ]);

        Medicine::create($formFields);

        return response()->json([
            'message' => 'success'
        ]);
    }
    public function update(Request $request, Medicine $medicine)
    {

        $formFields = $request->validate([
            'name' => ['required', 'unique:medicines'],
            'scientific_name' => 'required',
            'classification' => 'required',
            'company' => 'required',
            'quantity' => 'required',
            'expiration_date' => ['required', 'date'],
            'price' => 'required'
        ]);

        $medicine->update($formFields);

        return response()->json([
            'message' => 'success'
        ]);
    }

    public function add_favorite(Request $request)
    {
        $find = Medicine::find($request['name']);
        $user = User::where('token', $request['token']);
        if ($find) {
            $new_fav = [
                'user_name' => $user['name'],
                'medicine_name' => $request['name']
            ];
            Favorite::create($new_fav);
        }
    }

    public function remove_favorite(Request $request, $id)
    {
        $user = User::where('token', $request['token']);
        $favorite = Favorite::find($id);
        if ($favorite['user_name'] != $user['name']) {
            return response()->json([
                'message' => 'error'
            ]);
            abort(403);
        } else {
            $favorite->delete();
            return response()->json([
                'message' => 'success'
            ]);
        }
    }

    public function view_favorites(Request $request)
    {
        $user = User::where('token', $request['token']);

        return response()->json([
            'favorites' => Favorite::latest()->where('user_name', $user['name'])->get()
        ]);
    }

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();
        return response()->json([
            'message' => 'success'
        ]);
    }

}