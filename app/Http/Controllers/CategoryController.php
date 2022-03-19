<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
class CategoryController extends Controller
{
    public function fetch(){
    	return response()->json(Category::orderBy('id','desc')->get());
    }
}
