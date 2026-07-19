<div class="container-fluid" style="overflow-y: hidden; overflow-x: auto; width: 100%">
    <div class="box box-primary card">

        @unless(empty($title) && empty($header))
            <div class="box-header with-border">
                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                    @unless(empty($title))
                        <h3 class="box-title">{{ $title }}</h3>
                    @endunless

                    {{-- To add a button on the right add box-tools class to the div in your header --}}
                    @unless(empty($header))
                        <div class="box-tools">
                            {{ $header }}
                        </div>
                    @endunless
                </div>
            </div>
        @endunless

        <div class="box-body">
            {{ $body }}
        </div>
        <!-- /.box-body -->

        @unless(empty($footer))
            <div class="box-footer">
                {{ $footer }}
            </div>
        @endunless
    </div>

</div>
