<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RequestItem;
use App\RequestForm;
use App\Events\EventRequestItemCreated;
use App\RequestItemViews;
use DB;
use App\Events\RisItemUpdatedorDeleted;
use Log;
class RequestItemController extends Controller
{
    public function addExistingItemToRequestForm(Request $request){
        $ris_item = new RequestItem;
        $ris_item->item_id = $request->item['id'];
        $ris_item->requested_qty = 0;
        $ris_item->remarks = '';
        $ris_item->request_form_id = $request->request_form['id'];
        $ris_item->qty_before_withdraw = 0;
        $ris_item->save();
        return response()->json($ris_item);
    }
    public function deleteOnItem(Request $request){
        $id = $request->id;
        $request_form_id = $request->request_form_id;
        $removed = RequestItem::destroy($id);
        // event(new RisItemUpdatedorDeleted($request_form_id));
        return response()->json(array('removed' => $removed));
    }
    public function updateRitem(Request $request){
        $new_qty = $request->new_qty;
        $item = $request->item;
        $rsUpdated = Requestitem::where('id', $item['id'])->update(['requested_qty' => $new_qty]);
        return response()->json(['updated' => $rsUpdated]);
    }
    public function filterRSMI(Request $request){
        $from = $request->d1;
        $to = $request->d2;
        $arr = DB::table('request_item_views')
                    ->whereBetween('date_requested', [$from, $to])
                    ->orderBy('ris_month', 'asc')
                    ->orderBy('ris_number', 'asc')
                    ->get();
        return response($arr);
    }
    public function fetchRequestItemsByRID(Request $request){
        $rid = $request->request_form_id;
        $r_items = RequestItemViews::where('request_form_id', $rid)->get();
        return response()->json($r_items);
    }
    public function insertItems(Request $request){
        // Log::info('REQUEST FORM UPDATING', array('ris_updating', $request->ris_updating));
    	$request_form = $request->request_form;
    	$items = $request->items;
    	$r_items = array();
        $names = array();
        if ($request->ris_updating) {
            # currently updating
            # get items
            
            $items = RequestItemViews::where('request_form_id', $request_form['id'])->get();
            Log::info('updated ris_items', array('ris_item_count' , $items->count()));
            foreach ($items as $item) {
                array_push($names, $item['name'] . '  (' . $item['requested_qty'] . ')');
            }
        }else {
            # not updating
            # use the given items
            foreach ($items as $item) {
                $r_item = new RequestItem;
                $r_item->request_form_id = $request_form['id'];
                $r_item->item_id = $item['id'];
                $r_item->requested_qty = $item['requested_qty'];
                $r_item->remarks = "";
                $r_item->remarks = $item['remarks'];
                $r_item->qty_before_withdraw = $item['running_balance'];
                $r_item->save();
                array_push($r_items, $r_item);
                array_push($names, $item['name'] . '  (' . $item['requested_qty'] . ')');
                event(new EventRequestItemCreated($r_item));
            }
        }
    	
        
        $rf_updated = RequestForm::where('id', $request_form['id'])
                        ->update(['item_list' => join(", ",$names)]);
    	return response()->json([
    		'done' => true,
    		'request_items' => $r_items,
    		'request_form' => $request_form,
            'names' => $names
    	]);
    }
}
