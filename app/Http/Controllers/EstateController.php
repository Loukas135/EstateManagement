<?php

namespace App\Http\Controllers;

use App\Models\Estate;
use App\Models\EstateImage;
use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EstateController extends Controller
{
    public function add(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string',
            'description' => 'required|string|max:255',
            'city' => 'required|string',
            'street' => 'required|string',
            'latitude' => 'required',
            'longitude' => 'required',
            'space' => 'required',
            'price' => 'required',
            'number_of_rooms' => 'required',
        ]);

        $images = $request->validate([
            'images.*' => 'image|mimes:png,jpg',
            'property.*' => 'image|mimes:png,jpg',
        ]);

        if($data && $images)
        {
            $currentUser = $request->user();
            $newEstate = Estate::create([
                'category' => $data['category'],
                'description' => $data['description'],
                'city' => $data['city'],
                'street' => $data['street'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'space' => $data['space'],
                'price' => $data['price'],
                'number_of_rooms' => $data['number_of_rooms'],
                'user_id' => $currentUser->id,
            ]);
            
            if($newEstate){
                $newEstate->save();

                foreach($request->images as $img)
                {
                    $imageName = time().'.'.$img->getClientOriginalExtension();
                    Storage::disk('estate_images')->put($imageName, file_get_contents($img));
                    //$img->storeAs('estate_images', $imageName);    
                    //the right above might be the right one (f*ck postman);
                    EstateImage::create([
                        'estate_id' => $newEstate->id,
                        'user_id' => $currentUser->id,
                        'image_path' => 'public/storage/estate_images'.$imageName
                    ]);
                }
    
                foreach($request->property as $property)
                {
                    $imageName = time().'.'.$property->getClientOriginalExtension();
                    Storage::disk('property_images')->put($imageName, file_get_contents($property));
                    //$img->storeAs('estate_images', $imageName);    
    
                    PropertyImage::create([
                        'estate_id' => $newEstate->id,
                        'user_id' => $currentUser->id,
                        'image_path' => 'public/storage/property_images'.$imageName
                    ]);
                }

                return response()->json([
                    'message' => 'Estate has been added',
                    'estate' => $newEstate
                ], 201);
            }else{
                return response()->json([
                    'message' => 'something went wrong'
                ], 500);
            }
        }

        return response()->json([
            'message' => 'bad request'
        ], 400);
    }

    public function delete($id)
    {
        $estate = Estate::find($id);
        $estate->delete();
        return response()->json([
            'message' => 'Estate has been deleted'
        ], 204);
    }

    public function get_all()
    {
        $estates = Estate::get();
        if($estates){
            return response()->json([
                'message' => 'Estates retrived succesfully',
                'estate' => $estates
            ], 200);
        }
    }

    public function get_by_id($id)
    {
        $estate = Estate::find($id);
        if($estate){
            return response()->json([
                'message' => 'Estate retrived succesfully',
                'estate' => $estate
            ], 200);
        }
    }

    public function update(Request $request, $id)
    {
        $estate = Estate::find($id)->first();

        $data = $request->validate([
            'description' => 'required|string|max:255',
            'city' => 'required|string',
            'street' => 'required|string',
            'space' => 'required',
            'price' => 'required',
            'number_of_rooms' => 'required'
        ]);

        if($data){
            $updated = $estate->update($data);

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

    public function show_seller_estates(Request $request)
    {
        $seller = $request->user();
        $sellerEstates = Estate::where('user_id', $seller->id);
        return response()->json([
            'message' => 'seller\'s estates',
            'estates' => $sellerEstates
        ], 200);
    }

    public function filter_by_things($things, $number_of_things)
    {
        $estates = Estate::where($things, $number_of_things)->get();
        if($estates)
        {
            return response()->json([
                'message' => 'Here are the results',
                'estates' => $estates
            ], 200);
        }

        return response()->json([
            'message' => 'No results'
        ], 404);
    }
}
