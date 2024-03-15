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
                                    <li class="breadcrumb-item"><a href="{{ route('admin.trans') }}">Transactions</a>
                                    </li>
                                    <li class="breadcrumb-item active">Verify Biller
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
                                    <h4 class="card-title">Verify Biller</h4>
                                    <p>Verify biller code with provider</p>
                                    @include('layouts.alerts')
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form action="{{ route('admin.verify.post') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <fieldset class="form-group col-sm-6 col-12"">
                                                    <label for="product">Select Product</label>
                                                    <select class="form-control" name="product" id="product" required>
                                                        <option value="">Select Product</option>
                                                        @foreach ($products as $pro)
                                                            <option value="{{ $pro->slug }}">{{ $pro->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                                <fieldset class="form-group col-sm-6 col-12"">
                                                    <label for="product">Select API</label>
                                                    <select class="form-control" name="api" id="product" required>
                                                        <option value="">Select API</option>
                                                        @foreach ($api as $pro)
                                                            <option value="{{ $pro->id }}">{{ $pro->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                                <div class="form-group col-sm-6 col-12">
                                                    <label for="type">Select Type</label>
                                                    <select class="form-control" name="type" id="type" required>
                                                        <option value="">Select Type</option>
                                                        <option value="prepaid">Prepaid</option>
                                                        <option value="postpaid">Postpaid</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-sm-6 col-12">
                                                    <label class="text-bold-600" for="lastName">
                                                        Value
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="lastName"
                                                        name="value" value="{{ old('last_name') }}"
                                                        placeholder="Value to verify" required>
                                                </div>
                                            </div>


                                            <div class="">
                                                <button class="btn btn-primary" type="submit">Submit</button>
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

    <script>
        $('form').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: this.action,
                type: 'post',
                data: $(this).serializeArray(),
                beforeSend: () => {

                },
                success: res => {
                    console.log(res);
                },
                error: () => alert('Hmph, something went south!'),
            })
        })
    </script>
@endsection
