<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

date_default_timezone_set('Asia/Manila');
Route::get('/', 'UserController@initload');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/register_manual', 'UserController@register')->name('manualregister');
Route::post('/login_manual', 'UserController@login')->name('loginmanual');

Route::get('/test_route', function(){
    echo "Good boy";
});

 Route::get('/hassword_hash', function(){
        echo password_hash("password", PASSWORD_DEFAULT);
        //username: tintin
        //password:     
});


Route::group(['middleware' => ['auth']], function () {
    Route::get('/current/user', 'UserController@currentUser');
    Route::post('/autocomplete/requisition_form', 'RequestFormController@autocompleteForm');
    Route::get('/fetch/head_names', 'HeadController@fetchAll');
    Route::post('/add_or_update/head_name', 'HeadController@insertOrAddHead');
    Route::post('/add_one_purchase_item', 'PurchaseItemController@saveItemToExistingPO');
    Route::get('/units', 'UnitController@fetch');
    Route::get('/categories', 'CategoryController@fetch');
    Route::post('/item', 'ItemController@insert');
    Route::get('/items', 'ItemController@fetchLimit');
    Route::get('/count_total_items', 'ItemController@countAllItems');
    Route::post('/search_items', 'ItemController@search');
    Route::get('/p_o_categories', 'POCategoryController@fetch');
    Route::post('/supplier', 'SupplierController@insert');
    Route::get('/supplier', 'SupplierController@fetch');
    Route::post('/purchase_order', 'PurchaseOrderController@insert');
    Route::post('/purchase_items', 'PurchaseItemController@insertLocalItems');
    Route::get('/purchase_order', 'PurchaseOrderController@fetchLimit');
    Route::post('/search_purcase_order', 'PurchaseOrderController@search');
    Route::post('/validate/po_number', 'PurchaseOrderController@validatePONumber');
    Route::post('/purchase_items_by_po_id', 'PurchaseItemController@fetchByPoId');
    Route::delete('/purchase_item', 'PurchaseItemController@deleteitem');
    Route::put('/purchase_item_qty', 'PurchaseItemController@updateItemQty');
    Route::post('/search_po_number', 'PurchaseOrderController@searchPoNumber');
    Route::post('/delivery_form', 'DeliverFormController@insert');
    Route::post('/unit', 'UnitController@insert');
    Route::get('/unit_views', 'UnitController@fetchunitviews');
    Route::post('/search_apr_no_for_delivery', 'PurchaseOrderController@searchAprNo');
    Route::post('/delete_unit', 'UnitController@deleteone');
    Route::put('/edit_unit', 'UnitController@editUnit');
    Route::post('/purchse_orders/delivery', 'PurchaseOrderController@filterPoBySupplierId');
    Route::post('/delivery_items', 'DeliverItemController@saveitems');
    Route::get('/deliver_form_views', 'DeliverFormController@fetchDeliverFormViews');
    Route::post('/deliver_items_by_form_id', 'DeliverItemController@fetchItemsById');
    Route::post('/get_delivery_balance', 'DeliverItemController@fetchbalances');
    Route::post('/fetch_po_deliveries', 'DeliverFormController@fetchDeliveryForms');
    Route::post('/advanced_search_delivery', 'DeliverFormController@advancedSearch');
    Route::post('/advanced_search_po', 'PurchaseOrderController@advancedSearch');
    Route::post('/requisition_form', 'RequestFormController@insert');
    Route::post('/request_items', 'RequestItemController@insertItems');
    Route::get('/request_forms', 'RequestFormController@fetchLimit');
    Route::post('/search_requisitions', 'RequestFormController@search');
    Route::post('/request_item_by_form_id', 'RequestItemController@fetchRequestItemsByRID');
    Route::post('/stock_card_report', 'ItemController@stockCardReportById');
    Route::post('/fetch_supplier_name', 'SupplierController@fetchSupName');
    Route::post('/init_balance_item_id', 'ItemController@fetchItemInitBal');
    Route::post('/purchase_item_views_by_po_id', 'PurchaseItemController@fetchItemByPoId');
    Route::put('/item', 'ItemController@updateExistingItem');
    Route::post('/validate_ris_form', 'RequestFormController@validateForm');
    Route::post('/rsmi', 'RequestItemController@filterRSMI');
    Route::get('/ris_last_insert_id', 'RequestFormController@getLastInsertId');
    Route::put('/request_item', 'RequestItemController@updateRitem');
    Route::put('/request_form', 'RequestFormController@updateForm');
    Route::put('/purchase_item_unit_cost', 'PurchaseItemController@updateUnitCost');
    Route::post('/apr_form', 'AprFormController@insert');
    Route::post('/apr_items', 'AprItemController@saveItems');
    Route::get('/apr_form_views', 'AprFormController@fetchFormsViews');
    Route::post('/search_apr_form_views', 'AprFormController@search');
    Route::get('/hash', function(){
        echo password_hash("user101", PASSWORD_DEFAULT);
    });
    Route::post('/fetch_apr_items_byid', 'AprItemController@fetchItemsByAprId');

    #This route is for Inventory Report (ALL ITEMS) without Dates
    Route::get('/fetch_all_inventory_balance', 'ItemController@getStockBalAllItems');

    Route::post('/save_po_delivery_form', 'DeliverFormController@savePoDeliveryForm');
    Route::post('/save_po_deliver_items', 'DeliverItemController@savePoDeliverItem');
    Route::post('/save_apr_deliver_items', 'DeliverItemController@saveAprDeliverItem');
    Route::post('/validate_apr_no_duplicate', 'AprFormController@checkAprNoDuplicate');
    Route::put('/apr_form', 'AprFormController@update'); #only one item updated
    Route::put('/apr_item', 'AprItemController@update'); #only one item updated
    Route::delete('/apr_item', 'AprItemController@delete');
    Route::post('/insert_df_apr', 'DeliverFormController@insertAprDelivery');
    Route::post('/apr_balances', 'DeliverItemController@fetchBalancesApr');
    Route::post('/fetch_deliveries_by_apr_no', 'DeliverFormController@fetchByAprId');
    Route::post('/save_po_item_dr_item', 'ItemController@savePoItemAndDrItem');
    Route::post('/db_set_po_delivery_completed', 'PurchaseOrderController@updatePoDeliveryCompleted');
    Route::delete('/request_item', 'RequestItemController@deleteOnItem');
    Route::post('/add_ris_item_to_existing', 'RequestItemController@addExistingItemToRequestForm');
    Route::post('/get_actual_items_balance', 'ItemController@getActualItemsBalances');
    Route::post('/search_ris_using_ris_no', 'RequestFormController@searchUsingRisNo');
    Route::post('/get_physical_balance_report', 'ItemController@getPhysicalBalanceReport');
    Route::get('/items_force_all', 'ItemController@fetchAllForce');

    # this is to reset the stock card balances 
    # when you try to truncate deliver forms and items
    Route::get('/reset_deliver_forms', 'DeliverItemController@resetItemStocks');

});
