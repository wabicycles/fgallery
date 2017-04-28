<?php

/**
 * @author Abhimanyu Sharma <abhimanyusharma003@gmail.com>
 */

namespace App\Http\Controllers\Admin\User;

use App\Artvenue\Helpers\Resize;
use App\Artvenue\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function getIndex(Request $request)
    {
        $title = sprintf('List of %s users', ucfirst($request->get('type')));
        $type = $request->get('type');

        return view('admin.user.index', compact('title', 'type'));
    }

    /**
     * @return mixed
     */
    public function getAddUser()
    {
        $title = 'Add Real/Fake user';

        return view('admin.user.add', compact('title'));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getData(Request $request)
    {
        $users = User::select([
            'users.*',
            DB::raw('count(images.user_id) as images'),
            DB::raw('count(comments.user_id) as comments'),
        ])->leftJoin('images', 'images.user_id', '=', 'users.id')
            ->leftJoin('comments', 'comments.user_id', '=', 'users.id')
            ->groupBy('users.id');

        switch ($request->get('type')) {
            case 'approved':
                $users->whereNotNull('users.confirmed_at');
                break;
            case 'featured':
                $users->whereNotNull('users.featured_at');
                break;
            case 'approvalRequired':
                $users->whereNull('users.confirmed_at');
                break;
            case 'banned':
                $users->wherePermission('ban');
                break;
            default:
                $users->whereNotNull('users.confirmed_at');
        }

        $datatables = Datatables::of($users);

        if ($request->get('type') == 'approvalRequired') {
            $datatables->addColumn('actions', function ($image) {
                return '<a href="#" class="image-approve btn btn-sm btn-success" data-approve="' . $image->id . '"><i class="fa fa-check"></i> </a>
                 <a href="' . route('admin.users.edit', [$image->id]) . '" class="btn btn-sm btn-info" target="_blank"><i class="fa fa-edit"></i></a>
                <a href="#" class="image-disapprove btn btn-sm btn-danger" data-disapprove="' . $image->id . '"><i class="fa fa-times"></i></a>';
            });
        } else {
            $datatables->addColumn('actions', function ($user) {
                return '<a href="' . route('admin.users.edit', [$user->id]) . '" class="btn btn-sm btn-info" target="_blank"><i class="fa fa-edit"></i></a>
                <a href="' . route('user', [$user->username]) . '" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-search"></i></a>';
            });
        }

        return $datatables->addColumn('thumbnail', function ($image) {
            return '<img src="' . Resize::avatar($image, 'avatar') . '" style="width:80px"/>';
        })->editColumn('created_at', function ($user) {
            if ($user->created_at != null) {
                return $user->created_at->diffForHumans();
            }
        })
            ->editColumn('featured_at', function ($user) {
                if ($user->featured_at != null) {
                    return $user->featured_at->diffForHumans();
                }

                return 'Not Featured';
            })
            ->editColumn('updated_at', function ($user) {
                if ($user->updated_at != null) {
                    return $user->updated_at->diffForHumans();
                }
            })
            ->editColumn('fullname', '{!! str_limit($fullname, 60) !!}')
            ->make(true);
    }
}
