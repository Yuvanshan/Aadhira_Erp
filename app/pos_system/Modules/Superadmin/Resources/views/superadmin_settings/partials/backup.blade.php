<div class="pos-tab-content">
    <div class="row">
    	<div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('BACKUP_DISK', __('superadmin::lang.backup_disk') . ':') !!}
            	{!! Form::select('BACKUP_DISK', $backup_disk, $default_values['BACKUP_DISK'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-8 @if(env('BACKUP_DISK') != 'dropbox') hide @endif" id="dropbox_access_token_div">
            <div class="form-group">
            	{!! Form::label('DROPBOX_ACCESS_TOKEN', __('superadmin::lang.dropbox_access_token') . ':') !!}
            	{!! Form::text('DROPBOX_ACCESS_TOKEN', $default_values['DROPBOX_ACCESS_TOKEN'], ['class' => 'form-control','placeholder' => __('superadmin::lang.dropbox_access_token')]); !!}
            </div>
            <p class="help-block">{!! __('superadmin::lang.dropbox_help', ['link' => 'https://www.dropbox.com/developers/apps/create']) !!}</p>
        </div>
        <div class="col-xs-8 @if(env('BACKUP_DISK') != 'google') hide @endif" id="google_drive_div">
            <div class="form-group">
            	{!! Form::label('GOOGLE_DRIVE_CLIENT_ID', __('superadmin::lang.google_drive_client_id') . ':') !!}
            	{!! Form::text('GOOGLE_DRIVE_CLIENT_ID', $default_values['GOOGLE_DRIVE_CLIENT_ID'], ['class' => 'form-control','placeholder' => __('superadmin::lang.google_drive_client_id')]); !!}
            </div>
            <div class="form-group">
            	{!! Form::label('GOOGLE_DRIVE_CLIENT_SECRET', __('superadmin::lang.google_drive_client_secret') . ':') !!}
            	{!! Form::text('GOOGLE_DRIVE_CLIENT_SECRET', $default_values['GOOGLE_DRIVE_CLIENT_SECRET'], ['class' => 'form-control','placeholder' => __('superadmin::lang.google_drive_client_secret')]); !!}
            </div>
            <div class="form-group">
            	{!! Form::label('GOOGLE_DRIVE_REFRESH_TOKEN', __('superadmin::lang.google_drive_refresh_token') . ':') !!}
            	{!! Form::text('GOOGLE_DRIVE_REFRESH_TOKEN', $default_values['GOOGLE_DRIVE_REFRESH_TOKEN'], ['class' => 'form-control','placeholder' => __('superadmin::lang.google_drive_refresh_token')]); !!}
            </div>
            <div class="form-group">
            	{!! Form::label('GOOGLE_DRIVE_FOLDER', __('superadmin::lang.google_drive_folder') . ':') !!}
            	{!! Form::text('GOOGLE_DRIVE_FOLDER', $default_values['GOOGLE_DRIVE_FOLDER'], ['class' => 'form-control','placeholder' => __('superadmin::lang.google_drive_folder')]); !!}
            </div>
            <p class="help-block">{!! __('superadmin::lang.google_drive_help') !!}</p>
        </div>
    </div>
</div>