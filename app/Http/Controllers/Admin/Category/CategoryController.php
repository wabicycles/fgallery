<?php

/**
 * @author Abhimanyu Sharma <abhimanyusharma003@gmail.com>
 */

namespace App\Http\Controllers\Admin\Category;

use App\Artvenue\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CategoryController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        $title = 'Categories';

        return view('admin.settings.category', compact('title'));
    }

    /**
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
        ]);
        $category = new Category();
        $category->name = ucfirst($request->get('title'));
        $category->slug = get_slug($request->get('title'));
        $category->save();
        Artisan::call('cache:clear');

        return redirect()->back()->with('flashSuccess', 'New Category Is Added');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'id'   => ['required'],
            'slug' => ['required', 'alpha_dash'],
            'name' => ['required']
        ]);
        $category = Category::whereId($id)->with('images')->firstOrFail();

        if ($request->get('delete')) {
            if ($shiftCategoryId = $request->get('shiftCategory')) {
                $category->images()->update(['category_id' => $shiftCategoryId]);
            }
            $category->delete();

            return redirect()->back()->with('flashSuccess', 'Category is now deleted');
        }

        $category->slug = $request->get('slug');
        $category->name = $request->get('name');
        $category->save();
        Artisan::call('cache:clear');

        return redirect()->back()->with('flashSuccess', 'Category is now updated');
    }

    /**
     * @param Request $request
     */
    public function reorderCategory(Request $request)
    {
        $tree = $request->get('tree');
        foreach ($tree as $k => $v) {
            if ($v['depth'] == -1) {
                continue;
            }
            if ($v['parent_id'] == 'root') {
                $v['parent_id'] = 0;
            }

            $category = Category::whereId($v['item_id'])->first();
            $category->parent_id = $v['parent_id'];
            $category->depth = $v['depth'];
            $category->lft = $v['left'] - 1;
            $category->rgt = $v['right'] - 1;
            $category->save();
        }
        Artisan::call('cache:clear');
    }
}
