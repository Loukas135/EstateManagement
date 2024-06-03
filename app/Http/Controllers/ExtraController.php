<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Extra;

class ExtraController extends Controller
{
    public function add(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string',
            'name' => 'required|string',
            'contact_number' => 'required|string',
            'address' => 'required|string',
            'description' => 'required|string',
        ]);

        if($data){
            $extra = Extra::create([
                'category' => $data['category'],
                'name' => $data['name'],
                'contact_number' => $data['contact_number'],
                'address' => $data['address'],
                'description' => $data['description']
            ]);

            if($extra){
                return response()->json([
                    'message' => 'Added successfuly',
                    'extra' => $extra
                ], 200);
            }
            return response()->json([
                'message' => 'Something went wrong',
            ], 500);
        }
        return response()->json([
            'message' => 'Wrong data',
        ], 400);
    }

    public function get_all()
    {
        $extras = Extra::get();
        if($extras){
            return response()->json([
                'message' => 'Here are the extras',
                'extras' => $extras
            ], 200);
        }
    }

    public function get_by_id($id)
    {
        $extra = Extra::find($id)->first();
        if($extra){
            return response()->json([
                'message' => 'Here is the extra',
                'extras' => $extra
            ], 200);
        }
    }

    public function update(Request $request, $id)
    {
        $extra = Extra::find($id)->first();

        $data = $request->validate([
            'description' => 'required|string|max:255',
            'city' => 'required|string',
            'street' => 'required|string',
            'space' => 'required',
            'price' => 'required',
            'number_of_rooms' => 'required'
        ]);

        if($data){
            $updated = $extra->update($data);

            if($updated){

                return response()->json([
                    'message' => 'Updated',
                    'new estate' => $updated
                ]); 

            }

            return response()->json([
                'message' => 'Something went wrong'
            ], 500); 

        }

        return response()->json([
            'message' => 'Bad request'
        ], 400);
    }
    
}
