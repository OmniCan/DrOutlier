@extends('admin.layouts.app')
@section('panel')

<div class="row mb-none-30">
    <div class="col-lg-12 mb-30">
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.subscriptions.store')}}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="user_id" class="font-weight-bold">@lang('Select User') <span class="text-danger">*</span></label>
                                <select class="form-control select2-auto-tokenize" name="user_id" id="user_id" required>
                                    <option value="">@lang('Select User')</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->fullname }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="plan_id" class="font-weight-bold">@lang('Select Plan') <span class="text-danger">*</span></label>
                                <select class="form-control select2-basic" name="plan_id" id="plan_id" required>
                                    <option value="">@lang('Select Plan')</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}"
                                                data-price="{{ $plan->effective_price }}"
                                                data-duration="{{ $plan->duration_value }}"
                                                data-duration-type="{{ $plan->duration_type }}"
                                                {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }} - ₹{{ number_format($plan->effective_price, 2) }} ({{ $plan->duration_text }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="amount_paid" class="font-weight-bold">@lang('Amount Paid') (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount_paid" id="amount_paid" value="{{old('amount_paid')}}" class="form-control" placeholder="299.00" required>
                                <small class="text-muted">@lang('The amount paid by the user')</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="payment_method" class="font-weight-bold">@lang('Payment Method')</label>
                                <select class="form-control" name="payment_method" id="payment_method">
                                    <option value="manual" {{ old('payment_method') == 'manual' ? 'selected' : '' }}>@lang('Manual/Admin Created')</option>
                                    <option value="razorpay" {{ old('payment_method') == 'razorpay' ? 'selected' : '' }}>@lang('Razorpay')</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>@lang('Cash')</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>@lang('Bank Transfer')</option>
                                    <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>@lang('Other')</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="started_at" class="font-weight-bold">@lang('Start Date') <span class="text-danger">*</span></label>
                                <input type="date" name="started_at" id="started_at" value="{{old('started_at', date('Y-m-d'))}}" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="expires_at" class="font-weight-bold">@lang('Expiry Date') <span class="text-danger">*</span></label>
                                <input type="date" name="expires_at" id="expires_at" value="{{old('expires_at')}}" class="form-control" required>
                                <small class="text-muted">@lang('Will be auto-calculated based on plan duration')</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="razorpay_payment_id" class="font-weight-bold">@lang('Razorpay Payment ID')</label>
                                <input type="text" name="razorpay_payment_id" id="razorpay_payment_id" value="{{old('razorpay_payment_id')}}" class="form-control" placeholder="pay_xxxxxxxxxxxxx">
                                <small class="text-muted">@lang('Optional: If payment was made via Razorpay')</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="razorpay_subscription_id" class="font-weight-bold">@lang('Razorpay Subscription ID')</label>
                                <input type="text" name="razorpay_subscription_id" id="razorpay_subscription_id" value="{{old('razorpay_subscription_id')}}" class="form-control" placeholder="sub_xxxxxxxxxxxxx">
                                <small class="text-muted">@lang('Optional: If using Razorpay subscriptions')</small>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="notes" class="font-weight-bold">@lang('Notes')</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="@lang('Any additional notes about this subscription')">{{old('notes')}}</textarea>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="auto_activate" id="auto_activate" value="1" {{ old('auto_activate', true) ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="auto_activate">
                                        @lang('Activate Immediately')
                                    </label>
                                    <small class="d-block text-muted">@lang('User will get instant access to plan modules')</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn--primary btn-block btn-lg">
                                    @lang('Create Subscription')
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
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
    $(document).ready(function() {
        // Initialize Select2 for user dropdown with search
        $('#user_id').select2({
            placeholder: "@lang('Search and select user...')",
            allowClear: true,
            width: '100%'
        });

        // Initialize Select2 for plan dropdown
        $('#plan_id').select2({
            placeholder: "@lang('Select Plan')",
            allowClear: true,
            width: '100%'
        });

        // Auto-populate amount when plan is selected
        $('#plan_id').on('change', function() {
            let selectedOption = $(this).find(':selected');
            let price = selectedOption.data('price');
            let duration = selectedOption.data('duration');
            let durationType = selectedOption.data('duration-type');

            if (price) {
                $('#amount_paid').val(price);
            }

            // Auto-calculate expiry date
            if (duration && durationType) {
                let startDate = new Date($('#started_at').val() || new Date());
                let expiryDate = new Date(startDate);

                if (durationType === 'days') {
                    expiryDate.setDate(expiryDate.getDate() + parseInt(duration));
                } else if (durationType === 'months') {
                    expiryDate.setMonth(expiryDate.getMonth() + parseInt(duration));
                } else if (durationType === 'years') {
                    expiryDate.setFullYear(expiryDate.getFullYear() + parseInt(duration));
                }

                let formattedDate = expiryDate.toISOString().split('T')[0];
                $('#expires_at').val(formattedDate);
            }
        });

        // Recalculate expiry when start date changes
        $('#started_at').on('change', function() {
            $('#plan_id').trigger('change');
        });

        // Auto-calculate expiry on page load if plan is selected
        if ($('#plan_id').val()) {
            $('#plan_id').trigger('change');
        }
    });
</script>
@endpush
