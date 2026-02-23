@extends('admin.layouts.app')

@section('panel')
<div class="row">

    <div class="col-lg-12"> 
        <div class="card b-radius--10 ">
            <div class="card-body">
                 <div class="d-flex flex-wrap justify-content-end mb-3">
                    <form action="" method="GET" class="form-inline">
                        <div class="input-group justify-content-end">
                            <input type="text" name="search" class="form-control bg--white" placeholder="@lang('Search by  title')" value="{{ request()->search }}">
                            <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr> 
                                <th>@lang('Title')</th> 
                                <th>@lang('sort_order')</th>
                                <th>@lang('Image')</th> 
                                <th>@lang('Action')</th>

                            </tr>
                        </thead>
                        <tbody> 
                            @forelse($osceslist as $osces) 
                                <tr>
                                    <td>
                                        {{$osces->title}}
                                    </td>
                                    <td>
                                        <input type="number" class="form-control col-xs-1 sort_order width1 text-center"
                                            data-id="{{ $osces->id }}" placeholder=""
                                            value="{{ $osces->sort_order ?? '' }}" style="width:60px;">
                                    </td>
                                    <td>
                                        <img src="{{getImage(getFilePath('OsceImage').'/'.@$osces->image)}}" height="50" width="50"/>
                                    </td> 
                                    <td>
                                        <a title="@lang('Edit')" href="{{ route('admin.osce.osce-edit', $osces->id)}}" class="btn btn-sm btn--primary" > <i class="la la-pen"></i></a>

                                        <button class="btn btn-outline--danger btn-sm confirmationBtn" data-question="@lang('Are you sure that you want to delete?')" data-action="{{ route('admin.osce.osce-delete', $osces->id)}}" title="Delete"><i class="las la-trash"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No Record Found !</td>
                                </tr>
                            @endif
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
             
            <div class="card-footer py-4">
                {{ paginateLinks($osceslist) }}
            </div>
            
        </div><!-- card end -->
    </div>
</div>


<x-confirmation-modal></x-confirmation-modal>

@endsection


@push('breadcrumb-plugins')
<a href="{{route('admin.osce.osce-create')}}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="las la-angle-double-left"></i>@lang('Add Osce')</a>
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
    (function($) {
  "use strict";

  if (!$('.datepicker-here').val()) {
      $('.datepicker-here').datepicker();
  }

  $(".sort_order").on("blur", function(e) {
      e.preventDefault();

      var status_id = $(this).data('id');
      var sort_order = $(this).val();
      if (sort_order === "" || isNaN(sort_order)) {
          Toast.fire({
              icon: 'error',
              title: 'Invalid Sort Order'
          });
          return;
      }

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': "{{ csrf_token() }}"
          }
      });

      $.ajax({
          type: "POST",
          url: "{{ route('admin.osce.update-sort-order') }}",
          data: {
              status_id: status_id,
              sort_order: sort_order
          },
          dataType: "json",
          success: function(data) {
              if (data.success) {
                  Toast.fire({
                      icon: 'success',
                      title: data.message || 'Sort Order Updated Successfully'
                  });
              } else {
                  Toast.fire({
                      icon: 'error',
                      title: data.message || 'Failed to update Sort Order'
                  });
              }
          },
          error: function(xhr, status, error) {
              Toast.fire({
                  icon: 'error',
                  title: 'An error occurred, please try again'
              });
          }
      });
  });
})(jQuery);
</script>
@endpush