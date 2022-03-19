<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
     protected $fillable = ['supplier_id','po_number','date','po_category','apr_no','po_year','po_month'];
}
