<?php

namespace App\Http\Controllers;

use App\Models\Estate;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function filter_by_number_of_rooms($number_of_rooms)
    {
        $estates = Estate::where('number_of_rooms', $number_of_rooms);
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

    public function filter_by_space($max, $min)
    {
        $estates = Estate::whereBetween('space', [$max, $min])->get();
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

    public function filter_by_number_of_garages($number_of_garages)
    {
        $estates = Estate::where('garages', $number_of_garages);
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

    public function filter_by_number_of_bathrooms($number_of_bathrooms)
    {
        $estates = Estate::where('bathrooms', $number_of_bathrooms);
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

    public function filter_by_bedrooms($number_of_bedrooms)
    {
        $estates = Estate::where('bedrooms', $number_of_bedrooms);
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

    public function filter_by_things($things, $number_of_things)
    {
        $estates = Estate::where($things, $number_of_things);
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
