<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AprForm;
use Auth;
use App\AprFormViews;
class AprFormController extends Controller
{
    public function update(Request $request){
        $aprForm = AprForm::findOrFail($request->id);
        $aprForm->apr_no = $request->apr_no;
        $aprForm->apr_date = $request->apr_date;
        $aprForm->save();
        return response()->json($aprForm);
    }
    public function checkAprNoDuplicate(Request $request){
        $apr_no = $request->apr_no;
        $count = AprForm::where('apr_no', $apr_no)->count();
        return response()->json(array('count' => $count));
    }
    public function search(Request $request){
        $search = $request->search;
        $arr = AprFormViews::where('apr_no', 'like', '%' . $search . '%')
                ->orWhere('items', 'like', '%' . $search . '%')
                ->orWhere('total_amount', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%')
                ->get();
        return response()->json($arr);
    }
    public function fetchFormsViews(){
        $forms = AprFormViews::orderBy('id','desc')->get();
        return response()->json($forms);
    }
    public function insert(Request $request){
    	$request->validate([
		    'apr_no' => 'required|unique:apr_forms|max:100',
		    'apr_date' => 'required',
		]);
    	$apr_form = new AprForm;
    	$apr_form->apr_no = $request->apr_no;
    	$apr_form->apr_date = $request->apr_date;
    	$apr_form->encoder = Auth::user()->id;
    	$apr_form->save();
    	return response()->json($apr_form);
    }
    public function aprFormViews(){
        echo "Good one";
    }
}
