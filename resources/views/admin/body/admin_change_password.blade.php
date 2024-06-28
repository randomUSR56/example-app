@extends('admin.admin_master')
@section('admin')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">Change Password Page</h4>

                        @if (count($errors) > 0)

                            @foreach ($errors -> all() as $error)

                                <p class="alert alert-danger alert-dismissible fade show">{{ $error }}</p>

                            @endforeach

                        @endif

                        <form method="POST" action="{{ route('update.password') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="example-text-input" class="col-sm-2 col-form-label">Old Password</label>
                                <div class="col-sm-10">
                                    <input class="form-control" name="old_password" type="password" id="old_password" value="">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="example-text-input" class="col-sm-2 col-form-label">New Password</label>
                                <div class="col-sm-10">
                                    <input class="form-control" name="new_password" type="password" id="new_password" value="">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="example-text-input" class="col-sm-2 col-form-label">Confirm Password</label>
                                <div class="col-sm-10">
                                    <input class="form-control" name="confirm_password" type="password" id="confirm_password" value="">
                                </div>
                            </div>

                            <!-- end row -->

                            <input type="submit" value="Change Password" class="btn btn-info waves-effect waves-light">

                        </form>
                    </div>
                </div>
            </div> <!-- end col -->
        </div>

    </div>
</div>

@endsection
