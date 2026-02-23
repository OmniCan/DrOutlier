@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body">
                <h4 class="card-title">@lang('Update Category')</h4>
                <form action="{{ route('admin.quiz.category.update', $categories->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">@lang('Category Name')</label>
                                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $categories->name) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="image">@lang('Image')</label>
                                <input type="file" id="image" name="image" class="form-control">
                                @if($categories->image)
                                    <img src="{{ getImage(getFilePath('QuestionsImage').'/'.$categories->image) }}" alt="Category Image" width="100" height="100">
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">@lang('Status')</label>
                                <select name="status" class="form-control" required>
                                    <option value="1" {{ $categories->status == 1 ? 'selected' : '' }}>@lang('Active')</option>
                                    <option value="0" {{ $categories->status == 0 ? 'selected' : '' }}>@lang('Inactive')</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn--primary btn-block">@lang('Update Category')</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('breadcrumb-plugins')
<a href="{{route('admin.quiz.category.index')}}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="las la-angle-left"></i>@lang('Back to Category List')</a>
@endpush
