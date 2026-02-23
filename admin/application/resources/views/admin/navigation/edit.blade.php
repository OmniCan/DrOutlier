@extends('admin.layouts.app')
@section('panel')

<div class="row mb-none-30">
    <div class="col-lg-12 mb-30">
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.navigation.update', $navigationItem->id)}}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="title" class="font-weight-bold">@lang('Title') <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" value="{{old('title', $navigationItem->title)}}" class="form-control" placeholder="@lang('e.g., Notes')" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="type" class="font-weight-bold">@lang('Type') <span class="text-danger">*</span></label>
                                <select class="form-control" name="type" id="type" required>
                                    <option value="module" {{ old('type', $navigationItem->type) == 'module' ? 'selected' : '' }}>@lang('Module (Linked to Module)')</option>
                                    <option value="custom" {{ old('type', $navigationItem->type) == 'custom' ? 'selected' : '' }}>@lang('Custom (Internal Link)')</option>
                                    <option value="external" {{ old('type', $navigationItem->type) == 'external' ? 'selected' : '' }}>@lang('External (External Link)')</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="visibility_type" class="font-weight-bold">@lang('Visibility') <span class="text-danger">*</span></label>
                                <select class="form-control" name="visibility_type" id="visibility_type" required>
                                    <option value="public" {{ old('visibility_type', $navigationItem->visibility_type ?? 'public') == 'public' ? 'selected' : '' }}>@lang('Public (Show to Everyone)')</option>
                                    <option value="subscription" {{ old('visibility_type', $navigationItem->visibility_type) == 'subscription' ? 'selected' : '' }}>@lang('Subscription Based (Show Based on Plan Access)')</option>
                                    <option value="auth" {{ old('visibility_type', $navigationItem->visibility_type) == 'auth' ? 'selected' : '' }}>@lang('Authenticated Only (Logged in Users)')</option>
                                </select>
                                <small class="text-muted">@lang('Control who can see this menu item')</small>
                            </div>
                        </div>

                        <div class="col-lg-6" id="module-select">
                            <div class="form-group">
                                <label for="module_id" class="font-weight-bold">@lang('Select Module')</label>
                                <select class="form-control" name="module_id" id="module_id">
                                    <option value="">@lang('None')</option>
                                    @foreach($modules as $module)
                                        <option value="{{ $module->id }}" data-url="{{ $module->frontend_url }}" {{ old('module_id', $navigationItem->module_id) == $module->id ? 'selected' : '' }}>
                                            {{ $module->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">@lang('Select module for automatic access control')</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="url" class="font-weight-bold">@lang('URL') <span class="text-danger">*</span></label>
                                <input type="text" name="url" id="url" value="{{old('url', $navigationItem->url)}}" class="form-control" placeholder="@lang('/notes')" required>
                                <small class="text-muted">@lang('For module type: /notes, For external: https://example.com')</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="icon" class="font-weight-bold">@lang('Icon Class')</label>
                                <input type="text" name="icon" id="icon" value="{{old('icon', $navigationItem->icon)}}" class="form-control" placeholder="@lang('las la-sticky-note')">
                                <small class="text-muted">@lang('Line Awesome or Font Awesome icon class')</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="sort_order" class="font-weight-bold">@lang('Sort Order')</label>
                                <input type="number" name="sort_order" id="sort_order" value="{{old('sort_order', $navigationItem->sort_order)}}" class="form-control" placeholder="@lang('0')">
                                <small class="text-muted">@lang('Lower numbers appear first')</small>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $navigationItem->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="is_active">
                                        @lang('Active')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="show_in_navbar" id="show_in_navbar" value="1" {{ old('show_in_navbar', $navigationItem->show_in_navbar) ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="show_in_navbar">
                                        @lang('Show in Navbar')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="requires_auth" id="requires_auth" value="1" {{ old('requires_auth', $navigationItem->requires_auth) ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="requires_auth">
                                        @lang('Requires Login')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn--primary btn-block btn-lg">
                                    @lang('Update Navigation Item')
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
<a href="{{route('admin.navigation.index')}}" class="btn btn-sm btn--primary box--shadow1 text--small">
    <i class="las la-angle-double-left"></i>@lang('Go Back')
</a>
@endpush

@push('script')
<script>
    // Auto-fill URL when module is selected
    $('#module_id').on('change', function() {
        let url = $(this).find(':selected').data('url');
        if (url) {
            $('#url').val(url);
        }
    });

    // Toggle module select based on type
    $('#type').on('change', function() {
        if ($(this).val() === 'module') {
            $('#module-select').show();
        } else {
            $('#module-select').hide();
            $('#module_id').val('');
        }
    }).trigger('change');
</script>
@endpush
