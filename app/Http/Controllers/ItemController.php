<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use Auth;
use DB;
use App\ItemsView;
use App\Events\ItemCreated;
use App\InitialBalance;
use App\PurchaseItem;
use App\DeliverItem;
use App\PurchaseOrderViews;
use Log;
use App\RequestItemViews;
use App\AprItemViews;
use App\PurchaseItemViews;
use App\DeliverItemViews;
use App\AprItem;
class ItemController extends Controller
{
    private function getLastUnitCost($item_id){
        

        $countPO = PurchaseItem::where('item_id', $item_id)->orderBy('id', 'desc')->count();
        $countAPR = AprItem::where('item_id', $item_id)->orderBy('id', 'desc')->count();

        if ($countPO > 0 || $countAPR > 0) {
            $poItem = PurchaseItem::where('item_id', $item_id)->orderBy('id', 'desc')->first();
            $aprItem = AprItem::where('item_id', $item_id)->orderBy('id', 'desc')->first();

            if ($countPO > 0 && $countAPR == 0) {
                # has P.O but no APR
                if ($countPO > 1) {
                    Log::info('po_items', 
                        array(
                            'po_items' => PurchaseItem::where('item_id', $item_id)->orderBy('id', 'desc')->get()
                        )
                    );
                }
                return $poItem->unit_cost;
            }elseif ($countAPR > 0 && $countPO == 0) {
                # has APR but no P.O
                if ($countAPR > 1) {
                    Log::info('apr_items', 
                        array(
                            'apr_items' => AprItem::where('item_id', $item_id)->orderBy('id', 'desc')->get()
                        )
                    );
                }
                return $aprItem->unit_cost;
            }elseif ($countPO > 0 && $countAPR > 0) {
                # has APR and P.O, with APR and P.O
                $collection = collect();
                $arr = array();
                $poItems = PurchaseItem::where('item_id', $item_id)->get();
                $aprItems = AprItem::where('item_id', $item_id)->get();
                foreach ($poItems as $model) {
                    array_push($arr, $model);
                }
                foreach ($aprItems as $model) {
                    array_push($arr, $model);
                }
                $collection = collect($arr);
                $sorted = $collection->sortByDesc('created_at')->first();
                return $sorted->unit_cost;
            }else {
                return 2;
            }
        }else {
            // Log::info('nothing was found for this item: ' .$item_id);
            return "N/A";
        }        
    }

    public function fetchAllForce(){
        $arr = ItemsView::orderBy('rca_cat','asc')
                ->orderBy('rca_no','asc')
                ->get();
        return response()->json($arr);
    }

    public function getStockBalAllItems(){
        # inventory Report
        $stockCards = array();
        $ids = DB::table('items')->select('id')->pluck('id')->all();

        // Log::info('the date', array('date', $request->date));
        // print_r($ids);
        // foreach ($ids as $id) {
        //     Log::info('id', array('id' => $id));
        // }

        foreach ($ids as $item_id) {
            $requestItemViews = DB::table('request_item_stock_card_report_views')
                ->where('item_id', $item_id)
                ->get();

                
            $deliverItemViews = DB::table('deliver_item_stock_card_report_views')
                ->where('item_id', $item_id)
                ->get();

            Log::info('requestItemViews', array(
                'item_id' => $item_id,
                'count' => $deliverItemViews->count(),
                'date_of_po' => $deliverItemViews->pluck('date_of_po')->all(),
                'date_of_apr' => $deliverItemViews->pluck('date_of_apr')->all()
            ));

            $stockCard = array();
            foreach ($requestItemViews as $r_item) {
                $r_item->type = 'ris';
                $r_item->delivery_type = 'ris';
                array_push($stockCard, $r_item);
            }

            foreach ($deliverItemViews as $d_item) {
                if ($d_item->delivery_type == 'po') {
                    $d_item->timestamp = $d_item->date_of_po;
                }else {
                    $d_item->timestamp = $d_item->date_of_apr;
                }
                array_push($stockCard, $d_item);
            }

            $collection = collect($stockCard);
            $sorted = $collection->sortBy('timestamp');
            $countInitBal = InitialBalance::where('item_id', $item_id)->count();
            $init_bal = 0;
            if ($countInitBal) {
                $initBalModel = InitialBalance::where('item_id', $item_id)->first();
                $init_bal = $initBalModel->balance;
            }
            
            $finalSortedStockCards = $sorted->values()->all();
            $stock_balance = $this->countTotalBal($finalSortedStockCards, $init_bal, $item_id);

            array_push($stockCards, [
                'item_id' => $item_id,
                'actual_stock' => $stock_balance
            ]);
            
        }
        return response()->json($stockCards);
    }


