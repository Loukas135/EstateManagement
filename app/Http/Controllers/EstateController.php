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
            'bathrooms' => 'required',
            'garages' => 'required',
            'title' => 'required|string'
        ]);

        $images = $request->validate([
            'images.*' => 'image',
            'property.*' => 'image',
        ]);

        if ($data && $images) {
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
                'bathrooms' => $data['bathrooms'],
                'garages' => $data['garages'],
                'title' => $data['title'],
            ]);

            if ($newEstate) {
                $newEstate->save();

                foreach ($request->images as $img) {
                    $imageName = $img->hashName();
                    Storage::disk('estate_images')->put($imageName, file_get_contents($img));
                    EstateImage::create([
                        'estate_id' => $newEstate->id,
                        'user_id' => $currentUser->id,
                        'image_path' => Storage::disk('estate_images')->url($imageName),
                    ]);
                }

                foreach ($request->property as $property) {
                    $imageName = $property->hashName();
                    Storage::disk('property_images')->put($imageName, file_get_contents($property));
                    PropertyImage::create([
                        'estate_id' => $newEstate->id,
                        'user_id' => $currentUser->id,
                        'image_path' => Storage::disk('property_images')->url($imageName),
                    ]);
                }

                return response()->json([
                    'message' => 'Estate has been added',
                    'estate' => $newEstate
                ], 201);
            } else {
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

    public function get_all(Request $request)
    {

        $query = Estate::query();


        if ($request->has('q')) {
            $searchTerm = $request->input('q');
            $query->where(function ($query) use ($searchTerm) {
                $query->where('description', 'like', '%' . $searchTerm . '%')
                    ->orWhere('city', 'like', '%' . $searchTerm . '%')
                    ->orWhere('street', 'like', '%' . $searchTerm . '%')
                    ->orWhere('title', 'like', '%' . $searchTerm . '%');
            });
        }


        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->input('city') . '%');
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        if ($request->has('min_space')) {
            $query->where('space', '>=', $request->input('min_space'));
        }

        if ($request->has('max_space')) {
            $query->where('space', '<=', $request->input('max_space'));
        }

        if ($request->has('min_rooms')) {
            $query->where('number_of_rooms', '>=', $request->input('min_rooms'));
        }

        if ($request->has('max_rooms')) {
            $query->where('number_of_rooms', '<=', $request->input('max_rooms'));
        }

        if ($request->has('min_bathrooms')) {
            $query->where('bathrooms', '>=', $request->input('min_bathrooms'));
        }

        if ($request->has('max_bathrooms')) {
            $query->where('bathrooms', '<=', $request->input('max_bathrooms'));
        }

        if ($request->has('min_garages')) {
            $query->where('garages', '>=', $request->input('min_garages'));
        }

        if ($request->has('max_garages')) {
            $query->where('garages', '<=', $request->input('max_garages'));
        }

        $estates = $query->with("estate_images")->get();

        return response()->json([
            'message' => 'Estates retrived succesfully',
            'estates' => $estates
        ], 200);
    }

    public function get_by_id($id)
    {
        $estate = Estate::with("estate_images", "property_images", "user")->find($id);
        if ($estate) {
            return response()->json([
                'message' => 'Estate retrived succesfully',
                'estate' => $estate
            ], 200);
        }
    }

    public function update(Request $request, $id)
    {
        $estate = Estate::find($id);


        $data = $request->validate([
            'description' => 'required|string|max:255',
            'category' => 'required|in:Farm,Appartment,House',
            'city' => 'required|string',
            'street' => 'required|string',
            'space' => 'required|numeric',
            'price' => 'required|numeric',
            'number_of_rooms' => 'required|integer',
            'bathrooms' => 'required|integer',
            'garages' => 'required|integer',
        ]);

        if ($data) {

            $updated = $estate->update($data);

            if ($updated) {
                return response()->json([
                    'message' => 'Updated',
                    'estate' => $estate
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



    public function soldEstate(Request $request, $id)
    {
        $estate = Estate::find($id);
        $estate->sold = true;
        $estate->save();
        return response()->json(["message" => "estate updated succesfuly"]);
    }

    public function showSellerEstates(Request $request)
    {
        $seller = $request->user();
        $query = Estate::query()->where('user_id', $seller->id);


        if ($request->has('q')) {
            $searchTerm = $request->input('q');
            $query->where(function ($query) use ($searchTerm) {
                $query->where('description', 'like', '%' . $searchTerm . '%')
                    ->orWhere('city', 'like', '%' . $searchTerm . '%')
                    ->orWhere('street', 'like', '%' . $searchTerm . '%')
                    ->orWhere('title', 'like', '%' . $searchTerm . '%');
            });
        }


        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->input('city') . '%');
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        if ($request->has('min_space')) {
            $query->where('space', '>=', $request->input('min_space'));
        }

        if ($request->has('max_space')) {
            $query->where('space', '<=', $request->input('max_space'));
        }

        if ($request->has('min_rooms')) {
            $query->where('number_of_rooms', '>=', $request->input('min_rooms'));
        }

        if ($request->has('max_rooms')) {
            $query->where('number_of_rooms', '<=', $request->input('max_rooms'));
        }

        if ($request->has('min_bathrooms')) {
            $query->where('bathrooms', '>=', $request->input('min_bathrooms'));
        }

        if ($request->has('max_bathrooms')) {
            $query->where('bathrooms', '<=', $request->input('max_bathrooms'));
        }

        if ($request->has('min_garages')) {
            $query->where('garages', '>=', $request->input('min_garages'));
        }

        if ($request->has('max_garages')) {
            $query->where('garages', '<=', $request->input('max_garages'));
        }

        if ($request->has('active')) {
            $query->where('active', $request->input('active'));
        }
        $estates = $query->with("estate_images")->get();

        return response()->json([
            'message' => 'seller estates',
            'estates' => $estates
        ], 200);
    }
    public function show_unapproved()
    {
        $estates = Estate::with(["estate_images", "property_images", "user"])->where('active', false)->get();
        return response()->json([
            'message' => 'success',
            'estates' => $estates
        ], 200);
    }
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
}
