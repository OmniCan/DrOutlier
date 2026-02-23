@extends('admin.layouts.app')
@section('panel')

<div class="row mb-none-30">
    <div class="col-lg-12 mb-30">
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.watch-and-learn-category.update' , $category->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row"> 
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="name" class="font-weight-bold">@lang('Category Name')</label>
                                <input type="text" name="name" id="name"  class="form-control" value="{{$category->name}}" placeholder="@lang('Enter category name')" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group d-flex gap-2 mt-4">
                                
                                <input type="checkbox" name="sub" id="subcategory"/>
                                <label for="name" class="font-weight-bold">@lang('Click To Make Sub Category')</label>
                            </div>
                        </div>
                        <div class="col-lg-6 d-none catdropdown">
                            <div class="form-group">
                                <label for="name" class="font-weight-bold">@lang('Category Name')</label>
                               <select class="form-control" id="parent_id" name="parent_id">
                                <option selected value="0">Select Category</option>
                                @if(!empty($categories))
                                @foreach ($categories as $categorie)
                                    <option value="{{$categorie->id}}" {{($categorie->id == $category->parent_id)?'selected':''}}>{{$categorie->name}}</option>
                                @endforeach
                                @endif
                               </select>
                            </div>
                        </div>  
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="color" class="font-weight-bold">@lang('Color Code')</label>
                                
                                <div id="" class="input-group cp4">
                                    <input type="text" class="form-control col-xs-1 source_color width1 colorpicker-input-addon" name="color" value="{{$category->color}}" placeholder="Choose Color" />
                                    <span class="input-group-append"> 
                                        <span class="input-group-text colorpicker-input-addon" ><i></i></span>
                                    </span>
                                </div>
                            </div>
                        </div>
 

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="status" class="font-weight-bold">@lang('Status')</label>
                                <select class="form-control" name="status">
                                    <option value="" disabled>Select status</option>
                                    <option value="1" {{ $category->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $category->status == 0 ? 'selected' : '' }}>In-Active</option>
                                </select>
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
@php
$cat = $category->parent_id;  
@endphp
@endsection

@push('breadcrumb-plugins')
<a href="{{route('admin.watch-and-learn-category.index')}}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="las la-angle-double-left"></i>@lang('Go Back')</a>
@endpush


@push('style')
<style>
    .ck-placeholder{
        height: 190px !important;
    }
</style>
@endpush 
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/js/bootstrap-colorpicker.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/css/bootstrap-colorpicker.css" />
<script>
$(function() {
    $('.cp4').colorpicker();
});
 
var cat = @json($cat);

if (cat != 0) {
    $('.catdropdown').removeClass('d-none');
    $('#subcategory').prop('checked', true); 
    $('#parent_id').val(cat);
}

 

$(document).ready(function() { 
    $('#subcategory').change(function() {
        if ($(this).is(':checked')) {
            $('.catdropdown').removeClass('d-none');
            $('#parent_id').val(cat);
        } else {
            $('.catdropdown').addClass('d-none');
            $('#parent_id').val(0);
        }
    });
});
</script>
@endpush