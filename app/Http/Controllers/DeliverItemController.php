<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DeliverItem;
use App\DeliverItemViews;
use App\DeliverFormViews;
use App\PurchaseOrderViews;
use App\AprFormViews;
use App\Item;
use Log;
use App\Events\ItemWasDelivered;
use DB;
class DeliverItemController extends Controller
{
    /*
        *** deliver_form ***
        parameters: array:10 [
          "charge_invoice_no" => "4443234"
          "date_of_invoice" => "2019-04-01"
          "date_of_delivery" => "2019-04-01"
          "date_received" => "2019-04-01"
          "iar_no" => "554345"
          "purpose" => "Secret"
          "type" => "po"
          "updated_at" => "2019-04-01 08:31:30"
          "created_at" => "2019-04-01 08:31:30"
          "id" => 1
        ]
        
        *** items ***
            [id] => 7
            [po_id] => 2
            [item_id] => 6
            [qty] => 18
            [unit_cost] => 95.75
            [created_at] => 2019-03-04 09:25:21
            [updated_at] => 2019-03-04 09:25:21
            [name] => Double A Batterys
            [unit_name] => Piece
            [received] => 6
    */  
    public function resetItemStocks(Request $request){
        DB::connection('dilgserver')->table('deliver_items')->get();
    }
    public function fetchBalancesApr(Request $request){
        $apr_no = $request->apr_no;
        $d_items_view = DeliverItemViews::where('apr_no', $apr_no)->get();
        return response()->json($d_items_view);
    }
    public function saveAprDeliverItem(Request $request){
        $items = $request->items;
        // dd($request);
        foreach ($items as $item) {
            $stockCard = Item::findOrFail($item['item_id']);
            $deliverItem = new DeliverItem;
            $deliverItem->purchase_item_id = $item['id'];
            $deliverItem->item_id = $item['item_id'];
            $deliverItem->deliver_form_id = $request->id;
            $deliverItem->apr_id = $request->apr_id;
            $deliverItem->delivered_qty = $item['received'];
            $deliverItem->unit_cost = $item['unit_cost'];
            $deliverItem->type = 'apr';
            $deliverItem->qty_before_deliver = $stockCard->running_balance;
            $deliverItem->save();
            event(new ItemWasDelivered($deliverItem));
        }
        $apr_view = AprFormViews::where('apr_no',$request->apr_no)->first();
        return response()->json(array('apr_view' => $apr_view, 'apr_no' => $request->apr_no));
    }
    public function savePoDeliverItem(Request $request){
        // dd($request);
        $items = $request->incomplete_items;
        $po_id = $request->deliver_form['po_id'];
        foreach ($items as $item) {
            $stockCard = Item::findOrFail($item['item_id']);
            $deliverItem = new DeliverItem;
            $deliverItem->purchase_item_id = $item['id'];
            $deliverItem->item_id = $item['item_id'];
            $deliverItem->deliver_form_id = $request->deliver_form['id'];
            $deliverItem->po_id = $item['po_id'];
            $deliverItem->delivered_qty = $item['temp_received'];
            $deliverItem->unit_cost = $item['unit_cost'];
            $deliverItem->type = $request->type;
            $deliverItem->qty_before_deliver = $stockCard->running_balance;
            $deliverItem->save();
            event(new ItemWasDelivered($deliverItem));
        }
        $po_view = PurchaseOrderViews::findOrFail($po_id);
        return response()->json(array('po_view' => $po_view, 'po_id' => $po_id));
    }
    public function fetchbalances(Request $request){
        $ids = $request->ids;
        // Log::info('ids', array('ids' => $ids));
        $items = DeliverItemViews::whereIn('purchase_item_id', $ids)->get();
        return response()->json($items);
    }
    public function fetchItemsById(Request $request){
        $form_id = $request->form_id;
        $items = DeliverItemViews::where('deliver_form_id', $form_id)->get();
        return response()->json($items);
    }
    public function saveitems(Request $request){
    	$items = $request->items;
    	$is_dbm = $request->is_dbm;
        $deliver_form_id = $request->deliver_form_id;
    	foreach ($items as $item) {
            $stockCard = Item::findOrFail($item['item_id']);
    		$deliverItem = new DeliverItem;
            $deliverItem->deliver_form_id = $deliver_form_id;
    		$deliverItem->po_id = $item['po_id'];
            $deliverItem->purchase_item_id = $item['id'];
    		$deliverItem->item_id = $item['item_id'];
    		$deliverItem->delivered_qty = $item['received'];
    		$deliverItem->unit_cost = $item['unit_cost'];
    		$deliverItem->supplier_is_dbm = $is_dbm;
            $deliverItem->qty_before_deliver = $stockCard->running_balance;
    		$deliverItem->save();
            $this->addRunningBalItem($item['received'], $item['item_id']);
    	}
    }
    private function addRunningBalItem($addQty, $item_id){
        $ldate = date('m-d-Y H:i:s');
        Item::where('id', $item_id)->increment('running_balance', $addQty);
        Item::where('id', $item_id)->update(['running_bal_remarks' => 'As of ' . $ldate]);
    }
}
