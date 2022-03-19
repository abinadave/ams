<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseItem;
use App\Item;
use App\PurchaseOrder;
use App\PurchaseItemsView;
use Log;
use DB;
use App\Events\PoItemWasCreatedInExistingPO;
class PurchaseItemController extends Controller
{
    public function saveItemToExistingPO(Request $request){
        $item = (object)$request->item;
        $current_po = (object)$request->current_po;
        $po_item = new PurchaseItem;
        $po_item->po_id = $current_po->id;
        $po_item->item_id = $item->id;
        $po_item->qty = 0;
        $po_item->unit_cost = 0;
        $po_item->save();
        event(new PoItemWasCreatedInExistingPO($po_item));
        return response()->json(PurchaseItemsView::where('id', $po_item->id)->first());
    }
    public function fetchItemByPoId(Request $request){
        $po_id = $request->po_id;
        $po_items = PurchaseItemsView::where('po_id', $po_id)->get();
        return response()->json($po_items);
    }
    public function updateUnitCost(Request $request){
        $id = $request->primary_id;
        $unit_cost = $request->unit_cost;
        $rsUpdated = PurchaseItem::where('id', $id)
                     ->update(['unit_cost' => $unit_cost]);

        $purchaseItem = PurchaseItem::findOrFail($id);
        $po_id = $purchaseItem->po_id;
        $items = PurchaseItem::where('po_id', $po_id)->get();
        $total_sum = $items->sum(function ($item) {
            return $item['qty'] * $item['unit_cost'];
        });
        return response()->json(['updated'=>$rsUpdated, 'total_sum' => $total_sum, 'purchase_item' => $purchaseItem]);
    }
    public function updateItemQty(Request $request){
        $id = $request->primary_id;
        $qty = $request->qty;
        $rsUpdated = PurchaseItem::where('id', $id)
                     ->update(['qty' => $qty]);

        $purchaseItem = PurchaseItem::findOrFail($id);
        $po_id = $purchaseItem->po_id;
        $items = PurchaseItem::where('po_id', $po_id)->get();
        $total_sum = $items->sum(function ($item) {
            return $item['qty'] * $item['unit_cost'];
        });
        return response()->json(['updated'=>$rsUpdated, 'total_sum' => $total_sum, 'purchase_item' => $purchaseItem]);
    }
    public function deleteitem(Request $request){
        $id = $request->id;
        $count = PurchaseItem::where('id', $id)->count();
        if ($count) {
            $resp_del = PurchaseItem::destroy($id);
            return response()->json(['deleted' => $resp_del, '']);
        }else {return response()->json(['deleted' => 0]);}
    }
    public function fetchByPoId(Request $request){
        $po_id = $request->po_id;
        $items = PurchaseItem::where('po_id', $po_id)->get();
        $pitem_ids = $items->pluck('id');
        $purchase_items = PurchaseItemsView::whereIn('id', $pitem_ids)
                    ->get();
        // Log::info('purchase_items', array('pitems' => $purchase_items));
        return response()->json($purchase_items);
    }
    public function insertLocalItems(Request $request){
    	// echo "saving local items";
    	$items = $request->items;
    	$purchase_order = $request->purchase_order;
    	/* items
			 [id] => 121
             [name] => item aaaa1
             [unit] => PC
             [qty] => 5
             [unit_cost] => 77
    	*/
        /* Purchase Order
    		  "supplier_id" => 4
    		  "po_number" => "34345"
    		  "date" => "2018-09-26"
    		  "po_category" => 1
    		  "apr_no" => "34565"
    		  "updated_at" => "2018-09-27 03:20:15"
    		  "created_at" => "2018-09-27 03:20:15"
    		  "id" => 3
    		  "created_by" => 1
    	*/
		$total = 0;
        $p_names = [];
    	foreach ($items as $item) {
    		$total = $item['qty'] * $item['unit_cost'];
    		# wont save the item if amount total is zero
    		if ($total > 0) {
    			$pitem = new PurchaseItem;

	    		$pitem->po_id = $purchase_order['id'];
	    		$pitem->qty = $item['qty'];
	    		$pitem->unit_cost = $item['unit_cost'];
	    		$pitem->item_id = $item['id'];
	    		$pitem->save();

                $item = Item::where('id', $item['id'])->first();
                array_push($p_names, $item->name);
    		}
    	}

        PurchaseOrder::where('id', $purchase_order['id'])
              ->update(['items' => join(",  ",$p_names)]);
    }
}
