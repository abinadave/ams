<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AprItem;
use Log;
use App\Events\AprItemCreated;
use App\AprFormViews;
use App\AprItemViews;
class AprItemController extends Controller
{
    public function fetchByAprId(Request $request){
        // dd($request);
        $current_apr_form = $request->current_apr_form;
        $apr_items = AprItemViews::where('apr_id', $current_apr_form['id'])->get();
        return response()->json($apr_items);
        /**
            "current_apr_form" => [
                "id" => 6
                "apr_no" => "APR-3332112"
                "apr_date" => "2019-05-09"
                "encoder" => 1
                "created_at" => "2019-05-28 09:36:41"
                "updated_at" => "2019-05-28 09:36:41"
                "name" => "Elvira Cahayag"
                "count_items" => 2
                "count_deliveries" => 1
                "total_amount" => 317
                "items" => "Ink Cartridge-BT6000, Envelope - Pay"
                "delivery_completed" => 0
              ]
        **/
    }
    public function delete(Request $request){
        $id = $request->id;
        $count = AprItem::where('id', $id)->count();
        if ($count) {
            $apr_item = AprItem::findOrFail($id);
            $resp_del = AprItem::destroy($id);
            $apr_form_view = AprFormViews::findOrFail($apr_item->apr_id);
            return response()->json(['deleted' => $resp_del, 'apr_form_view' => $apr_form_view]);
        }else {
            return response()->json(['deleted' => 0]);
        }
    }
    public function update(Request $request){
        $item = $request->item;
        $aprItem = AprItem::findOrFail($item['primary_id']);
        $rsUpdated = false;
        if ($request->prop == 'qty') {
            $aprItem->qty = $item['qty'];
            $rsUpdated = $aprItem->save();
        }else {
            $aprItem->unit_cost = $item['unit_cost'];
            $rsUpdated = $aprItem->save();
        }
        // dd($request);
        $aprItemView = AprItemViews::findOrFail($item['id']);
        $aprFormView = AprFormViews::findOrFail($aprItem->apr_id);
        return response()->json(
            array('prop' => $request->prop, 
                'apr_item_view' => $aprItemView, 
                'rs_updated' => $rsUpdated,
                'apr_form_view' => $aprFormView
            )
        );
    }
    public function fetchItemsByAprId(Request $request){
        $apr_id = $request->apr_id;
        $items = AprItemViews::where('apr_id', $apr_id)->get();
        return response()->json($items);
    }
    public function saveItems(Request $request){
    	$apr_id = $request->apr_id;
    	$items = $request->items;
    	$savedItems = array();
    	foreach ($items as $item) {
    		$apr_item = new AprItem;
    		$apr_item->item_id = $item['id'];
    		$apr_item->qty = $item['qty'];
    		$apr_item->unit_cost = $item['unit_cost'];
    		$apr_item->apr_id = $apr_id;
    		$apr_item->save();
    		array_push($savedItems, $apr_item);
    	}
        event(new AprItemCreated($items, $apr_id));
        $apr_form = AprFormViews::findOrFail($apr_id);
    	return response()->json(array('saved_items' => $savedItems, 'apr_id' => $apr_id, 'apr_form' => $apr_form));
    }

}
