<?php


// app/Http/Controllers/ArticleController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Articles;
use App\Models\Tags;


class ArticlePost extends Controller
{
    public function getAllArticles()
    {
        $articles = Articles::with(['author', 'tags'])
            ->orderByDesc('created_at')
            ->get();

        foreach ($articles as $article) {
            $article->link = url("/article/$article->slug");
            $article->tag_names = $article->tags->pluck('tag_name')->toArray();
        }

        return response()->json($articles);
    }

    public function getArticleBySlug($slug)
    {
        $article = Articles::with(['author', 'tags'])
            ->where('slug', $slug)
            ->first();

        if (!$article) {
            abort(404);
        } else {
            $article->link = url("/article/$article->slug");
            $article->tag_names = $article->tags->pluck('tag_name')->toArray();

            return response()->json($article);
        }
    }
}


