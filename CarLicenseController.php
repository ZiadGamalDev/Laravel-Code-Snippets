<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CarLicense;
use App\Models\User;
use Illuminate\Http\Request;

class CarLicenseController extends Controller
{
    public function index()
    {
        $carLicenses = CarLicense::with('user:id,full_name')->paginate(10);

        return view('car_licenses.index', ['dataTable' => $carLicenses]);
    }

    public function user(User $user)
    {
        $carLicenses = CarLicense::with('user:id,full_name')->where('user_id', $user->id)->paginate(10);

        return view('car_licenses.index', ['dataTable' => $carLicenses]);
    }
}
