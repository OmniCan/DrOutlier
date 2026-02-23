@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-12">
        <div class="row">
             
            <div class="col-xl-12">
                <div class="card">
                    <div class="card p-2">
                         
                    <div class="card-header">
                        <h5 class="card-title mb-0">@lang('Information of') {{$user->fullname}}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.users.update',[$user->id])}}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                             

                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>@lang('First Name')</label>
                                        <input class="form-control" type="text" name="firstname" required
                                            value="{{$user->firstname}}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label">@lang('Last Name')</label>
                                        <input class="form-control" type="text" name="lastname" required
                                            value="{{$user->lastname}}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Email') </label>
                                        <input class="form-control" type="email" name="email" value="{{$user->email}}"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Mobile Number') </label>
                                        <div class="input-group "> 
                                            <input type="number" name="mobile" value="{{ old('mobile') }}" id="mobile"
                                                class="form-control checkUser" required>
                                        </div>
                                    </div>
                                </div>
                            </div>


                             
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="form-group  text-end mb-0">
                                        <button type="submit" class="btn btn--primary btn-global">@lang('Update')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
 
 
@endsection


@push('script')
<script>
    (function ($) {
        "use strict"
        $('.bal-btn').on('click', function () {
            var act = $(this).data('act');
            $('#addSubModal').find('input[name=act]').val(act);
            if (act == 'add') {
                $('.type').text('Add');
            } else {
                $('.type').text('Subtract');
            }
        });
        let mobileElement = $('.mobile-code');
        $('select[name=country]').change(function () {
            mobileElement.text(`+${$('select[name=country] :selected').data('mobile_code')}`);
        });

        $('select[name=country]').val('{{@$user->country_code}}');
        let dialCode = $('select[name=country] :selected').data('mobile_code');
        let mobileNumber = `{{ $user->mobile }}`;
        mobileNumber = mobileNumber.replace(dialCode, '');
        $('input[name=mobile]').val(mobileNumber);
        mobileElement.text(`+${dialCode}`);

    })(jQuery);
</script>
@endpush
