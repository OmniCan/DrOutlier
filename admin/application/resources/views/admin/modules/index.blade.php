@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Module Name')</th>
                                <th>@lang('Slug')</th>
                                <th>@lang('Frontend URL')</th>
                                <th>@lang('Admin URL')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($modules as $module)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $module->display_name }}</span>
                                        @if($module->icon)
                                            <i class="{{ $module->icon }} ml-2"></i>
                                        @endif
                                    </td>
                                    <td><code>{{ $module->slug }}</code></td>
                                    <td>{{ $module->frontend_url ?? 'N/A' }}</td>
                                    <td>{{ $module->admin_url ?? 'N/A' }}</td>
                                    <td>
                                        @if($module->is_active)
                                            <span class="badge badge--success">@lang('Active')</span>
                                        @else
                                            <span class="badge badge--danger">@lang('Inactive')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.modules.edit', $module->id) }}"
                                           class="btn btn-sm btn-outline--primary">
                                            <i class="la la-pen"></i> @lang('Edit')
                                        </a>

                                        <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                data-question="@lang('Are you sure you want to delete this module?')"
                                                data-action="{{ route('admin.modules.delete', $module->id) }}">
                                            <i class="la la-trash"></i> @lang('Delete')
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">@lang('No modules found')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($modules->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($modules) }}
                </div>
            @endif
        </div>
    </div>
</div>

<x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.modules.create') }}" class="btn btn-sm btn--primary">
        <i class="las la-plus"></i>@lang('Add New Module')
    </a>
@endpush
