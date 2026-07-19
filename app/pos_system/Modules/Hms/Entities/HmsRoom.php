<?php

namespace Modules\Hms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class HmsRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function type()
    {
        return $this->belongsTo(HmsRoomType::class, 'hms_room_type_id');
    }

    public static function non_booking_rooms($type_id, $arrival_date_time, $departure_date_time, $existing_rooms, $adate, $ddate, $t_id = null)
    {
        // 1. Get IDs of rooms that have active (confirmed or pending) bookings overlapping with the selected dates
        $booked_room_ids = \DB::table('hms_booking_lines')
            ->join('transactions', 'hms_booking_lines.transaction_id', '=', 'transactions.id')
            ->whereIn('transactions.status', ['confirmed', 'pending'])
            ->whereNull('transactions.check_out')
            ->when($t_id, function ($query) use ($t_id) {
                $query->where('transactions.id', '!=', $t_id);
            })
            ->where(function ($query) use ($arrival_date_time, $departure_date_time) {
                $query->where('transactions.hms_booking_arrival_date_time', '<', $departure_date_time)
                      ->where('transactions.hms_booking_departure_date_time', '>', $arrival_date_time);
            })
            ->pluck('hms_booking_lines.hms_room_id')
            ->toArray();

        // 2. Get IDs of rooms that are unavailable during the selected dates
        $unavailable_room_ids = \DB::table('hms_room_unavailables')
            ->where(function ($query) use ($adate, $ddate) {
                $query->whereDate('date_to', '>=', $adate)
                      ->whereDate('date_from', '<=', $ddate);
            })
            ->pluck('hms_rooms_id')
            ->toArray();

        // 3. Exclude both booked and unavailable room IDs
        $exclude_room_ids = array_merge($booked_room_ids, $unavailable_room_ids, $existing_rooms);
        $exclude_room_ids = array_unique(array_filter($exclude_room_ids));

        return HmsRoom::where('hms_room_type_id', $type_id)
            ->when(!empty($exclude_room_ids), function ($query) use ($exclude_room_ids) {
                $query->whereNotIn('id', $exclude_room_ids);
            })
            ->pluck('room_number', 'id')
            ->toArray();
    }

    public static function get_booked_and_unavailable_room_ids($type_id, $arrival_date_time, $departure_date_time, $existing_rooms, $adate, $ddate, $t_id = null)
    {
        // 1. Get IDs of rooms that have active (confirmed or pending) bookings overlapping with the selected dates
        $booked_room_ids = \DB::table('hms_booking_lines')
            ->join('transactions', 'hms_booking_lines.transaction_id', '=', 'transactions.id')
            ->whereIn('transactions.status', ['confirmed', 'pending'])
            ->whereNull('transactions.check_out')
            ->when($t_id, function ($query) use ($t_id) {
                $query->where('transactions.id', '!=', $t_id);
            })
            ->where(function ($query) use ($arrival_date_time, $departure_date_time) {
                $query->where('transactions.hms_booking_arrival_date_time', '<', $departure_date_time)
                      ->where('transactions.hms_booking_departure_date_time', '>', $arrival_date_time);
            })
            ->pluck('hms_booking_lines.hms_room_id')
            ->toArray();

        // 2. Get IDs of rooms that are unavailable during the selected dates
        $unavailable_room_ids = \DB::table('hms_room_unavailables')
            ->where(function ($query) use ($adate, $ddate) {
                $query->whereDate('date_to', '>=', $adate)
                      ->whereDate('date_from', '<=', $ddate);
            })
            ->pluck('hms_rooms_id')
            ->toArray();

        $exclude_room_ids = array_merge($booked_room_ids, $unavailable_room_ids, $existing_rooms);
        return array_unique(array_filter($exclude_room_ids));
    }
}
