<?php

namespace Modules\Connector\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\CommonResource;
use Modules\Hms\Entities\HmsRoom;

/**
 * @group Room management
 * @authenticated
 *
 * APIs for managing hotel rooms
 */
class RoomController extends ApiController
{
    /**
     * List rooms
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

        $rooms = HmsRoom::whereHas('type', function ($q) use ($business_id) {
                            $q->where('business_id', $business_id);
                        })
                        ->with(['type.Pricings'])
                        ->orderBy('room_number', 'asc')
                        ->get();

        return CommonResource::collection($rooms);
    }
}
