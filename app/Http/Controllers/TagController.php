<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tag;

class TagController extends Controller{
    
    public function store(Request $request) {
        $request->validate(['name' => 'required|string']);
        
        $tag = Tag::create($request->all());
        return response()->json($tag, 201);
    }
    
    public function index() {
        return response()->json(Tag::all());
    }
    
    public function show($id) {
        return response()->json(Tag::findOrFail($id));
    }
    
    public function update($id, Request $request) {

        $tag = Tag::findOrFail($id);
        $tag->update($request->all());
        return response()->json($tag);
        
    }
    
    public function destroy($id) {
        $tag = Tag::findOrFail($id);
        $tag->delete();
        return response()->json(['message' => 'Tag deleted successfully']);
    }

}