<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\POCategory;
class POCategoryController extends Controller
{
    public function fetch(){
    	return response()->json(POCategory::all());
    }
}
