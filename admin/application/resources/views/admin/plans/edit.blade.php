@extends('admin.layouts.app')
@section('panel')

<div class="row mb-none-30">
    <div class="col-lg-12 mb-30">
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.plans.update', $plan->id)}}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="name" class="font-weight-bold">@lang('Plan Name') <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{old('name', $plan->name)}}" class="form-control" placeholder="@lang('Enter plan name')" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="slug" class="font-weight-bold">@lang('Slug') <span class="text-danger">*</span></label>
                                <input type="text" name="slug" id="slug" value="{{old('slug', $plan->slug)}}" class="form-control" placeholder="@lang('e.g., spotters-premium')" required>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="description" class="font-weight-bold">@lang('Description')</label>
                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="@lang('Enter plan description')">{{old('description', $plan->description)}}</textarea>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="price" class="font-weight-bold">@lang('Price') (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="price" id="price" value="{{old('price', $plan->price)}}" class="form-control" placeholder="@lang('299')" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="discount_price" class="font-weight-bold">@lang('Discount Price') (₹)</label>
                                <input type="number" step="0.01" name="discount_price" id="discount_price" value="{{old('discount_price', $plan->discount_price)}}" class="form-control" placeholder="@lang('199 (optional)')">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="duration_value" class="font-weight-bold">@lang('Duration Value') <span class="text-danger">*</span></label>
                                <input type="number" name="duration_value" id="duration_value" value="{{old('duration_value', $plan->duration_value)}}" class="form-control" placeholder="@lang('1')" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="duration_type" class="font-weight-bold">@lang('Duration Type') <span class="text-danger">*</span></label>
                                <select class="form-control" name="duration_type" required>
                                    <option value="days" {{ old('duration_type', $plan->duration_type) == 'days' ? 'selected' : '' }}>@lang('Days')</option>
                                    <option value="months" {{ old('duration_type', $plan->duration_type) == 'months' ? 'selected' : '' }}>@lang('Months')</option>
                                    <option value="years" {{ old('duration_type', $plan->duration_type) == 'years' ? 'selected' : '' }}>@lang('Years')</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="sort_order" class="font-weight-bold">@lang('Sort Order')</label>
                                <input type="number" name="sort_order" id="sort_order" value="{{old('sort_order', $plan->sort_order)}}" class="form-control" placeholder="@lang('0')">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="font-weight-bold">@lang('Select Modules') <span class="text-danger">*</span></label>
                                <div class="row">
                                    @php
                                        $selectedModules = old('modules', $plan->modules->pluck('id')->toArray());
                                    @endphp
                                    @foreach($modules as $module)
                                        <div class="col-lg-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="modules[]" value="{{ $module->id }}" id="module_{{ $module->id }}" {{ in_array($module->id, $selectedModules) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="module_{{ $module->id }}">
                                                    <i class="{{ $module->icon ?? 'fas fa-circle' }}"></i> {{ $module->display_name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="font-weight-bold">@lang('Features') (Optional)</label>
                                <div id="features-container">
                                    @php
                                        $features = old('features', $plan->features ?? []);
                                    @endphp
                                    @if(!empty($features))
                                        @foreach($features as $feature)
                                            <div class="input-group mb-2">
                                                <input type="text" name="features[]" class="form-control" placeholder="@lang('Enter feature')" value="{{ $feature }}">
                                                <button type="button" class="btn btn-danger btn-sm remove-feature">
                                                    <i class="las la-minus"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="input-group mb-2">
                                            <input type="text" name="features[]" class="form-control" placeholder="@lang('e.g., Access to all videos')">
                                            <button type="button" class="btn btn-success btn-sm add-feature">
                                                <i class="las la-plus"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm btn-success add-feature mt-2">
                                    <i class="las la-plus"></i> @lang('Add Feature')
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="is_active">
                                        @lang('Active')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $plan->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="is_featured">
                                        @lang('Featured')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn--primary btn-block btn-lg">
                                    @lang('Update Plan')
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
<a href="{{route('admin.plans.index')}}" class="btn btn-sm btn--primary box--shadow1 text--small">
    <i class="las la-angle-double-left"></i>@lang('Go Back')
</a>
@endpush

@push('script')
<script>
    // Add more feature fields
    $(document).on('click', '.add-feature', function() {
        let html = `
            <div class="input-group mb-2">
                <input type="text" name="features[]" class="form-control" placeholder="@lang('Enter feature')">
                <button type="button" class="btn btn-danger btn-sm remove-feature">
                    <i class="las la-minus"></i>
                </button>
            </div>
        `;
        $('#features-container').append(html);
    });

    $(document).on('click', '.remove-feature', function() {
        $(this).closest('.input-group').remove();
    });
</script>
@endpush
