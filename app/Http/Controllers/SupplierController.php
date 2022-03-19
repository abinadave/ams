<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Supplier;

class SupplierController extends Controller
{
    public function fetchSupName(Request $request){
        $po = $request->po;
        $supplier_id = $po['supplier_id'];
        return response()->json(Supplier::findOrFail($supplier_id));
    }
	public function fetch(){
		return response()->json(Supplier::orderBy('name','desc')->get());
	}
    public function insert(Request $request){
    	$request->validate([
    		'name' => 'required|max:100|unique:suppliers'
    	]);
    	$supplier = Supplier::create($request->all());
    	return response()->json($supplier);
    }
}
