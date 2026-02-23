@extends('admin.layouts.app')

@section('panel')
<div class="row mb-none-30">
    <div class="col-lg-12 mb-30">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.quiz.update', $question->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="category_id" class="font-weight-bold">@lang('Quiz')</label>
                                        <select name="category_id" id="category_id" class="form-control" required>
                                            <option value="" selected disabled>@lang('Select Quiz')</option>
                                            @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $question->quiz_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="question_text" class="font-weight-bold">@lang('Question Text')</label>
                                        <input type="text" name="question_text" id="question_text" value="{{ old('question_text', $question->question_text) }}" class="form-control" placeholder="@lang('Enter Question Text')" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="image" class="font-weight-bold">@lang('Image')</label>
                                        <div class="file-upload-wrapper" data-text="Select image!">
                                            <input type="file" name="image" id="image" class="file-upload-field">



                                        </div>
                                    </div>
                                </div>
                                @if ($question->image)
                                <p class="mt-2 text-end">
                                    Current Image:
                                    <a href="{{ asset(getFilePath('QuestionsImage') . '/' . $question->image) }}" target="_blank" class="btn btn-outline-success">
                                        View Image
                                    </a>
                                </p>
                                @endif

                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="explanation" class="font-weight-bold">@lang('Explanation')</label>
                                    <textarea class="summernote form-control mb-2" name="explanation"  placeholder="@lang('Explanation')">{{old('explanation', $question->explanation)}}</textarea>


                                </div>
                            </div>
                            <div class="row">
                                <div class="text-right float-end mt-2 mb-3">
                                    <button type="button" id="add-answer-btn" class="btn btn-sm btn-primary ml-2 mb-2">@lang('Add Answer')</button>
                                </div>
                            </div>
                            <div class="col-lg-12">

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <div id="options-container">
                                            @foreach ($question->answers as $index => $answer)
                                            <div class="option-group" id="option-group-{{ $index }}">
                                                <label class="font-weight-bold">@lang('Answer Text') {{ $index + 1 }}</label>
                                                <input type="text" name="options[{{ $index }}][option_text]" class="form-control mb-2" value="{{ old('options.' . $index . '.option_text', $answer->option_text) }}" placeholder="@lang('Answer Text')" required>

                                                <!-- <label for="explanation" class="font-weight-bold">@lang('Answer Explanation')</label>
                                                <textarea class="summernote form-control mb-2" name="options[{{ $index }}][explanation]" placeholder="@lang('Option Explanation')">{{ old('options.' . $index . '.explanation', $answer->explanation) }}</textarea> -->

                                                <label>
                                                    <hr />
                                                </label>
                                                <label for="is_correct" class="font-weight-bold">@lang('Correct Answer')</label>
                                                <input type="checkbox" name="options[{{ $index }}][is_correct]" value="1" {{ old('options.' . $index . '.is_correct', $answer->is_correct) ? 'checked' : '' }}>

                                                <div class="m-3">
                                                    <button type="button" class="btn btn-danger btn-sm remove-answer-btn">Remove Answer</button>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group float-end">
                                    <button type="submit" class="btn btn--primary btn-block btn-sm ml-2">@lang('Update')</button>
                                </div>
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
<a href="{{ route('admin.quiz.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="las la-angle-double-left"></i>@lang('Go Back')</a>
@endpush

@push('style-lib')
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css" rel="stylesheet">
@endpush

@push('style')
<style>
    .ck-placeholder {
        height: 150px !important;
    }
</style>
@endpush

@push('script-lib')

<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.js"></script>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";

           var answerCount = {{ count($question->answers) }};
    const maxAnswers = 4;  // Max answers allowed

    function initializeSummernote() {
        $('.summernote').each(function() {
            if (!$(this).hasClass('initialized')) {
                $(this).summernote({
                    height: 150,
                    placeholder: "Enter explanation...",
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link', 'picture']]
                    ]
                }).addClass('initialized');
            }
        });
    }


        $('#add-answer-btn').on('click', function() {
            if (answerCount < maxAnswers) {
                var newAnswerIndex = answerCount;

                $('#options-container').append(
                    `<div class="option-group" id="option-group-${newAnswerIndex}">
                    <label class="font-weight-bold">Answer Text ${newAnswerIndex + 1}</label>
                    <input type="text" name="options[${newAnswerIndex}][option_text]" class="form-control mb-2" placeholder="Answer Text" required>
                    
                    <label for="explanation" class="font-weight-bold">Answer Explanation</label>
                    <textarea class="summernote form-control mb-2" name="options[${newAnswerIndex}][explanation]" placeholder="Option Explanation"></textarea>
                    
                    <label><hr/></label>
                    <label for="is_correct" class="font-weight-bold">Correct Answer</label>
                    <input type="checkbox" name="options[${newAnswerIndex}][is_correct]" value="1">
                    
                    <div class="m-3">
                        <button type="button" class="btn btn-danger btn-sm remove-answer-btn">Remove Answer</button>
                    </div>
                </div>`
                );

                initializeSummernote();
                answerCount++;

                if (answerCount === maxAnswers) {
                    $('#add-answer-btn').hide(); // Hide button when 4 answers are added
                }
            }
        });

        $(document).on('click', '.remove-answer-btn', function() {
            $(this).closest('.option-group').remove();
            answerCount--;

            if (answerCount < maxAnswers) {
                $('#add-answer-btn').show(); // Show button if answers are removed
            }

            $('#options-container .option-group').each(function(index) {
                $(this).find('label:first').text(`Answer Text ${index + 1}`);
            });
        });

        initializeSummernote();

    })(jQuery);
</script>
@endpush