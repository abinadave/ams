<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseOrder;
use App\PurchaseOrderViews;
use App\PurchaseItemsView;
use Auth;
use DB;
use Log;
class PurchaseOrderController extends Controller
{
	public function updatePoDeliveryCompleted(Request $request){
		$po = $request->current_po;
		DB::table('purchase_orders')
			->where('id', $po['id'])
			->update(['delivery_completed' => 1]);
		return response()->json(PurchaseOrderViews::where('id', $po['id'])->first());
	}
	public function advancedSearch(Request $request){
		$arr = DB::table('purchase_order_views')->where('supplier_id', ($request->supplier_id == null) ? "" : $request->supplier_id)
					->orWhere('po_number', ($request->po_number == null) ? "" : $request->po_number)
					->orWhere('po_year', ($request->po_year == null) ? "" : $request->po_year)
					->orWhere('po_month', ($request->po_month == null) ? "" : $request->po_month)
					->orWhere('date', ($request->date == null) ? "" : $request->date)
					->orWhere('po_category', ($request->po_category == null) ? "" : $request->po_category)
					->orderBy('id','desc')->get();
		if ($request->supplier_id == null && 
			$request->date == null && 
			$request->po_category == null &&
			$request->po_year != null &&
			$request->po_month != null &&
			$request->po_number != null) {
			echo "search for po.";
		}else {
			echo "search all";
		}
		// return response()->json($arr);
	}

	public function filterPoBySupplierId(Request $request){
		$supplier_id = $request->supplier_id;
		$po_selection = array();
		if ($supplier_id) {
			$po_selection = PurchaseOrderViews::where('supplier_id', $supplier_id)->orderBy('id','desc')->get();
		}
		return response()->json($po_selection);
	}

	public function paginatePo()
    {
        $purchaseOrderViews = PurchaseOrderViews::orderBy('id','desc')->paginate(15);
        // $users = DB::table('users')->simplePaginate(15);
        return response()->json($purchaseOrderViews);
    }
    public function fetchLimit(){
    	return $this->paginatePo();
    	// return PurchaseOrderViews::orderBy('id','desc')->get();
    	// return DB::table('purchase_order_views')->skip(0)->take(15)->orderBy('id','desc')->get();
    }
    public function searchAprNo(Request $request){
    	$apr_no = $request->apr_no;
		$count = PurchaseOrder::where('apr_no', $apr_no)->count();
		$purchase_items = array();
		$po = array();
		if ($count) {
			$po = PurchaseOrderViews::where('apr_no', $apr_no)->first();
			$purchase_items = PurchaseItemsView::where('apr_no', $apr_no)->get();
		}
		return response()->json([
			'count' => $count,
			'po' => $po,
			'purchase_items' => $purchase_items
		]);
    }
    public function searchPoNumber(Request $request){
		$po_number = $request->po_number;
		$po_year = $request->po_year;
		$po_month = $request->po_month;
		$count = PurchaseOrder::where('po_number', $po_number)
							  ->where('po_year', $po_year)
							  ->where('po_month', $po_month)
							  ->count();
		$purchase_items = array();
		$po = array();
		if ($count) {
			$po = PurchaseOrderViews::where('po_number', $po_number)
								  ->where('po_year', $po_year)
								  ->where('po_month', $po_month)
								  ->first();
			$purchase_items = PurchaseItemsView::where('po_id', $po->id)->get();
		}
		return response()->json([
			'count' => $count,
			'po' => $po,
			'purchase_items' => $purchase_items
		]);
	}
	public function validatePONumber(Request $request){
		$po_number = $request->po_number;
		$request->validate([
		    'po_number' => 'required|Numeric|unique:purchase_orders'
		]);
		echo "good";
	}
	public function search(Request $request){
		$search = $request->search;
		$arr = array();
		if ($search) {
			$arr = PurchaseOrderViews::where('po_number', 'like', '%' . $search . '%')
				->orWhere('supplier_name', 'like', '%' . $search . '%')
				// ->orWhere('apr_no', 'like', '%' . $search . '%')
				->orWhere('po_cat_name', 'like', '%' . $search . '%')
				->orWhere('items', 'like', '%' . $search . '%')
				->get();
		}else {
			$arr = DB::table('purchase_order_views')->skip(0)->take(15)->orderBy('id','desc')->get();
		}
		
		return response()->json($arr);
	}
    public function insert(Request $request){
    	$request->validate([
		    'supplier_id' => 'required|Numeric',
		    'po_number' => 'required|Numeric',
		    'date' => 'required|date',
		    'po_category' => 'required|Numeric'
		]);
		// Log::info('request all >>', array('request' => $request->all()));
    	$po = PurchaseOrder::create($request->all());
    	$po->created_by = Auth::user()->id;
    	$po->save();
    	return response()->json($po);
    }

}