    public function getPhysicalBalanceReport(Request $request){
        $stockCards = array();
        $ids = DB::table('items')->select('id')->pluck('id')->all();

        foreach ($ids as $item_id) {
            
            $requestItemViews = DB::table('request_item_stock_card_report_views')
                ->where('item_id', $item_id)
                ->whereDate('timestamp', '<=', $request->date)
                ->get();
                
            $deliverItemPos = DB::table('deliver_item_stock_card_report_views')
                ->where('item_id', $item_id)
                ->where('delivery_type', 'po')
                ->whereDate('date_of_po', '<=', $request->date)
                ->get();

            $deliverItemAprs = DB::table('deliver_item_stock_card_report_views')
                ->where('item_id', $item_id)
                ->where('delivery_type', 'apr')
                ->whereDate('date_of_apr', '<=', $request->date)
                ->get();

            // Log::info('requestItemViews', array(
            //     'item_id' => $item_id,
            //     'count' => $deliverItemViews->count(),
            //     'date_of_po' => $deliverItemViews->pluck('date_of_po')->all(),
            //     'date_of_apr' => $deliverItemViews->pluck('date_of_apr')->all()
            // ));

            $stockCard = array();
            foreach ($requestItemViews as $r_item) {
                $r_item->type = 'ris';
                $r_item->delivery_type = 'ris';
                array_push($stockCard, $r_item);
            }

            foreach ($deliverItemPos as $d_item) {
                if ($d_item->delivery_type == 'po') {
                    $d_item->timestamp = $d_item->date_of_po;
                    array_push($stockCard, $d_item);
                }
            }

            foreach ($deliverItemAprs as $d_item) {
                if ($d_item->delivery_type == 'apr') {
                    $d_item->timestamp = $d_item->date_of_apr;
                    array_push($stockCard, $d_item);
                }
            }

            $collection = collect($stockCard);
            $sorted = $collection->sortBy('timestamp');
            $countInitBal = InitialBalance::where('item_id', $item_id)->count();
            $init_bal = 0;
            if ($countInitBal) {
                $initBalModel = InitialBalance::where('item_id', $item_id)->first();
                $init_bal = $initBalModel->balance;
            }
            
            $finalSortedStockCards = $sorted->values()->all();
            $stock_balance = $this->countTotalBal($finalSortedStockCards, $init_bal, $item_id);
            $latestUnitCost = $this->getLastUnitCost($item_id);
            array_push($stockCards, [
                'item_id' => $item_id,
                'actual_stock' => $stock_balance,
                'unit_cost' => $latestUnitCost
            ]);
            
        }
        return response()->json($stockCards);
    }







    public function getActualItemsBalances(Request $request){
        // $local_items = $request->local_items;
        $items = array();

        $stockCards = array();
        foreach ($request->ids as $item_id) {
            // Log::info('what is this', array('item_id' => $item_id));
            $requestItemViews = DB::table('request_item_stock_card_report_views')->where('item_id', $item_id)->get();
            $deliverItemViews = DB::table('deliver_item_stock_card_report_views')->where('item_id', $item_id)->get();

            # apr - date of APR
            # po  - date of P.O

            $stockCard = array();
            foreach ($requestItemViews as $r_item) {
                $r_item->type = 'ris';
                $r_item->delivery_type = 'ris';
                array_push($stockCard, $r_item);
            }

            foreach ($deliverItemViews as $d_item) {
                if ($d_item->delivery_type == 'po') {
                    $d_item->timestamp = $d_item->date_of_po;
                }else {
                    $d_item->timestamp = $d_item->date_of_apr;
                }
                array_push($stockCard, $d_item);
            }

            $collection = collect($stockCard);
            $sorted = $collection->sortBy('timestamp');
            $countInitBal = InitialBalance::where('item_id', $item_id)->count();
            $init_bal = 0;
            if ($countInitBal) {
                # has balance (Initial Bal)
                $initBalModel = InitialBalance::where('item_id', $item_id)->first();
                $init_bal = $initBalModel->balance;
            }
            
            $finalSortedStockCards = $sorted->values()->all();
            $stock_balance = $this->countTotalBal($finalSortedStockCards, $init_bal, $item_id);

            // $stockCards["$item_id"] = $finalSortedStockCards;
            // $
            // $itemModel = Item::findOrFail($item_id);
            array_push($stockCards, [
                'item_id' => $item_id,
                'actual_stock' => $stock_balance
            ]);
            
        }
        return response()->json($stockCards);
    }

    private function countTotalBal($finalSortedStockCards, $init_bal, $item_id){
        $stock_balance = 0;
        $stock_balance = $init_bal;
        foreach ($finalSortedStockCards as $model) {
            // Log::info('type of delivery', array('delivery_type' => $model->delivery_type));
            // echo $model->delivery_type;
            if ($model->delivery_type == 'ris') {
                # deduction for R.I.S
                $stock_balance -= $model->requested_qty;
            }else {
                if ($model->delivery_type == 'apr' || $model->delivery_type == 'po') {
                    # additional for P.O and A.P.R
                    $stock_balance += $model->delivered_qty;
                }
            }
        }
        return $stock_balance;
    }

