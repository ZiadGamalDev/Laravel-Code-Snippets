<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CarLicense;
use App\Models\CarReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CarReportController extends Controller
{
    public function index()
    {
        $carReports = CarReport::with([
            'carLicense.user:id,full_name', 'garage:id,full_name'
        ])->withExists('media')->paginate(10);

        return view('car_reports.index', ['dataTable' => $carReports]);
    }

    public function report(CarLicense $carLicense)
    {
        $carReports = $carLicense->reports()->with([
            'carLicense.user:id,full_name', 'garage:id,full_name'
        ])->withExists('media')->paginate(10);

        return view('car_reports.index', ['dataTable' => $carReports]);
    }


    public function attachments($id)
    {
        $carReport = CarReport::findOrFail($id)->load('media');
        $attachments = $carReport->media->map(function ($media) {
            return $media->image;
        })->toArray();
        $type = Str::endsWith($attachments[0], '.pdf') ? 'pdf' : 'image';

        return view('car_reports.attachments', compact('attachments', 'type'));
    }

    public function user(User $user)
    {
        $carReports = $user->carReports()->with('garage:id,full_name')->paginate(10);

        return view('car_reports.index', ['dataTable' => $carReports]);
    }
}
