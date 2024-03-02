
<?php $a = announcements('pop'); ?>
@if ($a?->status === 'active')
<button type="button" class="btn btn-outline-primary block d-none" data-toggle="modal" data-target="#border-less-pop" id="auto-pop-up">
    Launch Modal
</button>

<!--Basic Modal -->
<div class="modal fade text-left modal-borderless" id="border-less-pop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="announcement">
        <div class="modal-content" style="min-height: 240px">
            {{-- <div class="modal-header">
                <h3 class="modal-title"></h3>
                <button type="button" class="close rounded-pill" data-dismiss="modal" aria-label="Close">
                    <i class="bx bx-x"></i>
                </button>
            </div> --}}
            <div class="modal-body">
                <p class="text-center">
                    {!! $a->message !!}
                </p>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-light-primary" data-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Close</span>
                </button>
            </div>
        </div>
    </div>
</div>

@section('page-script')
    <script>
        setTimeout(() => {
            $('#auto-pop-up').click();
        }, 3000);
    </script>
@endsection
@endif
