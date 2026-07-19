<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-12">
            <h4 class="tw-text-lg tw-font-bold tw-text-gray-700 tw-mb-4">
                <i class="fa fa-hdd-o text-primary"></i> @lang('lang_v1.backup_settings')
            </h4>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('backup_email', __('lang_v1.backup_email') . ':') !!}
                {!! Form::email('common_settings[backup_email]', !empty($common_settings['backup_email']) ? $common_settings['backup_email'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.backup_email_placeholder')]) !!}
                <p class="help-block">This email will receive a copy of the backup zip file when a backup runs.</p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <div class="checkbox" style="margin-top: 30px;">
                    <label>
                        {!! Form::checkbox('common_settings[backup_auto]', 1, !empty($common_settings['backup_auto']) ? true : false, ['class' => 'input-icheck']) !!}
                        <strong>@lang('lang_v1.enable_daily_auto_backup')</strong>
                    </label>
                    <p class="help-block">Automatically execute and email a full backup once a day.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('business_backup_disk', __('lang_v1.backup_disk') . ':') !!}
                {!! Form::select('common_settings[backup_disk]', ['local' => 'Local', 'dropbox' => 'Dropbox', 'google' => 'Google Drive'], !empty($common_settings['backup_disk']) ? $common_settings['backup_disk'] : 'local', ['class' => 'form-control', 'id' => 'business_backup_disk']) !!}
            </div>
        </div>
    </div>

    <div class="row @if(empty($common_settings['backup_disk']) || $common_settings['backup_disk'] != 'dropbox') hide @endif" id="business_dropbox_div">
        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label('business_dropbox_access_token', __('lang_v1.dropbox_access_token') . ':') !!}
                {!! Form::text('common_settings[dropbox_access_token]', !empty($common_settings['dropbox_access_token']) ? $common_settings['dropbox_access_token'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.dropbox_access_token'), 'id' => 'business_dropbox_access_token']) !!}
            </div>
        </div>
    </div>

    <div class="row @if(empty($common_settings['backup_disk']) || $common_settings['backup_disk'] != 'google') hide @endif" id="business_google_div">
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('business_google_drive_client_id', __('lang_v1.google_drive_client_id') . ':') !!}
                {!! Form::text('common_settings[google_drive_client_id]', !empty($common_settings['google_drive_client_id']) ? $common_settings['google_drive_client_id'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.google_drive_client_id'), 'id' => 'business_google_drive_client_id']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('business_google_drive_client_secret', __('lang_v1.google_drive_client_secret') . ':') !!}
                {!! Form::text('common_settings[google_drive_client_secret]', !empty($common_settings['google_drive_client_secret']) ? $common_settings['google_drive_client_secret'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.google_drive_client_secret'), 'id' => 'business_google_drive_client_secret']) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('business_google_drive_refresh_token', __('lang_v1.google_drive_refresh_token') . ':') !!}
                {!! Form::text('common_settings[google_drive_refresh_token]', !empty($common_settings['google_drive_refresh_token']) ? $common_settings['google_drive_refresh_token'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.google_drive_refresh_token'), 'id' => 'business_google_drive_refresh_token']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('business_google_drive_folder', __('lang_v1.google_drive_folder') . ':') !!}
                {!! Form::text('common_settings[google_drive_folder]', !empty($common_settings['google_drive_folder']) ? $common_settings['google_drive_folder'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.google_drive_folder'), 'id' => 'business_google_drive_folder']) !!}
            </div>
        </div>
        <div class="col-sm-12">
            <p class="help-block">{!! __('lang_v1.google_drive_help') !!}</p>
        </div>
    </div>

    @if(auth()->user()->can('backup'))
    <div class="row" style="margin-top: 20px;">
        <div class="col-xs-12">
            <div class="well well-sm" style="background-color: #fcfcfc; border-color: #e5e5e5;">
                <h4 style="margin-top: 5px; font-weight: bold;"><i class="fa fa-info-circle text-info"></i> Manual Backup & Email</h4>
                <p>Run a manual full system backup. The backup file will be saved to your configured backup drive and sent to the backup email configured above.</p>
                <a href="{{ action([\App\Http\Controllers\BackUpController::class, 'create']) }}" class="btn btn-primary" id="run-manual-backup-btn">
                    <i class="fa fa-play-circle"></i> Run Backup Now
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
