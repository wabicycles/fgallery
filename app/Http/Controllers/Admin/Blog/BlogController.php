<?php
namespace App\Http\Controllers\Admin\Blog;

use App\Artvenue\Models\Blog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

/**
 * @author Abhimanyu Sharma <abhimanyusharma003@gmail.com>
 */
class BlogController extends Controller
{
    /**
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function index()
    {
        $title = sprintf('List Of Blogs');

        return view('admin.blog.index', compact('title'));
    }

    /**
     * @param $id
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function edit($id)
    {
        $blog = Blog::whereId($id)->firstOrFail();
        $title = sprintf('Editing blog "%s"', $blog->title);

        return view('admin.blog.edit', compact('blog', 'title'));
    }

    /**
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function create()
    {
        $title = 'Creating new blog';

        return view('admin.blog.create', compact('title'));
    }

    /**
     * @param Request $request
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function store(Request $request)
    {
        $this->validate($request, ['title' => ['required']]);

        $blog = new Blog();
        $blog->title = $request->get('title');
        $blog->description = $request->get('description');
        $blog->user_id = auth()->user()->id;
        $blog->slug = get_slug($request->get('title'));
        $blog->save();

        return redirect()->route('admin.blogs.index')->with('flashSuccess', 'Blog is now crated');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $blog = Blog::whereId($id)->firstOrFail();

        if ($request->get('delete')) {
            $blog->delete();

            return redirect()->route('admin.blogs.index')->with('flashSuccess', 'Blog is now deleted');
        }

        $blog->title = $request->get('title');
        $blog->description = $request->get('description');
        $blog->slug = get_slug($request->get('title'));
        $blog->save();

        return redirect()->back()->with('flashSuccess', 'Blog is now updated');
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        $blogs = Blog::all();
        $datatables = Datatables::of($blogs);

        $datatables->addColumn('actions', function ($blog) {
            return '<a href="' . route('admin.blogs.edit', [$blog->id]) . '" class="btn btn-info" target="_blank"><i class="fa fa-edit"></i></a>
                <a href="' . route('blog', [$blog->id, $blog->slug]) . '" class="btn btn-success" target="_blank"><i class="fa fa-search"></i></a>';
        });

        return $datatables->make(true);
    }
}
