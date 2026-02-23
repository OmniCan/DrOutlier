@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.osce.osce-update', $osces->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row container">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="category" class="font-weight-bold">@lang('Select Chapter')</label>
                                    <select name="category" id="category" class="form-control" required>
                                        <option value="">@lang('Select Chapter')</option>
                                        @foreach ($parentCategories as $parent)
                                            <option value="" disabled style="font-weight:bold">{{ $parent->name }}</option>
                                            @foreach ($category as $cat)
                                                @if($cat->parent_id == $parent->id)
                                                    <option value="{{$cat->id}}" {{ $osces->category == $cat->id ? 'selected' : '' }} style="padding-left:20px">
                                                        &nbsp;&nbsp;&nbsp;&nbsp;{{ $cat->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="title" class="font-weight-bold">@lang('Title')</label>
                                    <input type="text" name="title" id="title" value="{{ $osces->title }}"
                                        class="form-control" placeholder="@lang('Enter Title')" required>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="image" class="font-weight-bold">@lang('Image')</label>
                                    <div class="file-upload-wrapper" data-text="Select image!">
                                        <input type="file" name="image" id="image" class="file-upload-field">
                                    </div>
                                    <img src="{{ getImage(getFilePath('OsceImage') . '/' . @$osces->image) }}" width="100"
                                        height="100" />
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="content" class="font-weight-bold">@lang('Content')</label>
                                    <textarea class="trumEdit" name="content" placeholder="@lang('Content')" id="content" cols="40" rows="10">{{ $osces->content }}</textarea>
                                </div>
                            </div>
                            <!-- Initial set of fields -->
                            <div id="fields-container">
                                @foreach ($osces->question as $k => $question)
                                    <div class="row field-set" id="">

                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label for="question-1" class="font-weight-bold">@lang('Question')</label>
                                                <input type="text" name="question[]" id="question-1"
                                                    value="{{ $question->question }}" class="form-control"
                                                    placeholder="@lang('Enter question')" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label for="answer-1" class="font-weight-bold">@lang('Answer')</label>
                                                <input type="text" name="answer[]" id="answer-1"
                                                    value="{{ $question->answer }}" class="form-control"
                                                    placeholder="@lang('Enter answer')" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group mb-0 mt-lg-4">
                                                @if ($k == 0)
                                                    <button type="button" class="btn btn--primary add-btn">+</button>
                                                @endif
                                                <button type="button" class="btn btn--danger remove-btn">-</button>
                                            </div>
                                        </div>



                                    </div>
                                @endforeach
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group float-end">
                                    <button type="submit"
                                        class="btn btn--primary btn-block btn-lg">@lang('Update')</button>
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
    <a href="{{ route('admin.osce.osce-index') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i
            class="las la-angle-double-left"></i>@lang('Go Back')</a>
@endpush


@push('style')
    <style>
        .ck-placeholder {
            height: 190px !important;
        }
    </style>
@endpush
@push('script')
    <script>
        $(document).ready(function() {
            let fieldIndex = 1; // Start index for new sets of fields

            // Add new set of fields
            $(document).on('click', '.add-btn', function() {
                fieldIndex++;
                let newFieldSet = $('#fields-container .field-set').first().clone();

                newFieldSet.find('input').each(function() {
                    let name = $(this).attr('name');
                    let id = $(this).attr('id');
                    $(this).attr('name', name.replace(/\d+/, fieldIndex));
                    $(this).attr('id', id.replace(/\d+/, fieldIndex));
                    $(this).val('');
                });

                newFieldSet.find('.remove-btn').show(); // Show remove button for new set
                $('#fields-container').append(newFieldSet); // Append new set to container
            });

            // Remove set of fields
            $(document).on('click', '.remove-btn', function() {
                $(this).closest('.field-set').remove();

                // Optionally, you can adjust the indices for remaining sets here
                $('#fields-container .field-set').each(function(index) {
                    $(this).find('input').each(function() {
                        let name = $(this).attr('name');
                        let id = $(this).attr('id');
                        $(this).attr('name', name.replace(/\d+/, index + 1));
                        $(this).attr('id', id.replace(/\d+/, index + 1));
                    });
                });

                // Show at least one remove button
                if ($('#fields-container .field-set').length > 1) {
                    $('#fields-container .remove-btn').show();
                } else {
                    $('#fields-container .remove-btn').hide();
                }
            });
        });
    </script>
@endpush
