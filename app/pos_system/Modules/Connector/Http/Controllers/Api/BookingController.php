<?php

namespace Modules\Connector\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\CommonResource;
use Modules\Hms\Entities\HmsTransactionClass;

/**
 * @group Booking management
 * @authenticated
 *
 * APIs for managing hotel bookings
 */
class BookingController extends ApiController
{
    /**
     * List bookings
     *
     * @response {
     *      "data": []
     * }
     */
    public function index()
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module_util = new \App\Utils\ModuleUtil();
        if (!(env('APP_DEBUG') || $module_util->hasThePermissionInSubscription($business_id, 'hms_mobile_app_access') || auth()->user()->can('superadmin'))) {
            abort(403, 'Unauthorized action. No mobile app access subscription.');
        }

        $bookings = HmsTransactionClass::where('business_id', $business_id)
                        ->where('type', 'hms_booking')
                        ->with(['hms_booking_lines', 'hms_booking_extras.extra', 'contact'])
                        ->orderBy('transaction_date', 'desc')
                        ->get();

        return CommonResource::collection($bookings);
    }

    /**
     * Get the specified booking
     *
     * @urlParam booking required comma separated ids of the bookings
     * @response {
     *      "data": []
     * }
     */
    public function show($booking_ids)
    {
        $user = Auth::user();
        $business_id = $user->business_id;
        $booking_ids = explode(',', $booking_ids);

        $module_util = new \App\Utils\ModuleUtil();
        if (!(env('APP_DEBUG') || $module_util->hasThePermissionInSubscription($business_id, 'hms_mobile_app_access') || auth()->user()->can('superadmin'))) {
            abort(403, 'Unauthorized action. No mobile app access subscription.');
        }

        $bookings = HmsTransactionClass::where('business_id', $business_id)
                        ->where('type', 'hms_booking')
                        ->whereIn('id', $booking_ids)
                        ->with(['hms_booking_lines', 'hms_booking_extras.extra', 'contact'])
                        ->get();

        return CommonResource::collection($bookings);
    }
}
