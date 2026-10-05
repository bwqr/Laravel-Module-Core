<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Category;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');

        $this->middleware('permission:ownership.category')->except([
            'getCategories'
        ]);
    }

    public function getCategories()
    {
        return Category::with('translations')->get();
    }

    public function postCategory()
    {
        request()->validate([
            'name' => 'required',
            'description' => 'required',
            'slug' => 'required',
            'translations' => 'nullable|array',
            'translations.*.locale' => 'required_with:translations|string|max:10',
            'translations.*.name' => 'required_with:translations|string|max:191',
            'translations.*.slug' => 'required_with:translations|string|max:191',
            'translations.*.published' => 'boolean'
        ]);

        $category = Category::create(request()->only(['name', 'description', 'slug']));
        $this->storeTranslations($category);

        return response()->json();
    }

    public function putCategory($category_id)
    {
        request()->validate([
            'name' => 'required',
            'description' => 'required',
            'slug' => 'required',
            'translations' => 'nullable|array',
            'translations.*.locale' => 'required_with:translations|string|max:10',
            'translations.*.name' => 'required_with:translations|string|max:191',
            'translations.*.slug' => 'required_with:translations|string|max:191',
            'translations.*.published' => 'boolean'
        ]);

        $category = Category::findOrFail($category_id);
        $category->update(request()->only(['name', 'description', 'slug']));
        $this->storeTranslations($category);

        return response()->json();
    }

    public function deleteCategory($id)
    {
        Category::findOrFail($id)->forceDelete();

        return response()->json();
    }

    private function storeTranslations(Category $category)
    {
        foreach (request()->input('translations', []) as $translation) {
            $category->translations()->updateOrCreate(
                ['locale' => $translation['locale']],
                array_only($translation, ['name', 'slug', 'description', 'published'])
            );
        }
    }

}
