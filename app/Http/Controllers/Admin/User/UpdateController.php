<?php
/**
 * @author Abhimanyu Sharma <abhimanyusharma003@gmail.com>
 */

namespace App\Http\Controllers\Admin\User;

use App\Artvenue\Helpers\ResizeHelper;
use App\Artvenue\Models\Notification;
use App\Artvenue\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    /**
     * @param $id
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function getEdit($id)
    {
        $user = User::whereId($id)->with('images', 'comments', 'favorites')->firstOrFail();
        $title = sprintf('Editing User %s (%s)', $user->fullname, $user->username);

        return view('admin.user.edit', compact('user', 'title'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAddUser(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|alpha_num|unique:users',
            'fullname' => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = new User();
        $user->username = $request->get('username');
        $user->fullname = $request->get('fullname');
        $user->email = $request->get('email');
        $user->password = bcrypt($request->get('password'));
        $user->confirmed_at = Carbon::now();
        $user->save();

        return redirect()->back()->with('flashSuccess', 'New User is added');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEdit(Request $request)
    {
        $this->validate($request, [
            'fullname'   => 'required',
            'email'      => 'required|email',
            'blog_url'   => 'url',
            'fb_link'    => 'url',
            'tw_link'    => 'url',
            'permission' => 'required',
            'country'    => 'max:3',
            'delete'     => 'boolean',
        ]);
        $user = User::whereId($request->route('id'))->firstOrFail();
        if ($request->get('delete')) {
            foreach ($user->images()->get() as $image) {
                $d = new ResizeHelper(sprintf('%s.%s', $image->image_name, $image->type), 'uploads/images');
                $d->delete();
                $image->delete();
            }
            Notification::whereFromId($user->id)->delete();
            Notification::whereUserId($user->id)->delete();
            $user->delete();

            return redirect()->route('admin.users')->with('flashSuccess', 'User is now deleted');
        }

        $user->fullname = $request->get('fullname');
        $user->email = $request->get('email');
        $user->about_me = $request->get('about_me');
        $user->blogurl = $request->get('blog_url');
        $user->fb_link = $request->get('fb_link');
        $user->tw_link = $request->get('tw_link');
        $user->permission = $request->get('permission');
        if ($request->get('country') == 'null') {
            $user->country = null;
        } else {
            $user->country = $request->get('country');
        }
        if ($request->get('featured_at') && $user->featured_at == null) {
            $user->featured_at = Carbon::now();
        } elseif ($request->get('featured_at') == null && $user->featured_at) {
            $user->featured_at = null;
        }
        $user->save();

        return redirect()->back()->with('flashSuccess', 'User is now update');
    }

    /**
     * @param Request $request
     * @return string
     */
    public function postApprove(Request $request)
    {
        $user = User::whereId($request->get('id'))->firstOrFail();
        if ($request->get('approve') == 1) {
            $user->confirmed_at = Carbon::now();
            $user->save();

            return 'Approved';
        }
        if ($request->get('approve') == 0) {
            $user->delete();

            return 'Deleted';
        }
    }
}
