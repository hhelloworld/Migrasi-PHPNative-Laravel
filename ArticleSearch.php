<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleSearch extends Controller
{
    public function search(Request $request)
    {
        try {
            if ($request->has('query')) {
                $searchQuery = $request->input('query');

                $results = DB::table('Articles')
                    ->whereRaw('LOWER(title) LIKE ? OR LOWER(glance) LIKE ? OR LOWER(content) LIKE ?', [
                        '%' . strtolower($searchQuery) . '%',
                        '%' . strtolower($searchQuery) . '%',
                        '%' . strtolower($searchQuery) . '%',
                    ])
                    ->get();

                if ($results->isNotEmpty()) {
                    return response()->json($results);
                } else {
                    return response()->json(['message' => 'No results found.']);
                }
            } else {
                return response()->json(['message' => 'Query parameter not provided.']);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error executing the query: ' . $e->getMessage()]);
        }
    }
}
