<?php
/**
 * @author Abhimanyu Sharma <abhimanyusharma003@gmail.com>
 */

namespace App\Http\Controllers\Admin\Image;

use App\Artvenue\Helpers\Resize;
use App\Artvenue\Models\Image;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ImageController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function getIndex(Request $request)
    {
        $title = sprintf('List of %s images', ucfirst($request->get('type')));
        $type = $request->get('type');

        return view('admin.image.index', compact('title', 'type'));
    }

    /**
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function getBulkUpload()
    {
        $title = sprintf('Bulkupload');

        return view('admin.image.bulkupload', compact('title'));
    }

    /**
     * @return mixed
     */
    public function getData(Request $request)
    {
        $images = Image::query()->with('user', 'favorites');

        switch ($request->get('type')) {
            case 'approved':
                $images->approved();
                break;
            case 'featured':
                $images->whereNotNull('images.featured_at');
                break;
            case 'approvalRequired':
                $images->whereNull('images.approved_at');
                break;
            default:
                $images->approved();
        }

        $datatables = Datatables::of($images);

        if ($request->get('type') == 'approvalRequired') {
            $datatables->addColumn('actions', function ($image) {
                return '<a href="#" class="image-approve btn btn-xs btn-success" data-approve="' . $image->id . '"><i class="fa fa-check"></i></a>
                 <a href="' . route('admin.images.edit', [$image->id]) . '" class="btn btn-xs btn-info" target="_blank"><i class="fa fa-edit"></i></a>
                <a href="#" class="image-disapprove btn btn-xs btn-danger" data-disapprove="' . $image->id . '"><i class="fa fa-times"></i></a>';
            });
        } else {
            $datatables->addColumn('actions', function ($image) {
                return '<a href="' . route('admin.images.edit', [$image->id]) . '" class="btn btn-xs btn-info" target="_blank"><i class="fa fa-edit"></i></a>
                <a href="' . route('image', [$image->id, $image->slug]) . '" class="btn btn-xs btn-success" target="_blank"><i class="fa fa-search"></i></a>';
            });
        }

        $datatables->addColumn('thumbnail', function ($image) {
            return '<img src="' . Resize::image($image, 'gallery') . '" style="max-width:50px;max-height:50px"/>';
        });

        $datatables->editColumn('favorites', function ($image) {
            return $image->favorites->count();
        });

        $datatables->editColumn('created_at', function ($image) {
            if ($image->created_at) {
                return $image->created_at->toFormattedDateString();
            }
        });
        $datatables->editColumn('updated_at', function ($image) {
            if ($image->updated_at) {
                return $image->updated_at->toFormattedDateString();
            }
        });
        $datatables->editColumn('featured_at', function ($image) {
            if ($image->featured_at === null) {
                return '---';
            }

            return $image->featured_at->toFormattedDateString();
        });
        $datatables->editColumn('title', '{!! str_limit($title, 20) !!}');

        return $datatables->make(true);
    }
}
