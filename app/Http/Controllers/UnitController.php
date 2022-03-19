<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Unit;
use Auth;
use App\UnitViews;

class UnitController extends Controller
{
    public function editUnit(Request $request){
        $unit = $request->unit;
        $newUnitName = $request->newUnitName;
        $respUpdate = Unit::where('id', $unit['id'])
                        ->update(['unit_name' => $newUnitName]);
        return response()->json([
            'update' => $respUpdate
        ]);
    }
    public function deleteone(Request $request){
        $id = $request->unit_id;
        $resp = Unit::destroy($id);
        return response()->json(['deleted' => $resp]);
    }
	public function fetchunitviews(){
		return response()->json(UnitViews::orderBy('unit_name','asc')->get());
	}
    public function fetch(){
    	return response()->json(Unit::orderBy('unit_name','asc')->get());
    }
    public function insert(Request $request){
    	$unit_name = $request->unit_name;
    	$unit = new Unit;
    	$unit->unit_name = $unit_name;
    	$unit->created_by = Auth::user()->id;
    	$unit->save();
    	return response()->json($unit);
    }
}
