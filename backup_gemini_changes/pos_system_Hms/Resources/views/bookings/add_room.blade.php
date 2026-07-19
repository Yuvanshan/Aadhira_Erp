<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['id' => 'add_booking_room' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang('messages.add') @lang('hms::lang.rooms')</h4>
      </div>
  
      <div class="modal-body">
        <div class="form-group">
            {!! Form::label('name', __('hms::lang.type') . '*') !!}
            {!! Form::select('type', $types, '', [
                'class' => 'form-control',
                'required',
                'id' => 'type',
                'placeholder' => __('hms::lang.type'),
            ]) !!}
        </div>
       <div class="modify_field"> 
         <div class="form-group">
            {!! Form::label('no_of_adult', __('hms::lang.no_of_adult') . '*') !!}
            {!! Form::select('no_of_adult', [], '', [
              'class' => 'form-control',
              'required',
              'id' => 'no_of_adult',
              'placeholder' => __('hms::lang.no_of_adult'),
            ]) !!}
          </div>
          <div class="form-group">
              {!! Form::label('no_of_child', __('hms::lang.no_of_child') . '*') !!}
              {!! Form::select('no_of_child', [], '', [
                'class' => 'form-control',
                'required',
                'id' => 'no_of_child',
                'placeholder' => __('hms::lang.no_of_child'),
              ]) !!}
          </div>
          <div class="form-group">
            {!! Form::label('room_no', __('hms::lang.room_no') . '*') !!}
            {!! Form::select('room_no', [], '', [
                'class' => 'form-control',
                'required',
                'id' => 'room_no',
                'placeholder' => __('hms::lang.room_no'),
            ]) !!}
          </div>
       </div>
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.save' )</button>
        <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->

  <script>
      $(document).ready(function() {
          console.log("Add Room modal loaded!");
          $('#type').on('change', function() {
              console.log("Change event on #type inside modal, value: ", $(this).val());
              if (!$(this).val()) {
                  return;
              }
              var roomIds = [];
              $('.room-id-input').each(function() {
                  roomIds.push($(this).val());
              });
              $.ajax({
                  url: "{{ route('get_room_type_by') }}",
                  dataType: 'html',
                  data: {
                      'type_id': $(this).val(),
                      'arrival_date': $('#arrival_date').val(),
                      'departure_date': $('#departure_date').val(),
                      'arrival_time': $('#arrival_time').val(),
                      'departure_time': $('#departure_time').val(),
                      'room_ids': roomIds,
                  },
                  success: function(result) {
                      console.log("AJAX success returned result for get_room_type_by!");
                      $('.modify_field').html(result);
                  },
                  error: function(xhr, status, error) {
                      console.error("AJAX error: ", error);
                  }
              });
          });
      });
  </script>