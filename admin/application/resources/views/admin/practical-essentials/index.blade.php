@extends('admin.layouts.app')

@section('panel')
<div class="row">

    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body">
                 <div class="d-flex flex-wrap justify-content-end mb-3">
                    <form action="" method="GET" class="form-inline">
                        <div class="input-group justify-content-end">
                            <input type="text" name="search" class="form-control bg--white" placeholder="@lang('Search by category or title')" value="{{ request()->search }}">
                            <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two ">
                        <thead>
                            <tr>
                                <th>@lang('Image')</th>
                                <th>@lang('Category')</th>
                                <th>@lang('Title')</th>
                                <th>@lang('PDF')</th>
                                <th>@lang('Sort Order')</th>
                                <th>@lang('Premium')</th>
                                <th>@lang('Action')</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse($practicalessentialslist as $practicalEssentials)
                                <tr>
                                    <td>
                                        <img src="{{getImage(getFilePath('PracticalEssentialsImage').'/'.@$practicalEssentials->image)}}" height="50" width="50"/>
                                    </td>
                                    <td>
                                        @php
                                            $category = $practicalEssentials->categories;
                                            if($category) {
                                                if($category->parent_id != 0) {
                                                    // This is a chapter, show Parent > Chapter
                                                    $parentCategory = \App\Models\PracticalEssentialsCategory::find($category->parent_id);
                                                    echo ($parentCategory ? $parentCategory->name . ' > ' : '') . $category->name;
                                                } else {
                                                    // This is a parent category
                                                    echo $category->name;
                                                }
                                            }
                                        @endphp
                                    </td>
                                    <td>{{ Str::limit($practicalEssentials->title, 40) }}</td>
                                    <td>
                                        @if($practicalEssentials->pdf_file)
                                            <a href="{{getImage(getFilePath('PracticalEssentialsPDF').'/'.@$practicalEssentials->pdf_file)}}" target="_blank" class="btn btn-sm btn--success">
                                                <i class="las la-file-pdf"></i> View PDF
                                            </a>
                                        @else
                                            <span class="badge badge--warning">No PDF</span>
                                        @endif
                                    </td>
                                    <td>
                                        <input type="number" class="form-control col-xs-1 sort_order width1 text-center"
                                            data-id="{{ $practicalEssentials->id }}" placeholder=""
                                            value="{{ $practicalEssentials->sort_order ?? '' }}" style="width:60px;">
                                    </td>
                                    <td>
                                        <span class="badge badge--{{ $practicalEssentials->is_premium == 1 ? 'warning' : 'success' }} premium-badge" data-id="{{$practicalEssentials->id}}" style="cursor: pointer;" title="Click to toggle">
                                            {{ $practicalEssentials->is_premium == 1 ? 'Premium' : 'Free' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a title="@lang('Edit')" href="{{ route('admin.practical-essentials.practical-essentials-edit', $practicalEssentials->id)}}" class="btn btn-sm btn--primary" > <i class="la la-pen"></i></a>

                                        <button class="btn btn-outline--danger btn-sm confirmationBtn" data-question="@lang('Are you sure that you want to delete?')" data-action="{{ route('admin.practical-essentials.practical-essentials-delete', $practicalEssentials->id)}}" title="Delete"><i class="las la-trash"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No Record Found !</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>

            <div class="card-footer py-4">
                {{ paginateLinks($practicalessentialslist) }}
            </div>

        </div><!-- card end -->
    </div>
</div>


<x-confirmation-modal></x-confirmation-modal>

@endsection


@push('breadcrumb-plugins')
<a href="{{route('admin.practical-essentials.practical-essentials-create')}}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="las la-angle-double-left"></i>@lang('Add Practical Essentials')</a>
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
          url: "{{ route('admin.practical-essentials.update-sort-order') }}",
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

  // Premium toggle functionality
  $('.premium-badge').on('click', function() {
      var badge = $(this);
      var id = badge.data('id');

      $.ajax({
          url: "{{ route('admin.practical-essentials.toggle-premium', '') }}/" + id,
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
})(jQuery);
</script>
@endpush
