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
                                <th>@lang('Plan Name')</th>
                                <th>@lang('Price')</th>
                                <th>@lang('Duration')</th>
                                <th>@lang('Modules')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Featured')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($plans as $plan)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $plan->name }}</span>
                                    </td>
                                    <td>
                                        @if($plan->discount_price)
                                            <span class="text-muted" style="text-decoration: line-through;">₹{{ $plan->price }}</span>
                                            <br>
                                            <span class="fw-bold text-success">₹{{ $plan->discount_price }}</span>
                                        @else
                                            <span class="fw-bold">₹{{ $plan->price }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $plan->duration_value }} {{ ucfirst($plan->duration_type) }}</td>
                                    <td>
                                        <span class="badge badge--primary">{{ $plan->modules->count() }} Modules</span>
                                    </td>
                                    <td>
                                        @if($plan->is_active)
                                            <span class="badge badge--success">@lang('Active')</span>
                                        @else
                                            <span class="badge badge--danger">@lang('Inactive')</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($plan->is_featured)
                                            <span class="badge badge--warning">@lang('Featured')</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.plans.edit', $plan->id) }}"
                                           class="btn btn-sm btn-outline--primary">
                                            <i class="la la-pen"></i> @lang('Edit')
                                        </a>

                                        <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                data-question="@lang('Are you sure you want to delete this plan?')"
                                                data-action="{{ route('admin.plans.delete', $plan->id) }}">
                                            <i class="la la-trash"></i> @lang('Delete')
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">@lang('No plans found')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($plans->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($plans) }}
                </div>
            @endif
        </div>
    </div>
</div>

<x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.plans.create') }}" class="btn btn-sm btn--primary">
        <i class="las la-plus"></i>@lang('Add New Plan')
    </a>
@endpush
