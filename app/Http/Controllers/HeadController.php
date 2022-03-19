<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\HeadName;
class HeadController extends Controller
{
    public function fetchAll(Request $request){
        return response()->json(HeadName::orderBy('name','asc')->get());
    }
    public function insertOrAddHead(Request $request){
        $position = $request->position;
        $name = $request->name;
        $count = HeadName::where('position', $position)->count();
        $resp_head = null;
        $type = '';
        if (!$count) {
            # add...
            $head = new HeadName;
            $head->position = $request->position;
            $head->name = $request->name;
            $head->created_by = auth()->user()->id;
            $head->save();
            $resp_head = $head;
            $type = 'add';
        }else {
            # update ..
             HeadName::where('position', $position)
            ->update(['name' => $name]);
            $resp_head = HeadName::where('position', $position)->first();
            $type= 'update';
        }
        return response()->json([
            'count' => $count,
            'resp_head' => $resp_head,
            'type' => $type
        ]);
    }
}
