@extends('admin.layouts.app')
@section('panel')

<div class="row mb-none-30">
    <div class="col-lg-12 mb-30">
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.basic.basic-store')}}" method="POST" >
                    @csrf
                    <div class="row">
                        
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="category" class="font-weight-bold ">@lang('Category')</label>
                                <select class="form-control" name="category">
                                    <option value="" selected >Select Category</option>
                                    @if(!empty($datalist))
                                    @foreach ($datalist as $cat) 
                                    <optgroup label="{{$cat->name}}"> 
                                        @foreach($cat->child as $child)
                                     <option value="{{$child->id}}">{{$child->name}}</option> 
                                    @endforeach
                                      </optgroup>
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
  
                        
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="content" class="font-weight-bold">@lang('Content')</label>
                                <textarea class="trumEdit" name="content"
                                placeholder="@lang('Content')" id="content" cols="40"
                                rows="10"></textarea>
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
<a href="{{route('admin.basic.basic-index')}}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="las la-angle-double-left"></i>@lang('Go Back')</a>
@endpush


@push('style')
<style>
    .ck-placeholder{
        height: 190px !important;
    }
</style>
@endpush




z