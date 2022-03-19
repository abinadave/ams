<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DeliverForm;
use App\PurchaseOrder;
use Auth;
use Log;
use DB;
class DeliverFormController extends Controller
{
	
	public function insertAprDelivery(Request $request){
		# Deliver for APR (Insert / Save)

		$request->validate([
		    'purpose' => 'required|max:100|min:4',
		    // 'iar_no' => 'numeric',
		    'charge_invoice_no' => 'required|numeric|unique:deliver_forms',
		    'date_of_invoice' => 'required',
		    'date_of_delivery' => 'required',
		    'date_received' => 'required',
		]);
		$form = new DeliverForm;
		$form->purpose = $request->purpose;
		$form->charge_invoice_no = $request->charge_invoice_no;
		$form->date_of_invoice = $request->date_of_invoice;
		$form->date_of_delivery = $request->date_of_delivery;
		$form->date_received = $request->date_received;
		$form->type = 'apr';
		$form->apr_id = $request->apr_id;
		$form->iar_no = ($request->iar_no > 0 == true) ? $request->iar_no : 0;
		$form->transact_by = Auth::user()->id;
		$form->save();
		if ($request->iar_no > 0) {
			# purchase order is completed
			DB::table('apr_forms')->where('apr_no', $form->apr_no)->update(['delivery_completed' => 1]);
		}
		return response()->json($form);
		// dd($request);
	}
	public function savePoDeliveryForm(Request $request){
		// dd($request);
		$request->validate([
		    'purpose' => 'required',
		    'supplier_id' => 'required',
		    'charge_invoice_no' => 'required',
		    'date_of_invoice' => 'required',
		    'date_of_delivery' => 'required',
		    'date_received' => 'required',
		]);
		$form = new DeliverForm;
		$form->po_id = $request->po_id;
		$form->charge_invoice_no = $request->charge_invoice_no;
		$form->date_of_invoice = $request->date_of_invoice;
		$form->date_of_delivery = $request->date_of_delivery;
		$form->date_received = $request->date_received;
		$form->iar_no = ($request->iar_no > 0 == true) ? $request->iar_no : 0;
		$form->purpose = $request->purpose;
		$form->type = "po";
		$form->save();
		if ($request->iar_no > 0) {
			# purchase order is completed
			DB::table('purchase_orders')->where('id', $form->po_id)->update(['delivery_completed' => 1]);
		}
		return response()->json($form);
		/** 
			"form" => array:10 [
		        "supplier_id" => 10
		        "po_number" => 33
		        "po_year" => 2019
		        "po_month" => 1
		        "charge_invoice_no" => "111"
		        "date_of_invoice" => "2019-03-01"
		        "date_of_delivery" => "2019-03-02"
		        "date_received" => "2019-03-03"
		        "iar_no" => "2222"
		        "purpose" => "secret"
		      ]
	     **/
	}
	public function advancedSearch(Request $request){
		// ($user['permissions'] == 'admin') ? true : false;
		$arr = DB::table('deliver_form_views')->where('supplier_id', ($request->supplier_id == null) ? "" : $request->supplier_id)
					->orWhere('po_number', ($request->po_number == null) ? "" : $request->po_number)
					->orWhere('po_year', ($request->po_year == null) ? "" : $request->po_year)
					->orWhere('po_month', ($request->po_month == null) ? "" : $request->po_month)
					->orWhere('charge_invoice_no', $request->charge_invoice_no)
					->orWhere('apr_no', ($request->apr_no == null) ? "" : $request->apr_no)
					->orWhere('date_of_delivery', ($request->date_of_delivery == null) ? "" : $request->date_of_delivery)
					->orWhere('date_received', ($request->date_received == null) ? "" : $request->date_received)
					->orWhere('iar_no', ($request->iar_no == null) ? "" : $request->iar_no)
					->orWhere('purpose', ($request->purpose == null) ? "" : $request->purpose)
					->orderBy('id','desc')->get();
		return response()->json($arr);
	}
	public function fetchDeliveryForms(Request $request){
		return DB::table('deliver_form_views')->where('po_id', $request->id)->orderBy('id','desc')->get();
	}
	public function fetchByAprId(Request $request){
		return DB::table('deliver_form_views')->where('apr_id', $request->id)->orderBy('id','desc')->get();
	}
	public function fetchDeliverFormViews(){
		return DB::table('deliver_form_views')->skip(0)->take(15)->orderBy('id','desc')->get();
	}
    public function insert(Request $request){
    	$request->validate([
		    'purpose' => 'required',
		    'supplier_id' => 'required'
		]);
		$supplier_id = $request->supplier_id;
		$po_id = 0;
		// Log::info('Check', array('supplier_id' => $supplier_id, 'purchase_order' => $request->all()));
		if ($supplier_id == 1) {
			$purchaseOrder = PurchaseOrder::where('apr_no', $request->apr_no)->first();
			$po_id = $purchaseOrder->id;
		}else {
			Log::info('po number', array('po_number' => $request->po_number));
			$purchaseOrder = PurchaseOrder::where('po_number', $request->po_number)->first();
			$po_id = $purchaseOrder->id;
		}
		$deliver_form = new DeliverForm;
		$deliver_form->po_id = $po_id;
		$deliver_form->po_number = $request->po_number;
		$deliver_form->po_year = $request->po_year;
		$deliver_form->po_month = $request->po_month;
		$deliver_form->purpose = $request->purpose;
		$deliver_form->charge_invoice_no = $request->charge_invoice_no;
		$deliver_form->date_of_invoice = $request->date_of_invoice;
		$deliver_form->apr_no = $request->apr_no;
		$deliver_form->date_of_apr = $request->date_of_apr;
		$deliver_form->date_of_delivery = $request->date_of_delivery;
		$deliver_form->date_received = $request->date_received;
		$deliver_form->iar_no = $request->iar_no;
		$deliver_form->purpose = $request->purpose;
		$deliver_form->transact_by = Auth::user()->id;

		$deliver_form->save();
		return response()->json($deliver_form);
    }
}
