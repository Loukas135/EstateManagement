<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estate;

class AdminController extends Controller
{
    public function approve($id)
    {
        $estate = Estate::find($id);
        $estate->active = true;
        $estate->save();
        return response()->json([
            'message' => 'approved',
            'estate' => $estate
        ], 200);
    }

    public function show_unapproved()
    {
        $estates = Estate::where('active', false);
        return response()->json([
            'message' => 'these are the unapproved',
            'estates' => $estates
        ], 200);
    }
}
