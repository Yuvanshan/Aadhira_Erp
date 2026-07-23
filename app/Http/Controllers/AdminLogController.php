<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use App\BusinessLocation;
use App\User;
use Illuminate\Http\Request;

class AdminLogController extends Controller
{
    /**
     * Display a listing of the admin activity logs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        
        // Ensure user is authorized to view admin logs
        if (!auth()->user()->can('business_settings.access') && !auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        // Get locations permitted to this user
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $permitted_locations = $user->permitted_locations();
        
        $locations = BusinessLocation::forDropdown($business_id, false);
        
        // Filter dropdown to only permitted locations if not 'all'
        if ($permitted_locations !== 'all') {
            $locations = $locations->filter(function ($value, $key) use ($permitted_locations) {
                return in_array($key, $permitted_locations);
            });
        }

        $query = Activity::where('business_id', $business_id)
            ->with(['causer'])
            ->orderBy('created_at', 'desc');

        // Apply location filtering
        if ($request->has('location_id') && !empty($request->input('location_id'))) {
            $location_id = $request->input('location_id');
            // Security check: check if user is permitted to view logs for this location
            if ($permitted_locations !== 'all' && !in_array($location_id, $permitted_locations)) {
                abort(403, 'Unauthorized location.');
            }
            $query->where('location_id', $location_id);
        } else {
            // If user has access only to specific locations, restrict the logs to those locations
            if ($permitted_locations !== 'all') {
                $query->whereIn('location_id', $permitted_locations);
            }
        }

        $activities = $query->paginate(30);

        return view('admin_log.index')
            ->with(compact('activities', 'locations'));
    }
}
