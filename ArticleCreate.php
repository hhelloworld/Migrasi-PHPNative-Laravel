<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Articles;
use App\Models\Tags;
use App\Models\ArticleTags;
use Illuminate\Support\Str;
use Carbon\Carbon; 


class ArticleCreate extends Controller
{
    public function create(Request $request)
    {
        try {
            // Validation rules can be added here

            $authorId = $request->input('author_id');
            $title = $request->input('title');
            $content = $request->input('content');
            $glance = $request->input('glance');
            $thumbnailImage = $request->file('thumbnail_image');
            $selectedTagIds = json_decode($request->input('tags'), true);

            
            

            // Check for empty fields
            if (empty($authorId)) {
                throw new \Exception('Author ID is required.');
            }

            if (empty($title)) {
                throw new \Exception('Title is required.');
            }

            if (empty($content)) {
                throw new \Exception('Content is required.');
            }

            if (empty($glance)) {
                throw new \Exception('Glance is required.');
            }

            if (!$thumbnailImage) {
                throw new \Exception('Thumbnail image is required.');
            }

            if (!is_array($selectedTagIds)) {
                // If $selectedTagIds is not an array, you can handle it as needed.
                throw new \Exception('$selectedTagIds must be an array.');
            }
            

            // Handle image upload to public_html/img directory
            $thumbnailPath = $thumbnailImage->storeAs($thumbnailImage->getClientOriginalName());

            // Create the article
            $article = Articles::create([
                'author_id' => $authorId,
                'title' => $title,
                'content' => $content,
                'thumbnail_image' => $thumbnailPath,
                'glance' => $glance,
                'slug' => Str::slug($title), // Laravel helper function
                
            ]);
            
            $article->created_at = Carbon::now();
            $article->updated_at = Carbon::now();
            $article->save();

            // Sync tags with the article
            if (is_array($selectedTagIds)) {
                foreach ($selectedTagIds as $tagId) {
                    ArticleTags::create([
                        'article_id' => $article->article_id,
                        'tag_id' => $tagId,
                    ]);
                }
            } else {
                // Handle the case where $selectedTagIds is not an array
                throw new \Exception('$selectedTagIds must be an array.');
            }
            
            
            
            $article->tags()->sync($selectedTagIds);

            // You can customize the response based on success or failure
            return response()->json(['status' => 'success', 'message' => 'Article created successfully', 'article' => $article], 200);
            
        } catch (\Exception $e) {
            \Log::error($e);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
