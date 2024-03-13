<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Range;
use Illuminate\Contracts\Support\ValidatedData;

class RangeController extends Controller
{
    public function index()
    {
        $ranges = Range::all();
        return response()->json($ranges, 200);
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'places' => 'required|integer|max:255',
            'category_id' => 'required|integer',
            'location' => 'required|string|max:255',

        ]);

        $range = Range::create([
            'places' => $validateData['places'],
            'category_id' => $validateData['category_id'],
            'location' => $validateData['location']
        ]);

        return response()->json($range, 201);
    }

    public function show(Range $range)
    {
        return response()->json($range);
    }

    public function update(Request $request, Range $range)
    {
        $validateData = $request->validate([
            'places' => 'nullable|integer|max:255',
            'category_id' => 'nullable|integer',
            'location' => 'nullable|string|max:255',
        ]);

        // $range->places = $validateData['places']?? $range->places;
        // $range->category_id = $validateData['category_id']?? $range->category_id;
        // $range->location = $validateData['location']?? $range->location;


        // $range->save();
        $range->update($validateData);

        return response()->json($range);
    }

    public function destroy(Range $range)
    {
        $range->delete();
        return response()->json(['message' => 'Range deleted successfully'], 200);
    }



    public function getProductsInRange($rangeId)
    {
        $range = Range::with('products')->find($rangeId);

        if (!$range) {
            return response()->json(['error' => 'Rayon non trouvé'], 404);
        }
        if (count($range->Products) == 0) {
            return response()->json([
                "message" => "Aucun résultat correspondant à cette recherche."
            ]);
        }
        return response()->json([
            'products' => $range->Products,
            'range' => $range
        ]);
    }
}
