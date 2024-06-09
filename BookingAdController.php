<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BookingAd;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class BookingAdController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    public function index()
    {
        $bookingAds = BookingAd::with([
            'garage:id,full_name', 'media'
        ])->withCount('cars')->paginate(10);

        return view('booking_ads.index', ['dataTable' => $bookingAds]);
    }

    public function user(User $user)
    {
        $bookingAds = $user->bookingAds()->with('garage:id,full_name')->paginate(10);

        return view('booking_ads.index', ['dataTable' => $bookingAds]);
    }

    public function show(BookingAd $bookingAd)
    {
        return view('booking_ads.show', ['booking_ad' => $bookingAd]);
    }

    public function approve(BookingAd $bookingAd)
    {
        $data = [
            'status' => 1,
            'display' => true,
            'display_start_date' => now()->toDateString(),
            'display_end_date' => now()->addDays($bookingAd->display_duration)->toDateString(),
        ];

        $bookingAd->update($data);
        $this->notification($bookingAd->id, $bookingAd->garage_id, auth()->user()->full_name);

        return  redirect()->route('booking.ads.index')->with('success', 'Booking ad has been approved');
    }

    public function reject(Request $request, BookingAd $bookingAd)
    {
        $data = $request->validate(['rejection_reason' => 'nullable|string']);
        $data['status'] = 2;

        $bookingAd->update($data);
        $this->notification($bookingAd->id, $bookingAd->garage_id, auth()->user()->full_name);

        return  redirect()->route('booking.ads.index')->with('success', 'Booking ad has been rejected');
    }

    public function notification($booking_id, $reciver_id, $creator_name)
    {
        $text_en = "#$booking_id Booking Ad Status has been changed ";
        $text_ar = "#$booking_id تم تغيير حالة حجز الاعلان ";
        $notification_type_en = "booking";
        $notification_type_ar = "حجز";
        $api =  url("api/booking/ads/show/" . $booking_id);
        $this->notificationService->notification($booking_id, $creator_name,  $text_en, $text_ar, $notification_type_en, $notification_type_ar, $api, $reciver_id);
    }
}
