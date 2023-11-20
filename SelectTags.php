<?php


// app/Http/Controllers/TagController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tags;

class SelectTags extends Controller
{
    public function getAllTags()
    {
        $tags = Tags::all();

        return response()->json($tags);
    }
}
