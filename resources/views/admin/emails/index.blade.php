@extends('layouts.app')
@section('content')
    <!-- Content wrapper -->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-12 mb-2 mt-1">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb p-0 mb-0">
                                    <li class="breadcrumb-item"><a href="/"><i class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Emails
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <section id="table-success">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">Email Management</h4>
                            @if (Route::is('emails.index'))
                                <a class="btn btn-danger mr-1 mb-1" href="{{ route('emails.sweep') }}">
                                    <i class="fa fa-trash"></i><span class="align-middle ml-25">Clear Emails</span>
                                </a>
                            @endif
                        </div>
                        @include('layouts.alerts')
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            {{-- <form action="{{ route('admin.trans') }}" method="GET">
                                <div class="row">
                                    <div class="col-md-3">
                                        <fieldset class="form-group">
                                            <label for="email">Transaction Email</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter customer email address" value="{{ \Request::get('email')}}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3">
                                        <fieldset class="form-group">
                                            <label for="phone">Transaction Phone</label>
                                            <input type="phone" class="form-control" id="phone" name="phone" placeholder="Enter customer phone number" value="{{ \Request::get('phone')}}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3">
                                        <fieldset class="form-group">
                                            <label for="service">Service</label>
                                            <select class="form-control" name="service" id="service">
                                                <option value="">Select</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}" {{ \Request::get('service') == $product->id ? 'selected' : ''}}>{{ $product->display_name }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3">
                                        <fieldset class="form-group">
                                            <label for="transaction_id">Transaction ID</label>
                                            <input type="text" class="form-control" id="transaction_id" name="transaction_id" placeholder="Enter transaction ID" value="{{ \Request::get('transaction_id')}}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3">
                                        <fieldset class="form-group">
                                            <label for="unique_element">Unique Element</label>
                                            <input type="text" class="form-control" id="unique_element" name="unique_element" placeholder="Enter unique element" value="{{ \Request::get('unique_element') }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3">
                                        <fieldset class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" name="status" id="status">
                                                <option value="">Select</option>
                                                <option value="delivered" {{ \Request::get('status') == 'delivered' ? 'selected' : ''}}>Delivered</option>
                                                <option value="failed" {{ \Request::get('status') == 'failed' ? 'selected' : ''}}>Failed</option>
                                                <option value="attention-required" {{ \Request::get('status') == 'attention-required' ? 'selected' : ''}}>Attention Required</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-2">
                                        <fieldset class="form-group">
                                            <label for="from">From</label>
                                            <input type="date" class="form-control" value="{{ \Request::get('from')}}" name="from">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-2">
                                        <fieldset class="form-group">
                                            <label for="to">To</label>
                                            <input type="date" class="form-control" value="{{ \Request::get('to')}}" name="to">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="submit" class="form-control btn btn-primary mt-2" value="Search">
                                    </div>
                                </div>
                            </form> --}}
                            <hr>
                        </div>
                        <div class="table-responsive">
                            {{-- <form method="post"> --}}
                            <table id="table-extended-success" class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Recipients</th>
                                        <th>Subject</th>
                                        <th>Content</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($emails as $mail)
                                        <tr>
                                            <td>
                                                {{ $mail->recipient }}
                                            </td>
                                            <td>
                                                {{ $mail->subject }}
                                            </td>
                                            <td>
                                                <span class="content" data-content="{{ $mail->content }}">
                                                    {!! substr($mail->content, 0, 250) !!}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $mail->status }}
                                            </td>
                                            <td>
                                                @if ($mail->status != 'pending')
                                                    <a class="btn btn-primary btn-sm mr-1 mb-1"
                                                        href="{{ route('emails.resend', $mail->id) }}">
                                                        <i class="fa fa-refresh"></i><span
                                                            class="align-middle ml-25">View</span>
                                                    </a>
                                                    <a class="btn btn-warning btn-sm mr-1 mb-1"
                                                        href="{{ route('emails.resend', $mail->id) }}">
                                                        <i class="fa fa-refresh"></i><span
                                                            class="align-middle ml-25">Resend</span>
                                                    </a>
                                                    <a class="btn btn-danger btn-sm mr-1 mb-1"
                                                        href="{{ route('emails.destroy', $mail->id) }}">
                                                        <i class="fa fa-trash"></i><span
                                                            class="align-middle ml-25">Delete</span>
                                                    </a>
                                                @endif
                                                @if ($mail->status == 'pending')
                                                    <a class="btn btn-success btn-sm mr-1 mb-1" href="{{ route('emails-send', $mail->id) }}">
                                                        Send
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm mr-1 mb-1 edit-mail"
                                                        data-toggle="modal" data-target="#large"
                                                        data-id="{{ route('emails.update', $mail->id) }}">
                                                        Edit
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </form>
                            {{ $emails->render() }}
                            <div class="modal fade text-left" id="large" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel17" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
                                    role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myModalLabel17">Edit Email Content</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <i class="bx bx-x"></i>
                                            </button>
                                        </div>
                                        <form action="" method="post" class="form-actions">
                                            @method('PATCH')
                                            <div class="modal-body" style="min-height: 30rem">
                                                <fieldset class="form-group">
                                                    {{-- <label for="type"></label> --}}
                                                    <div id="toolbar-container">
                                                        <span class="ql-formats">
                                                            <select class="ql-font"></select>
                                                            <select class="ql-size"></select>
                                                        </span>
                                                        <span class="ql-formats">
                                                            <button class="ql-bold"></button>
                                                            <button class="ql-italic"></button>
                                                            <button class="ql-underline"></button>
                                                            <button class="ql-strike"></button>
                                                        </span>
                                                        <span class="ql-formats">
                                                            <select class="ql-color"></select>
                                                            <select class="ql-background"></select>
                                                        </span>
                                                        <span class="ql-formats">
                                                            <button class="ql-script" value="sub"></button>
                                                            <button class="ql-script" value="super"></button>
                                                        </span>
                                                        <span class="ql-formats">
                                                            <button class="ql-header" value="1"></button>
                                                            <button class="ql-header" value="2"></button>
                                                            <button class="ql-blockquote"></button>
                                                            <button class="ql-code-block"></button>
                                                        </span>
                                                        <span class="ql-formats">
                                                            <button class="ql-list" value="ordered"></button>
                                                            <button class="ql-list" value="bullet"></button>
                                                            <button class="ql-indent" value="-1"></button>
                                                            <button class="ql-indent" value="+1"></button>
                                                        </span>
                                                        <span class="ql-formats">
                                                            <button class="ql-direction" value="rtl"></button>
                                                            <select class="ql-align"></select>
                                                        </span>
                                                        <span class="ql-formats">
                                                            <button class="ql-link"></button>
                                                            <button class="ql-image"></button>
                                                            <button class="ql-video"></button>
                                                            <button class="ql-formula"></button>
                                                        </span>
                                                        <span class="ql-formats">
                                                            <button class="ql-clean"></button>
                                                        </span>
                                                    </div>
                                                    <div class="editor">

                                                    </div>
                                                    <input name="message" type="hidden" id="content" />
                                                </fieldset>
                                                @csrf
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light-secondary"
                                                    data-dismiss="modal">
                                                    <i class="bx bx-x d-block d-sm-none"></i>
                                                    <span class="d-none d-sm-block">Close</span>
                                                </button>
                                                <button type="submit" class="btn btn-primary ml-1" {{-- data-dismiss="modal" --}}>
                                                    <i class="bx bx-check d-block d-sm-none"></i>
                                                    <span class="d-none d-sm-block">Update</span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@section('page-script')
    {{-- <script src="{{asset('asset/js/app-logistics-dashboard.js')}}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css" />
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
        let quill = new Quill('.editor', {
            theme: 'snow',
            toolbar: true,
            placeholder: 'Edit mail...',
            modules: {
                syntax: true,
                toolbar: '#toolbar-container',
            },
        });

        $('.edit-mail').click(function() {
            let btn = $(this);
            let id = btn.data('id');
            let content = btn.parents('tr').find('.content').data('content');
            $('.editor p').html(content)
            $('.form-actions').prop('action', id);
        });

        $('form').on('submit', (e) => {
            var myEditor = document.querySelector('.editor')
            var html = myEditor.children[0].innerHTML;
            $('#content').val(html);
        });
    </script>
@endsection
