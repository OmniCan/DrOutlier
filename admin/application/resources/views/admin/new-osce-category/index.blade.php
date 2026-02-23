@extends('admin.layouts.app')

@section('panel')
<div class="row">

    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body">
                 <div class="d-flex flex-wrap justify-content-end mb-3">
                    <form action="" method="GET" class="form-inline">
                        <div class="input-group justify-content-end">
                            <input type="text" name="search" class="form-control bg--white" placeholder="@lang('Search by Category Or Chapter')" value="{{ request()->search }}">
                            <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two ">
                        <thead>
                            <tr>

                                <th>@lang('Category')</th>
                                <th>@lang('Chapter')</th>
                                <th>@lang('Color')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Premium')</th>
                                <th>@lang('Action')</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse($datalist as $k=>$data)
                                <tr>

                                    <td>{{$data->name}}</td>
                                    <td></td>
                                    <td><span class="badge " style="background: {{$data->color}}">{{$data->color}}</span></td>
                                    <td>{{($data->status == 1)?'Active':'In-Active'}}</td>
                                    <td>
                                        <span class="badge badge--{{ $data->is_premium == 1 ? 'warning' : 'success' }} premium-badge" data-id="{{$data->id}}" style="cursor: pointer;" title="Click to toggle">
                                            {{ $data->is_premium == 1 ? 'Premium' : 'Free' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a title="@lang('Edit')" href="{{ route('admin.new-osce-category.edit', $data->id)}}" class="btn btn-sm btn--primary" > <i class="la la-pen"></i></a>

                                        <button class="btn btn-outline--danger btn-sm confirmationBtn" data-question="@lang('Are you sure that you want to delete?')" data-action="{{ route('admin.new-osce-category.delete', $data->id)}}" title="Delete"><i class="las la-trash"></i></button>
                                    </td>
                                </tr>
                                @foreach($data->child as $key=>$child)
                                    <tr>

                                        <td></td>
                                        <td>{{$child->name}}</td>
                                        <td><span class="badge " style="background: {{$child->color}}">{{$child->color}}</span></td>
                                        <td>{{($child->status == 1)?'Active':'In-Active'}}</td>
                                        <td>
                                            <span class="badge badge--{{ $child->is_premium == 1 ? 'warning' : 'success' }} premium-badge" data-id="{{$child->id}}" style="cursor: pointer;" title="Click to toggle">
                                                {{ $child->is_premium == 1 ? 'Premium' : 'Free' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a title="@lang('Edit')" href="{{ route('admin.new-osce-category.edit', $child->id)}}" class="btn btn-sm btn--primary" > <i class="la la-pen"></i></a>

                                            <button class="btn btn-outline--danger btn-sm confirmationBtn" data-question="@lang('Are you sure that you want to delete?')" data-action="{{ route('admin.new-osce-category.delete', $child->id)}}" title="Delete"><i class="las la-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No Record Found !</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>

            <div class="card-footer py-4">

            </div>

        </div><!-- card end -->
    </div>
</div>


<x-confirmation-modal></x-confirmation-modal>

@endsection


@push('breadcrumb-plugins')
<a href="{{route('admin.new-osce-category.create')}}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="las la-angle-double-left"></i>@lang('Add Category')</a>
@endpush


@push('style-lib')
<link rel="stylesheet" href="{{asset('assets/admin/css/datepicker.min.css')}}">
@endpush


@push('script-lib')
<script src="{{ asset('assets/admin/js/datepicker.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/datepicker.en.js') }}"></script>
@endpush
@push('script')
<script>
    (function ($) {
        "use strict";
        if (!$('.datepicker-here').val()) {
            $('.datepicker-here').datepicker();
        }

        // Premium toggle functionality
        $('.premium-badge').on('click', function() {
            var badge = $(this);
            var id = badge.data('id');

            $.ajax({
                url: "{{ route('admin.new-osce-category.toggle-premium', '') }}/" + id,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if(response.success) {
                        if(response.is_premium == 1) {
                            badge.removeClass('badge--success').addClass('badge--warning').text('Premium');
                        } else {
                            badge.removeClass('badge--warning').addClass('badge--success').text('Free');
                        }
                        iziToast.success({
                            message: response.message,
                            position: "topRight"
                        });
                    }
                },
                error: function() {
                    iziToast.error({
                        message: 'Failed to update premium status',
                        position: "topRight"
                    });
                }
            });
        });
    })(jQuery)
</script>
@endpush
