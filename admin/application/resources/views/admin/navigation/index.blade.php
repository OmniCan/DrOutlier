@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two" id="sortable-table">
                        <thead>
                            <tr>
                                <th>@lang('Order')</th>
                                <th>@lang('Title')</th>
                                <th>@lang('URL')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Visibility')</th>
                                <th>@lang('Module')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-body">
                            @forelse($navigationItems as $item)
                                <tr data-id="{{ $item->id }}" data-order="{{ $item->sort_order }}">
                                    <td>
                                        <i class="las la-arrows-alt drag-handle" style="cursor: move; font-size: 20px;"></i>
                                        <span class="badge badge--dark">{{ $item->sort_order }}</span>
                                    </td>
                                    <td>
                                        @if($item->icon)
                                            <i class="{{ $item->icon }}"></i>
                                        @endif
                                        <span class="fw-bold">{{ $item->title }}</span>
                                    </td>
                                    <td>
                                        <code>{{ $item->url }}</code>
                                    </td>
                                    <td>
                                        @if($item->type == 'module')
                                            <span class="badge badge--primary">@lang('Module')</span>
                                        @elseif($item->type == 'custom')
                                            <span class="badge badge--info">@lang('Custom')</span>
                                        @else
                                            <span class="badge badge--warning">@lang('External')</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(($item->visibility_type ?? 'public') == 'public')
                                            <span class="badge badge--success">@lang('Public')</span>
                                        @elseif($item->visibility_type == 'subscription')
                                            <span class="badge badge--primary">@lang('Subscription')</span>
                                        @else
                                            <span class="badge badge--info">@lang('Auth Only')</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->module)
                                            <span class="badge badge--success">{{ $item->module->display_name }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->is_active)
                                            <span class="badge badge--success">@lang('Active')</span>
                                        @else
                                            <span class="badge badge--danger">@lang('Inactive')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.navigation.edit', $item->id) }}"
                                           class="btn btn-sm btn-outline--primary">
                                            <i class="la la-pen"></i> @lang('Edit')
                                        </a>

                                        <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                data-question="@lang('Are you sure you want to delete this item?')"
                                                data-action="{{ route('admin.navigation.delete', $item->id) }}">
                                            <i class="la la-trash"></i> @lang('Delete')
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">@lang('No navigation items found')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($navigationItems->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($navigationItems) }}
                </div>
            @endif
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card b-radius--10">
            <div class="card-body">
                <h5 class="card-title mb-3">@lang('Navigation Manager Information')</h5>
                <p class="text-muted">
                    @lang('Control which items appear in the frontend navigation bar. Items are displayed in the order specified.')
                </p>
                <ul class="text-muted">
                    <li><strong>@lang('Public'):</strong> @lang('Show to everyone (logged in or not)')</li>
                    <li><strong>@lang('Subscription Based'):</strong> @lang('Show only to users who have an active subscription that includes the linked module')</li>
                    <li><strong>@lang('Auth Only'):</strong> @lang('Show only to logged in users (regardless of subscription)')</li>
                    <li><strong>@lang('Drag and drop'):</strong> @lang('rows to reorder navigation items')</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.navigation.create') }}" class="btn btn-sm btn--primary">
        <i class="las la-plus"></i>@lang('Add Navigation Item')
    </a>
@endpush

@push('script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    // Initialize Sortable
    var el = document.getElementById('sortable-body');
    if (el) {
        var sortable = Sortable.create(el, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function (evt) {
                updateOrder();
            }
        });
    }

    function updateOrder() {
        let items = [];
        $('#sortable-body tr').each(function(index) {
            items.push({
                id: $(this).data('id'),
                sort_order: index
            });
        });

        $.ajax({
            url: "{{ route('admin.navigation.update-order') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                items: items
            },
            success: function(response) {
                notify('success', 'Navigation order updated successfully');
            },
            error: function(xhr) {
                notify('error', 'Failed to update order');
            }
        });
    }
</script>
@endpush
