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

    public function find_teacher($id) {
        try {
            $teacher = Teachers::findOrFail($id);
            
            // Use the formatted image from the accessor
            $teacher->image = $teacher->formatted_image;
            
            return response()->json($teacher);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Teacher not found'], 404);
        }
    }
}
