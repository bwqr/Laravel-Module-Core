<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Author;
use Illuminate\Support\Facades\DB;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'permission:ownership.article']);
    }

    public function index()
    {
        return Author::with(['translations', 'user' => function ($query) {
            $query->select('user_id', 'name');
        }])->orderBy('created_at', 'desc')->get();
    }

    public function show($id)
    {
        return Author::with('translations')->findOrFail($id);
    }

    public function store()
    {
        $this->validateRequest();

        $author = DB::transaction(function () {
            $author = Author::create(request()->only([
                'user_id', 'erp_person_uuid', 'profile_image', 'linkedin_url', 'social_links', 'active',
            ]));
            $this->storeTranslations($author);

            return $author;
        });

        return response()->json($author->load('translations'), 201);
    }

    public function update($id)
    {
        $this->validateRequest($id);
        $author = Author::findOrFail($id);

        DB::transaction(function () use ($author) {
            $author->update(request()->only([
                'user_id', 'erp_person_uuid', 'profile_image', 'linkedin_url', 'social_links', 'active',
            ]));
            $this->storeTranslations($author);
        });

        return response()->json($author->fresh('translations'));
    }

    public function destroy($id)
    {
        Author::findOrFail($id)->delete();

        return response()->json();
    }

    private function validateRequest($id = null)
    {
        request()->validate([
            'active' => 'required|boolean',
            'translations' => 'required|array|min:1',
            'translations.*.locale' => 'required|string|max:10',
            'translations.*.slug' => 'required|string|max:191',
            'translations.*.name' => 'required|string|max:191',
            'translations.*.professional_title' => 'nullable|string|max:255',
            'translations.*.biography' => 'nullable|string',
            'translations.*.expertise' => 'nullable|array',
        ]);
    }

    private function storeTranslations(Author $author)
    {
        foreach (request()->input('translations', []) as $translation) {
            $author->translations()->updateOrCreate(
                ['locale' => $translation['locale']],
                array_only($translation, [
                    'slug', 'name', 'professional_title', 'biography', 'expertise',
                ])
            );
        }
    }
}
