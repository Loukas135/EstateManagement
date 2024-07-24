<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Extra;
use App\Models\Work;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr;

class ExtraController extends Controller
{
    public function get_all()
    {
        $extras = Extra::all();
        if ($extras) {
            return response()->json([
                'message' => 'Here are the extras',
                'extras' => $extras
            ], 200);
        }
    }

    public function get_by_id($id)
    {
        $extra = Extra::find($id)->first();
        if ($extra) {
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
            'category_id' => 'required',
            'name' => 'required|string',
            'contact_number' => 'required|string',
            'address' => 'required|string',
            'description' => 'required|string',
            'image'  => 'image',
        ]);

        if ($request->hasFile('image')) {
            $imageName = $request->image->hashName();
            Storage::disk('extra_images')->put($imageName, file_get_contents($request->image));
            $data['image'] = Storage::disk('extra_images')->url($imageName);
        }

        if ($data) {
            $updated = $extra->update($data);

            if ($updated) {

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

    public function add_work(Request $request){
        $data = $request->validate([
            'title' => 'string|required',
            'description' => 'string|required',
            'image' => 'image',
        ]);


        if($data){
            $currentUser = $request->user()->id;
            $extra = Extra::where('user_id', $currentUser)->get();
            //dd($extra[0]->id);
            $newWork = Work::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'image' => $data['image'],
                'extra_id' => $extra[0]->id
            ]);
            
            if ($request->hasFile('image')) {
                $imageName = $newWork->id;
                Storage::disk('works')->put($imageName, file_get_contents($request->image));
                $data['image'] = Storage::disk('works')->url($imageName);
            }
        }

        return response()->json([
            'message' => 'work_added',
        ], 201);
    }

    public function get_extra_works(Request $request)
    {
        $works = Work::Where('extra_id', $request->user()->id)->get();
        if ($works) {
            return response()->json([
                'message' => 'Success',
                'works' => $works
            ], 200);
        }
    }

    public function get_all_works()
    {
        $works = Work::all();
        if ($works) {
            return response()->json([
                'message' => 'Here_are_the_works',
                'works' => $works
            ], 200);
        }
    }

    public function get_work($id)
    {
        $work = Work::find($id)->first();
        if ($work) {
            return response()->json([
                'message' => 'Here_are_the_work',
                'works' => $work
            ], 200);
        }
    }
    
    
}

// public function add(Request $request)
//     {
//         $data = $request->validate([
//             'category' => 'required|string',
//             'name' => 'required|string',
//             'contact_number' => 'required|string',
//             'address' => 'required|string',
//             'description' => 'required|string',
//             'image'  => 'required|image'
//         ]);

//         if ($data) {
//             if ($request->hasFile('image')) {
//                 $imageName = $request->image->hashName();
//                 Storage::disk('extra_images')->put($imageName, file_get_contents($request->image));
//                 $data['image'] = Storage::disk('extra_images')->url($imageName);
//             }
//             $extra = Extra::create($data);

//             if ($extra) {
//                 return response()->json([
//                     'message' => 'Added successfuly',
//                     'data' => $extra
//                 ], 200);
//             }
//             return response()->json([
//                 'message' => 'Something went wrong',
//             ], 500);
//         }
//         return response()->json([
//             'message' => 'Wrong data',
//         ], 400);
//     }

