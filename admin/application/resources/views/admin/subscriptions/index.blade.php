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
                                <th>@lang('User')</th>
                                <th>@lang('Plan')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Started')</th>
                                <th>@lang('Expires')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriptions as $subscription)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $subscription->user->firstname }} {{ $subscription->user->lastname }}</span>
                                        <br>
                                        <small class="text-muted">{{ $subscription->user->email }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $subscription->plan->name }}</span>
                                        <br>
                                        <small class="text-muted">{{ $subscription->plan->duration_text }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold">₹{{ number_format($subscription->amount_paid, 2) }}</span>
                                    </td>
                                    <td>
                                        @if($subscription->status == 'active')
                                            <span class="badge badge--success">@lang('Active')</span>
                                        @elseif($subscription->status == 'expired')
                                            <span class="badge badge--danger">@lang('Expired')</span>
                                        @elseif($subscription->status == 'cancelled')
                                            <span class="badge badge--warning">@lang('Cancelled')</span>
                                        @else
                                            <span class="badge badge--dark">{{ ucfirst($subscription->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $subscription->started_at ? $subscription->started_at->format('d M Y') : '-' }}
                                    </td>
                                    <td>
                                        @if($subscription->expires_at)
                                            {{ $subscription->expires_at->format('d M Y') }}
                                            @if($subscription->status == 'active')
                                                <br>
                                                <small class="text-muted">{{ $subscription->days_remaining }} days left</small>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.subscriptions.detail', $subscription->id) }}"
                                           class="btn btn-sm btn-outline--primary">
                                            <i class="la la-eye"></i> @lang('View')
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">@lang('No subscriptions found')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($subscriptions->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($subscriptions) }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Subscription Stats -->
<div class="row mt-4">
    <div class="col-lg-3 col-sm-6 mb-30">
        <div class="card b-radius--10 bg--primary overflow-hidden box--shadow1">
            <div class="card-body">
                <div class="widget-card">
                    <div class="widget-card__icon">
                        <i class="las la-check-circle"></i>
                    </div>
                    <div class="widget-card__content">
                        <h4 class="text-white">{{ $activeCount }}</h4>
                        <span class="text-white">@lang('Active Subscriptions')</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-30">
        <div class="card b-radius--10 bg--danger overflow-hidden box--shadow1">
            <div class="card-body">
                <div class="widget-card">
                    <div class="widget-card__icon">
                        <i class="las la-times-circle"></i>
                    </div>
                    <div class="widget-card__content">
                        <h4 class="text-white">{{ $expiredCount }}</h4>
                        <span class="text-white">@lang('Expired Subscriptions')</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-30">
        <div class="card b-radius--10 bg--warning overflow-hidden box--shadow1">
            <div class="card-body">
                <div class="widget-card">
                    <div class="widget-card__icon">
                        <i class="las la-ban"></i>
                    </div>
                    <div class="widget-card__content">
                        <h4 class="text-white">{{ $cancelledCount }}</h4>
                        <span class="text-white">@lang('Cancelled Subscriptions')</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-30">
        <div class="card b-radius--10 bg--success overflow-hidden box--shadow1">
            <div class="card-body">
                <div class="widget-card">
                    <div class="widget-card__icon">
                        <i class="las la-rupee-sign"></i>
                    </div>
                    <div class="widget-card__content">
                        <h4 class="text-white">₹{{ number_format($totalRevenue, 2) }}</h4>
                        <span class="text-white">@lang('Total Revenue')</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2">
        <a href="{{ route('admin.subscriptions.active') }}" class="btn btn-sm btn--success">
            <i class="las la-check"></i>@lang('Active')
        </a>
        <a href="{{ route('admin.subscriptions.expired') }}" class="btn btn-sm btn--danger">
            <i class="las la-times"></i>@lang('Expired')
        </a>
        <a href="{{ route('admin.subscriptions.cancelled') }}" class="btn btn-sm btn--warning">
            <i class="las la-ban"></i>@lang('Cancelled')
        </a>
        <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-sm btn--primary">
            <i class="las la-plus"></i>@lang('Add Subscription')
        </a>
    </div>
@endpush
