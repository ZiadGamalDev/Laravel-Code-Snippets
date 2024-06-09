<?php

namespace App\Http\Repositories\Web;

use App\Http\Interfaces\Web\DashboardInterface;
use App\Models\BookingAd;
use App\Models\BookingService;
use App\Models\BookingWinch;
use App\Models\GarageData;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardRepository implements DashboardInterface
{
    public function index()
    {
        $stats = [
            'customers_count' => User::where('role_id', 2)->count(),
            'providers_count' => GarageData::count(),
            'bookings_count' => BookingService::count() + BookingWinch::count() + BookingAd::count(),
            'bookings_amount' => $this->calculateBookingsAmount(),
            'bookings' => $this->getBookings(),
            'eProviders' => GarageData::with('checkService:id,name')->take(3)->get(),
        ];

        return view('dashboard.index', $stats);
    }

    private function calculateBookingsAmount()
    {
        $bookingServiceAmount = BookingService::where('payment_stataus', 'paid')->sum('payment_amount');
        $bookingWinchAmount = BookingWinch::where('payment_stataus', 'paid')->sum('payment_amount');
        $bookingAdAmount = BookingAd::where('status', '!=', 3)->sum('amount');

        return number_format($bookingServiceAmount + $bookingWinchAmount + $bookingAdAmount, 2, '.', '');
    }


    private function getBookings()
    {
        $currentYear = Carbon::now()->year;

        $bookingServices = BookingService::selectRaw('payment_amount as amount')
            ->where('payment_stataus', 'paid')
            ->whereYear('created_at', $currentYear)
            ->get();

        $bookingWinchs = BookingWinch::selectRaw('payment_amount as amount')
            ->where('payment_stataus', 'paid')
            ->whereYear('created_at', $currentYear)
            ->get();

        $bookingAds = BookingAd::select('amount')
            ->where('status', '!=', 3)
            ->whereYear('created_at', $currentYear)
            ->get();

        return $bookingServices->concat($bookingWinchs)->concat($bookingAds);
    }
}
