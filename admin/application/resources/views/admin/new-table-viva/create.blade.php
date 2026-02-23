@extends('admin.layouts.app')
@section('panel')

<div class="row mb-none-30">
    <div class="col-lg-12 mb-30">
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.new-table-viva.new-table-viva-store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="category" class="font-weight-bold">@lang('Chapter')</label>
                                <select class="form-control" name="category" required>
                                    <option value="" selected disabled>Select Chapter</option>
                                    @if(!empty($parentCategories))
                                        @foreach ($parentCategories as $parent)
                                            {{-- Parent Category (disabled, not selectable) --}}
                                            <option value="" disabled style="font-weight: bold; color: #333;">{{ $parent->name }}</option>

                                            {{-- Child Chapters (selectable) --}}
                                            @foreach ($category as $cat)
                                                @if($cat->parent_id == $parent->id)
                                                    <option value="{{$cat->id}}" style="padding-left: 20px;">
                                                        &nbsp;&nbsp;&nbsp;&nbsp;{{ $cat->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="title" class="font-weight-bold">@lang('Title')</label>
                                <input type="text" name="title" id="title" value="{{old('title')}}" class="form-control" placeholder="@lang('Enter Title')" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="sort_order" class="font-weight-bold">@lang('Sort Order')</label>
                                <input type="number" name="sort_order" id="sort_order" value="{{old('sort_order')}}" class="form-control" placeholder="@lang('Enter sort order')" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="image" class="font-weight-bold">@lang('Image') </label>
                                <div class="file-upload-wrapper" data-text="Select image!">
                                    <input type="file" name="image" id="image" class="file-upload-field" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="description" class="font-weight-bold">@lang('Description') <span class="text-muted">(Optional)</span></label>
                                <textarea class="ckeditor form-control" name="description">{{old('description')}}</textarea>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="pdf_file" class="font-weight-bold">@lang('PDF File') <span class="text-muted">(Optional)</span></label>
                                <div class="file-upload-wrapper" data-text="Select PDF file!">
                                    <input type="file" name="pdf_file" id="pdf_file" class="file-upload-field" accept=".pdf">
                                </div>
                                <small class="text-muted">Maximum file size: 10MB</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="is_premium" class="font-weight-bold">@lang('Type')</label>
                                <select class="form-control" name="is_premium">
                                    <option value="0" selected>Free</option>
                                    <option value="1">Premium</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-12 ">
                            <div class="form-group float-end">
                                <button type="submit" class="btn btn--primary btn-block btn-lg"> @lang('Create')</button>
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
<a href="{{route('admin.new-table-viva.new-table-viva-index')}}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="las la-angle-double-left"></i>@lang('Go Back')</a>
@endpush


@push('style')
<style>
    .ck-placeholder{
        height: 190px !important;
    }
</style>
@endpush
