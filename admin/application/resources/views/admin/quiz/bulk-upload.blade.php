@extends('admin.layouts.app')

@section('panel')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body">
                <div id="bulkUploadContainer">
                    
                    <!-- Step 1: Select Quiz -->
                    <div id="step1" class="step-content">
                        <h4 class="mb-4"><i class="las la-list-alt"></i> Step 1: Select Quiz</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="quizSelect" class="form-label">Select Quiz <span class="text--danger">*</span></label>
                                <select id="quizSelect" class="form-control">
                                    <option value="">-- Select Quiz --</option>
                                </select>
                                <small class="form-text text-muted">Choose the quiz to add questions to</small>
                            </div>
                        </div>
                        <div id="quizInfo" class="alert alert-info mt-3" style="display:none;">
                            <h5 id="quizName"></h5>
                            <p id="quizCategory" class="mb-0"></p>
                        </div>
                        <div class="mt-4">
                            <button id="proceedToUploadBtn" class="btn btn--primary" disabled>
                                <i class="las la-arrow-right"></i> Proceed to Upload
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Upload Excel File -->
                    <div id="step2" class="step-content" style="display:none;">
                        <h4 class="mb-4"><i class="las la-file-excel"></i> Step 2: Upload Excel File</h4>
                        
                        <div class="alert alert-success">
                            <i class="las la-download"></i> 
                            <strong>Download Template:</strong> 
                            <a href="{{ route('admin.quiz.download-template') }}" class="btn btn-sm btn-success ml-2">
                                <i class="las la-file-excel"></i> Download Excel Template
                            </a>
                        </div>

                        <div id="uploadDropZone" class="upload-drop-zone">
                            <i class="las la-cloud-upload-alt" style="font-size: 48px; color: #666;"></i>
                            <p class="mt-3">Drag & drop Excel file here or click to browse</p>
                            <input type="file" id="excelFileInput" accept=".xlsx,.xls" style="display:none;">
                            <button type="button" class="btn btn--primary mt-2" onclick="document.getElementById('excelFileInput').click();">
                                <i class="las la-folder-open"></i> Browse Files
                            </button>
                        </div>

                        <div id="fileInfo" class="mt-3" style="display:none;">
                            <div class="alert alert-info">
                                <strong>Selected File:</strong> <span id="fileName"></span><br>
                                <strong>Size:</strong> <span id="fileSize"></span>
                            </div>
                        </div>

                        <div id="uploadProgress" class="mt-3" style="display:none;">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%">
                                    Parsing Excel file...
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button id="backToStep1Btn" class="btn btn-secondary">
                                <i class="las la-arrow-left"></i> Back
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Preview & Edit -->
                    <div id="step3" class="step-content" style="display:none;">
                        <h4 class="mb-4"><i class="las la-eye"></i> Step 3: Preview & Edit Questions</h4>

                        <div id="validationSummary" class="mb-4"></div>

                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <button id="expandAllBtn" class="btn btn-sm btn--info">
                                    <i class="las la-plus-circle"></i> Expand All
                                </button>
                                <button id="collapseAllBtn" class="btn btn-sm btn--info">
                                    <i class="las la-minus-circle"></i> Collapse All
                                </button>
                            </div>
                            <div>
                                <span class="badge badge--success" id="validQuestionsCount">0 Valid</span>
                                <span class="badge badge--danger" id="invalidQuestionsCount">0 Errors</span>
                            </div>
                        </div>

                        <div id="questionsPreview"></div>

                        <div class="mt-4">
                            <button id="backToStep2Btn" class="btn btn-secondary">
                                <i class="las la-arrow-left"></i> Back
                            </button>
                            <button id="submitQuestionsBtn" class="btn btn--primary">
                                <i class="las la-check-circle"></i> Submit Questions
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Success -->
                    <div id="step4" class="step-content" style="display:none;">
                        <div class="text-center py-5">
                            <i class="las la-check-circle text-success" style="font-size: 72px;"></i>
                            <h3 class="mt-3">Questions Uploaded Successfully!</h3>
                            <p class="text-muted" id="successMessage"></p>
                            <button id="uploadAnotherBtn" class="btn btn--primary mt-3">
                                <i class="las la-plus"></i> Upload More Questions
                            </button>
                            <a href="{{ route('admin.quiz.index') }}" class="btn btn--info mt-3">
                                <i class="las la-list"></i> View Questions
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Question Modal -->
<div class="modal fade" id="editQuestionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editModalContent">
                <!-- Dynamic content -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn--primary" id="saveQuestionEditBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('breadcrumb-plugins')
<a href="{{route('admin.quiz.index')}}" class="btn btn-sm btn--primary">
    <i class="las la-list"></i>@lang('All Questions')
</a>
@endpush

@push('style')
<style>
    .upload-drop-zone {
        border: 3px dashed #ccc;
        border-radius: 10px;
        padding: 60px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    .upload-drop-zone:hover {
        border-color: #0071C5;
        background-color: #f8f9fa;
    }
    .upload-drop-zone.dragover {
        border-color: #0071C5;
        background-color: #e3f2fd;
    }
    .question-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 15px;
        overflow: hidden;
    }
    .question-card.has-error {
        border-left: 4px solid #dc3545;
    }
    .question-header {
        background: #f5f5f5;
        padding: 15px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .question-header:hover {
        background: #e9ecef;
    }
    .question-body {
        padding: 20px;
        display: none;
    }
    .question-body.show {
        display: block;
    }
    .option-item {
        padding: 10px;
        margin: 5px 0;
        border-radius: 5px;
        background: #f8f9fa;
    }
    .option-item.correct {
        background: #d4edda;
        border-left: 3px solid #28a745;
    }
    .step-content {
        min-height: 400px;
    }
</style>
@endpush

@push('script')
<script src="{{ asset('assets/admin/js/bulk-question-upload.js') }}"></script>
<script>
    const bulkUpload = new BulkQuestionUpload();
</script>
@endpush
