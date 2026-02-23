@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body">

            
                <h4 class="card-title">@lang('Add New Quiz')</h4>
                <form action="{{ route('admin.quiz.quiz.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="category_id" class="font-weight-bold">@lang('Categories')</label>
                                <select name="category_id" id="category_id" class="form-control" required>
                                    <option value="" selected disabled>@lang('Select Category')</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">@lang('Quiz Name')</label>
                                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="image">@lang('Image')</label>
                                <input type="file" id="image" name="image" class="form-control">
                            </div>
                        </div>

                    
                    </div>

                    <button type="submit" class="btn btn--primary btn-block">@lang('Save Quiz')</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('breadcrumb-plugins')
<a href="{{route('admin.quiz.quiz.index')}}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="las la-angle-left"></i>@lang('Back to Quiz List')</a>
@endpush
