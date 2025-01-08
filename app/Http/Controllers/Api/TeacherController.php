<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teachers;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function get_all(){
        return Teachers::all();
    }

    public function search_teachers(Request $request) {
        return Teachers::where('name', 'like', '%'.$request->name.'%')->orWhere('profession', 'like', '%'.$request->name.'%')->get();
    }
}
