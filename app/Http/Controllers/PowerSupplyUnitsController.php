<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PowerSupplyUnitsController extends Controller
{
    public function index()
    {
       return PowerSupplyUnits::all();
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'psu_name'           => 'required|string',
            'brand'              => 'required|string',
            'wattage'            => 'required|integer',
            'efficiency_rating'  => 'required|string',
        ]);

        $power_supply_units = PowerSupplyUnits::create($fields);

        return response()->json(['message'=> 'Created successful'],200);
    }

    public function show($id)
    {
        $power_supply_units = PowerSupplyUnits::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Power Supply Unit data found successfully',
            'data' => $power_supply_units
        ], 200);
    }



public function update(Request $request, $power_supply_units)
{
    $fields = $request->validate([
        'psu_name'           => 'required|string',
        'brand'              => 'required|string',
        'wattage'            => 'required|integer',
        'efficiency_rating'  => 'required|string',
    ]);

    $power_supply_units = PowerSupplyUnits::find($power_supply_units);

    $power_supply_units->update($fields);

    return response()->json($power_supply_units, 201);
}



    public function destroy($power_supply_units)
    {
        $power_supply_units = PowerSupplyUnits::find($power_supply_units);
        $power_supply_units->delete();

        return response()->json([
            'message' => 'Power Supply Unit data deleted successfully'
        ], 200);
    }}
