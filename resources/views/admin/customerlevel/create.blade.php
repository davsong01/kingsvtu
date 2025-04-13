@extends('layouts.app')
@section('title', 'Add Customer Level')
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
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard', request()->array)}}"><i class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ route('customerlevel.index') }}">Customer Levels</a>
                                    </li>
                                    <li class="breadcrumb-item active">Add Customer Level
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic Inputs start -->
                <section id="basic-input">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Add Customer Level</h4>
                                    @include('layouts.alerts')
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form action="{{route('customerlevel.store')}}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <fieldset class="form-group">
                                                        <label for="name">Name</label>
                                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter name" required>
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="upgrade_amount">Upgrade Amount</label>
                                                        <input type="text" class="form-control" id="upgrade_amount" name="upgrade_amount" value="{{ old('upgrade_amount') }}" placeholder="Enter Upgrade amount" required>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-6">
                                                    <fieldset class="form-group">
                                                        <label for="make_api_level">Make API Level</label>
                                                        <select class="form-control" name="make_api_level" id="make_api_level" required>
                                                            <option value="">Select</option>
                                                            <option value="yes" {{ old('make_api_level') == 'yes' ? 'selected' : '' }}>Yes
                                                            </option>
                                                            <option value="no" {{ old('make_api_level') == 'make_api_level' ? 'selected' : '' }}>No
                                                            </option>
                                                        </select>
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="order">Order</label>
                                                        <input type="number" class="form-control" name="order" value="{{ old('order') }}" placeholder="Enter order" id="order" required>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-12">
                                                    <fieldset class="form-group">
                                                        <label for="type">Extra Benefit</label>
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
                                                        <div class="editor" style="min-height: 250px">
                                                            {!! old('extra_benefit') !!}
                                                        </div>
                                                        <input name="extra_benefit" type="hidden" id="extra_benefit" />
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-12">
                                                    <fieldset class="form-group">
                                                        <label for="status">Status</label>
                                                        <select class="form-control" name="status" id="status" required>
                                                            <option value="">Select</option>
                                                            <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active
                                                            </option>
                                                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive
                                                            </option>
                                                        </select>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-12">
                                                <button class="btn btn-primary" type="submit">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
@section('page-script')
<script src="{{ asset('app-assets/js/scripts/pages/dashboard-analytics.js') }}"></script>
 <link href="/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css" />
    
<script>
        let quill = new Quill('.editor', {
            theme: 'snow',
            toolbar: true,
            placeholder: 'Enter content...',
            modules: {
                syntax: true,
                toolbar: '#toolbar-container',
            },
        });

        $('form').on('submit', () => {
            var myEditor = document.querySelector('.editor')
            var html = myEditor.children[0].innerHTML;
            $('#content').val(html);
        });
    </script>
@endsection