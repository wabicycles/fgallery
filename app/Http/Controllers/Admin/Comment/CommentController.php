<?php
/**
 * @author Abhimanyu Sharma <abhimanyusharma003@gmail.com>
 */
namespace App\Http\Controllers\Admin\Comment;

use App\Artvenue\Models\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class CommentController extends Controller
{
    /**
     * @param Request $request
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = sprintf('List of %s comments', ucfirst($request->get('type')));
        $type = $request->get('type');

        return view('admin.comment.index', compact('title', 'type'));
    }

    /**
     * @param $id
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function edit($id)
    {
        $title = 'Editing Comment';
        $comment = Comment::whereId($id)->with('image', 'user', 'replies', 'votes')->firstOrFail();

        return view('admin.comment.edit', compact('title', 'comment'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $comment = Comment::whereId($id)->with('image', 'user', 'replies', 'votes')->firstOrFail();
        if ($request->get('delete')) {
            $comment->delete();

            return redirect()->route('admin.comments.index')->with('flashSuccess', 'Comment is now deleted');
        }
        $comment->comment = $request->get('comment');
        $comment->save();

        return redirect()->back()->with('flashSuccess', 'Comment is now updated');
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        $comments = Comment::select([
            'comments.*',
            DB::raw('count(comments_votes.comment_id) as votes'),
            DB::raw('users.fullname as fullname'),
            DB::raw('users.username as username'),
            DB::raw('users.avatar as avatar'),
        ])->leftJoin('comments_votes', 'comments_votes.comment_id', '=', 'comments.id')
            ->leftJoin('users', 'comments.user_id', '=', 'users.id')
            ->groupBy('comments.id');

        $datatables = Datatables::of($comments);

        $datatables->addColumn('actions', function ($comment) {
            return '<a href="' . route('admin.comments.edit', [$comment->id]) . '" class="btn btn-info" target="_blank"><i class="fa fa-edit"></i></a>';
        });
        $datatables->editColumn('username', '{!! link_to_route("user", $username, [$username]) !!}');
        $datatables->editColumn('image_id', '{!! link_to_route("image", $image_id, [$image_id]) !!}');
        $datatables->editColumn('featured_at', function ($image) {
            if ($image->featured_at) {
                $image->featured_at->diffForHumans();
            }

            return 'Not Featured';
        });
        $datatables->editColumn('updated_at', function ($comment) {
            if ($comment->updated_at) {
                return $comment->created_at->diffForHumans();
            }
        });
        $datatables->editColumn('created_at', function ($comment) {
            if ($comment->created_at) {
                return $comment->created_at->diffForHumans();
            }
        });
        $datatables->editColumn('fullname', '{!! str_limit($fullname, 60) !!}');

        return $datatables->make(true);
    }
}
