@extends('admin.layouts.app')
@section('panel')

<div class="row mb-none-30">
    <div class="col-lg-12 mb-30">
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.modules.store')}}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="name" class="font-weight-bold">@lang('Module Name') <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{old('name')}}" class="form-control" placeholder="@lang('e.g., notes')" required>
                                <small class="text-muted">@lang('Internal identifier (lowercase, no spaces)')</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="display_name" class="font-weight-bold">@lang('Display Name') <span class="text-danger">*</span></label>
                                <input type="text" name="display_name" id="display_name" value="{{old('display_name')}}" class="form-control" placeholder="@lang('e.g., Notes')" required>
                                <small class="text-muted">@lang('Name shown to users')</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="slug" class="font-weight-bold">@lang('Slug') <span class="text-danger">*</span></label>
                                <input type="text" name="slug" id="slug" value="{{old('slug')}}" class="form-control" placeholder="@lang('e.g., notes')" required>
                                <small class="text-muted">@lang('Must match API route middleware identifier')</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="icon" class="font-weight-bold">@lang('Icon Class')</label>
                                <input type="text" name="icon" id="icon" value="{{old('icon')}}" class="form-control" placeholder="@lang('e.g., las la-sticky-note')">
                                <small class="text-muted">@lang('Line Awesome or Font Awesome class')</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="frontend_url" class="font-weight-bold">@lang('Frontend URL')</label>
                                <input type="text" name="frontend_url" id="frontend_url" value="{{old('frontend_url')}}" class="form-control" placeholder="@lang('/notes')">
                                <small class="text-muted">@lang('Frontend route path')</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="admin_url" class="font-weight-bold">@lang('Admin URL')</label>
                                <input type="text" name="admin_url" id="admin_url" value="{{old('admin_url')}}" class="form-control" placeholder="@lang('/admin/category/index')">
                                <small class="text-muted">@lang('Admin panel route path')</small>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="description" class="font-weight-bold">@lang('Description')</label>
                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="@lang('Enter module description')">{{old('description')}}</textarea>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="sort_order" class="font-weight-bold">@lang('Sort Order')</label>
                                <input type="number" name="sort_order" id="sort_order" value="{{old('sort_order', 0)}}" class="form-control" placeholder="@lang('0')">
                                <small class="text-muted">@lang('Display order (lower numbers first)')</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="is_active">
                                        @lang('Active')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">@lang('Important Notes:')</h6>
                                <ul class="mb-0">
                                    <li>@lang('The slug must exactly match the module identifier used in API routes (e.g., module.access:notes)')</li>
                                    <li>@lang('After creating a module, update the corresponding API routes in routes/api.php to use the middleware')</li>
                                    <li>@lang('Example: Route::middleware(\'module.access:your-slug\')->group(function() { ... });')</li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn--primary btn-block btn-lg">
                                    @lang('Create Module')
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
<a href="{{route('admin.modules.index')}}" class="btn btn-sm btn--primary box--shadow1 text--small">
    <i class="las la-angle-double-left"></i>@lang('Go Back')
</a>
@endpush

@push('script')
<script>
    // Auto-generate slug from name
    $('#name').on('keyup', function() {
        let name = $(this).val();
        let slug = name.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        $('#slug').val(slug);
    });
</script>
@endpush
