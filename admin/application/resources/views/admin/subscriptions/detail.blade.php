@extends('admin.layouts.app')

@section('panel')
<div class="row mb-none-30">
    <div class="col-xl-4 col-md-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <h5 class="mb-20 text-muted">@lang('User Information')</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Name')
                        <span class="fw-bold">{{ $subscription->user->fullname }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Email')
                        <span class="fw-bold">{{ $subscription->user->email }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Mobile')
                        <span class="fw-bold">{{ $subscription->user->mobile ?? 'N/A' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('User ID')
                        <span class="fw-bold">#{{ $subscription->user->id }}</span>
                    </li>
                </ul>
                <div class="mt-3">
                    <a href="{{ route('admin.users.detail', $subscription->user_id) }}" class="btn btn--primary btn-block">
                        @lang('View User Profile')
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <h5 class="mb-20 text-muted">@lang('Plan Information')</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Plan Name')
                        <span class="fw-bold">{{ $subscription->plan->name }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Duration')
                        <span class="fw-bold">{{ $subscription->plan->duration_text }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Plan Price')
                        <span class="fw-bold">₹{{ number_format($subscription->plan->effective_price, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Amount Paid')
                        <span class="fw-bold text-success">₹{{ number_format($subscription->amount_paid, 2) }}</span>
                    </li>
                </ul>
                <div class="mt-3">
                    <a href="{{ route('admin.plans.edit', $subscription->plan_id) }}" class="btn btn--primary btn-block">
                        @lang('View Plan Details')
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-12 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <h5 class="mb-20 text-muted">@lang('Subscription Status')</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Status')
                        @if($subscription->status == 'active')
                            <span class="badge badge--success">@lang('Active')</span>
                        @elseif($subscription->status == 'expired')
                            <span class="badge badge--danger">@lang('Expired')</span>
                        @elseif($subscription->status == 'cancelled')
                            <span class="badge badge--warning">@lang('Cancelled')</span>
                        @else
                            <span class="badge badge--dark">{{ ucfirst($subscription->status) }}</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Started At')
                        <span class="fw-bold">{{ $subscription->started_at ? $subscription->started_at->format('d M Y, h:i A') : 'N/A' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Expires At')
                        <span class="fw-bold">{{ $subscription->expires_at ? $subscription->expires_at->format('d M Y, h:i A') : 'N/A' }}</span>
                    </li>
                    @if($subscription->status == 'active')
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Days Remaining')
                            <span class="fw-bold text-primary">{{ $subscription->days_remaining }} days</span>
                        </li>
                    @endif
                </ul>
                <div class="mt-3">
                    @if($subscription->status == 'active')
                        <button class="btn btn--warning btn-block cancelBtn"
                                data-id="{{ $subscription->id }}"
                                data-action="{{ route('admin.subscriptions.cancel', $subscription->id) }}">
                            <i class="las la-ban"></i> @lang('Cancel Subscription')
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-12 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <h5 class="mb-20 text-muted">@lang('Payment Information')</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Razorpay Subscription ID')
                        <span class="fw-bold">{{ $subscription->razorpay_subscription_id ?? 'N/A' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Razorpay Payment ID')
                        <span class="fw-bold">{{ $subscription->razorpay_payment_id ?? 'N/A' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Created At')
                        <span class="fw-bold">{{ $subscription->created_at->format('d M Y, h:i A') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Last Updated')
                        <span class="fw-bold">{{ $subscription->updated_at->format('d M Y, h:i A') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-xl-12 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <h5 class="mb-20 text-muted">@lang('Accessible Modules')</h5>
                <div class="row">
                    @forelse($subscription->plan->modules as $module)
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg--primary">
                                <div class="card-body text-center">
                                    <i class="{{ $module->icon ?? 'las la-cube' }} text-white" style="font-size: 2rem;"></i>
                                    <h6 class="text-white mt-2 mb-0">{{ $module->display_name }}</h6>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted text-center">@lang('No modules assigned to this plan')</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @if($subscription->status == 'active' || $subscription->status == 'expired')
        <div class="col-xl-12 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Extend Subscription')</h5>
                    <form action="{{ route('admin.subscriptions.extend', $subscription->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Extend By (Days)')</label>
                                    <input type="number" name="days" class="form-control" placeholder="30" required>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>@lang('Reason')</label>
                                    <input type="text" name="reason" class="form-control" placeholder="@lang('e.g., Promotional extension')">
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn--success">
                                    <i class="las la-calendar-plus"></i> @lang('Extend Subscription')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Cancel Confirmation Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">@lang('Cancel Subscription')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="cancelForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>@lang('Are you sure you want to cancel this subscription?')</p>
                    <div class="form-group">
                        <label>@lang('Reason for cancellation')</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="@lang('Optional')"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--warning">@lang('Yes, Cancel')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('breadcrumb-plugins')
<a href="{{route('admin.subscriptions.index')}}" class="btn btn-sm btn--primary box--shadow1 text--small">
    <i class="las la-angle-double-left"></i>@lang('Go Back')
</a>
@endpush

@push('script')
<script>
    $(document).on('click', '.cancelBtn', function() {
        let action = $(this).data('action');
        $('#cancelForm').attr('action', action);
        $('#cancelModal').modal('show');
    });
</script>
@endpush