    public function savePoItemAndDrItem(Request $request){
       
     
        // dd($request);   

        $purchase_item = new PurchaseItem;
        $purchase_item->item_id = $request->item['id'];
        $purchase_item->po_id = $request->current_po['id'];
        $purchase_item->qty = $request->payload['qty'];
        $purchase_item->unit_cost = $request->payload['unit_cost'];
        $purchase_item->save();
        
        $deliver_item = new DeliverItem;
        $deliver_item->item_id = $request->item['id'];
        $deliver_item->deliver_form_id = $request->payload['dr_id'];
        $deliver_item->purchase_item_id = $purchase_item->id;
        $deliver_item->po_id = $request->current_po['id'];
        $deliver_item->delivered_qty = $request->payload['qty'];
        $deliver_item->unit_cost = $request->payload['unit_cost'];
        $deliver_item->type = 'po';
        $deliver_item->qty_before_deliver = 0;
        $deliver_item->save();
        return response()->json(array(
            'purchase_item'     => $purchase_item,
            'deliver_item'      => $deliver_item,
            'item'              => ItemsView::findOrFail($request->item['id']),
            'purchase_order' => PurchaseOrderViews::findOrFail($request->current_po['id'])
        ));
    }
    public function fetchInventoryBalance(){
        $count = ItemsView::count();
        $items = ItemsView::orderBy('rca_cat','asc')->get();
        return response()->json([
            'count' => $count,
            'items' => $items
        ]);
    }
    public function updateExistingItem(Request $request){
        $id = $request->id;
        $item = Item::findOrFail($id);
        $item->name = $request->name;
        $item->rca_code = $request->rca_code;
        $item->rca_cat = $request->rca_cat;
        $item->rca_no = $request->rca_no;
        $item->cat_id = $request->cat_id;
        $item->unit_id = $request->unit_id;
        $item->reorder_point = $request->reorder_point;
        $item->description = $request->description;
        $item->running_balance = $request->running_balance;
        $item->running_bal_remarks = $request->running_bal_remarks;
        $rsUpdated = $item->save();
        return response()->json(array('updated' => $rsUpdated, 'item' => $item));
        
    }
    public function fetchItemInitBal(Request $request){
        $item_id = $request->item_id;
        $init_bal = InitialBalance::where('item_id', $item_id)->first();
        return response()->json($init_bal);
    }
    public function stockCardReportById(Request $request){
        $item_id = $request->item_id;
        $deliver_arr = DB::table('deliver_item_stock_card_report_views')
                ->where('item_id', $item_id)->orderBy('id','desc')
                ->groupBy('delivered_qty')
                ->groupBy('po_year')
                ->groupBy('po_month')
                ->groupBy('po_number')
                ->get();
        $ris_arr = DB::table('request_item_stock_card_report_views')
                ->where('item_id', $item_id)->orderBy('id','desc')
                ->get();
        return response()->json([
            'deliver_arr' => $deliver_arr,
            'ris_arr' => $ris_arr
        ]);
    }
    public function search(Request $request){
        $keyword = $request->search;
        if (!$keyword) {
            $arr = $this->fetchLimit();
        }else {
            $arr = ItemsView::where('name', 'like', '%' . $keyword . '%')
                ->orWhere('description', 'like', '%' . $keyword . '%')
                ->orWhere('reorder_point', 'like', '%' . $keyword . '%')
                ->orWhere('running_balance', 'like', '%' . $keyword . '%')
                ->orWhere('running_bal_remarks', 'like', '%' . $keyword . '%')
                ->orWhere('cat_name', 'like', '%' . $keyword . '%')
                ->orWhere('unit_name', 'like', '%' . $keyword . '%')
                ->orderBy('id','desc')
                ->get();
        }
        return response()->json($arr);
    }
    public function countAllItems(){
        return response()->json([
            'count' => DB::table('items')->count()
        ]);
    }
    public function insert(Request $request){
        $request->validate([
            'name' => 'required|unique:items|max:255',
            'description' => 'required|max:255',
            'reorder_point' => 'required',
            'unit_id' => 'required',
            'cat_id' => 'required',
            'rca_code' => 'required|Numeric',
            'rca_cat' => 'required|Numeric',
            'rca_no' => 'required|Numeric'
        ]);

    	$item = new Item;
    	$item->name = $request->name;
    	$item->description = $request->description;
    	$item->cat_id = $request->cat_id;
    	$item->unit_id = $request->unit_id;
    	$item->reorder_point = $request->reorder_point;
    	$item->created_by = Auth::user()->id;

        #additional props
        $item->running_balance = $request->running_balance;
        $item->rca_code = $request->rca_code;
        $item->rca_cat = $request->rca_cat;
        $item->rca_no = $request->rca_no;
        $item->running_bal_remarks = $request->running_bal_remarks;
    	$rsSaved = $item->save();
        if ($rsSaved) {
            event(new ItemCreated($item));
        }
    	return response()->json($item);
    }

    public function fetchLimit(){
        return DB::table('items_view')->orderBy('id','desc')->get();
        // return DB::table('items_view')->skip(0)->take(15)->orderBy('id','desc')->get();
    }
}
