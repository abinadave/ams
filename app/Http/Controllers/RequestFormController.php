<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RequestForm;
use App\RequestFormViews;
use Auth;
use Log;
use DB;

class RequestFormController extends Controller
{
	public function autocompleteForm(Request $request){
		$prop = $request->prop;
		$val = $request->val;
		$autocomplete_props = array();
		if(is_array($request->prop)){
			foreach ($prop as $key) {
				$arr = RequestForm::where($key,  'like', '%'. $val . '%')
						->distinct($key)
						->select($key)
						->orderBy($key)
						->get();
				array_push($autocomplete_props, [
					$key => $arr,
					'prop' => $key
				]);
			}
		}
		
		return response()->json([
			'autocomplete_props' => $autocomplete_props,
			'columns' => $prop,
			'value'  => $val
		]);
	}

	public function searchUsingRisNo(Request $request){
		#parameters: array:2 [
	    // "str" => array:3 [
	    //     0 => "2019"
	    //     1 => "8"
	    //     2 => "97"
	    //   ]
	    //   "search" => "2019-8-97"
	    // ]
	    $arr = RequestFormViews::where('ris_year', $request->str[0])
	    			->where('ris_month', $request->str[1])
	    			->where('ris_number', $request->str[2])->get();
	    return response()->json($arr);
	}

	public function updateForm(Request $request){
		$request->validate([
		    'ris_year' => 'required',
		    'ris_month' => 'required',
		    'ris_number' => 'required',
		    'division' => 'required',
		    'requested_by' => 'required',
		    'date_requested' => 'required'
		]);
		$req_form = RequestForm::findOrFail($request->id);
		$req_form->ris_year = $request->ris_year;
		$req_form->ris_month = $request->ris_month;
		$req_form->ris_number = $request->ris_number;
		$req_form->division = $request->division;
		$req_form->office = $request->office;
		$req_form->resp_center_code = $request->resp_center_code;
		$req_form->fund_cluster = $request->fund_cluster;
		$req_form->requested_by = $request->requested_by;
		$req_form->approved_by = $request->approved_by;
		$req_form->issued_by = $request->issued_by;
		$req_form->received_by = $request->received_by;
		$req_form->transact_by = Auth::user()->id;
		$req_form->date_requested = $request->date_requested;
		$req_form->save();
		$re_form_view = RequestFormViews::where('id', $request->id)->first();
		return response()->json($re_form_view);
	}
	public function getLastInsertId(){
		$max = RequestForm::max('ris_number');
		return response()->json([
			'max_ris_number' => $max
		]);
	}
	public function validateForm(Request $request){
		$count = RequestForm::where('ris_year', $request->ris_year)
						->where('ris_month', $request->ris_month)
						->where('ris_number', $request->ris_number)
						->count();
		// Log::info('request', array('request' => $request));
		return response()->json(['count' => $count]);
	}
	public function search(Request $request){
		$search = $request->search;
		if (!$search) {
			# this prevents loading too long after searching ""
			return $this->fetchLimit();
		}else {
			$arr = RequestFormViews::where('division', 'like', '%'. $search . '%')
					->orWhere('office', 'like', '%'. $search . '%')
					->orWhere('resp_center_code', 'like', '%'. $search . '%')
					->orWhere('fund_cluster', 'like', '%'. $search . '%')
					->orWhere('requested_by', 'like', '%'. $search . '%')
					->orWhere('approved_by', 'like', '%'. $search . '%')
					->orWhere('issued_by', 'like', '%'. $search . '%')
					->orWhere('received_by', 'like', '%'. $search . '%')
					->orWhere('item_list', 'like', '%'. $search . '%')
					->orWhere('ris_year', 'like', '%'. $search . '%')
					->orWhere('ris_month', 'like', '%'. $search . '%')
					->orWhere('ris_number', 'like', '%'. $search . '%')
					->orderBy('id','desc')
					->get();
			return response()->json($arr);
		}
		
	}
	public function fetchLimit(){
		$arr = DB::table('request_form_views')->skip(0)->take(15)->orderBy('date_requested','desc')->get();
		// $arr = RequestFormViews::orderBy('id','desc')->get();
		return response()->json($arr);
	}
    public function insert(Request $request){
    	$request->validate([
		    'ris_year' => 'required',
		    'ris_month' => 'required',
		    'ris_number' => 'required',
		    'division' => 'required',
		    'requested_by' => 'required',
		    'date_requested' => 'required'
		]);
		$req_form = new RequestForm;
		$req_form->ris_year = $request->ris_year;
		$req_form->ris_month = $request->ris_month;
		$req_form->ris_number = $request->ris_number;
		$req_form->division = $request->division;
		$req_form->office = $request->office;
		$req_form->resp_center_code = $request->resp_center_code;
		$req_form->fund_cluster = $request->fund_cluster;
		$req_form->requested_by = $request->requested_by;
		$req_form->approved_by = $request->approved_by;
		$req_form->issued_by = $request->issued_by;
		$req_form->received_by = $request->received_by;
		$req_form->transact_by = Auth::user()->id;
		$req_form->date_requested = $request->date_requested;
		$req_form->save();
		return response()->json($req_form);
    }
}
