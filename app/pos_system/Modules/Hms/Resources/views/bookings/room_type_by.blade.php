<div class="form-group">
    {!! Form::label('no_of_adult', __('hms::lang.no_of_adult') . '*') !!}
    <select class="form-control" id="no_of_adult" required name="no_of_adult">
        @for ($i = 1; $i <= $type->no_of_adult; $i++)
            <option value="{{ $i }}">{{ $i }}</option>
        @endfor
    </select>
</div>
<div class="form-group">
    {!! Form::label('no_of_child', __('hms::lang.no_of_child') . '*') !!}
    <select class="form-control" id="no_of_child" required name="no_of_child">
        @for ($i = 0; $i <= $type->no_of_child; $i++)
            <option value="{{ $i }}">{{ $i }}</option>
        @endfor
    </select>
</div>
<div class="form-group">
    {!! Form::label('room_no', __('hms::lang.room_no') . '*') !!}
    <select class="form-control" id="room_no" name="room_no" required>
        <option value="">{{ __('hms::lang.room_no') }}</option>
        @foreach ($rooms as $room)
            @php
                $is_booked = in_array($room->id, $exclude_room_ids);
                $label = $room->room_number . ($is_booked ? ' (Booked)' : '');
            @endphp
            <option value="{{ $room->id }}" {{ $is_booked ? 'disabled style=opacity:0.5;' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
</div>
