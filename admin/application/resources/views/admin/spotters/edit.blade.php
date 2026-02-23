@extends('admin.layouts.app')
@section('panel')

<div class="row mb-none-30">
    <div class="col-lg-12 mb-30">
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.spotters.spotters-update' , $spotters->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="category" class="font-weight-bold">@lang('Category')</label>
                                <select class="form-control" name="category" required>
                                    <option value="" disabled>Select Chapter</option>
                                    @if(!empty($parentCategories))
                                        @foreach ($parentCategories as $parent)
                                            {{-- Parent Category (disabled, not selectable) --}}
                                            <option value="" disabled style="font-weight: bold; color: #333;">{{ $parent->name }}</option>

                                            {{-- Child Chapters (selectable) --}}
                                            @foreach ($category as $cate)
                                                @if($cate->parent_id == $parent->id)
                                                    <option value="{{$cate->id}}" {{($spotters->category == $cate->id)?'selected':''}} style="padding-left: 20px;">
                                                        &nbsp;&nbsp;&nbsp;&nbsp;{{ $cate->name }}
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
                                <input type="text" name="title" id="title" value="{{$spotters->title}}" class="form-control" placeholder="@lang('Enter Title')" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="sort_order" class="font-weight-bold">@lang('Sort Order')</label>
                                <input type="text" name="sort_order" id="sort_order" value="{{$spotters->sort_order}}" class="form-control" placeholder="@lang('Enter sort order')" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="image" class="font-weight-bold">@lang('Image') </label>
                                <div class="file-upload-wrapper" data-text="Select image!">
                                    <input type="file" name="image" id="image" class="file-upload-field">
                                </div>
                                <img src="{{getImage(getFilePath('SpottersImage').'/'.@$spotters->image)}}" width="100" height="100"/>
                            </div>
                        </div>


                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="content" class="font-weight-bold">@lang('Content')</label>
                                <textarea class="trumEdit" name="content"
                                placeholder="@lang('Content')" id="content" cols="40"
                                rows="10">{{$spotters->content}}</textarea>
                            </div>
                        </div>

                        <div class="col-lg-12 ">
                            <div class="form-group float-end">
                                <button type="submit" class="btn btn--primary btn-block btn-lg"> @lang('Update')</button>
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
<a href="{{route('admin.spotters.spotters-index')}}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="las la-angle-double-left"></i>@lang('Go Back')</a>
@endpush


@push('style')
<style>
    .ck-placeholder{
        height: 190px !important;
    }
</style>
@endpush




