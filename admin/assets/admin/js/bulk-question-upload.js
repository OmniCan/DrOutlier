class BulkQuestionUpload {
    constructor() {
        this.selectedQuizId = null;
        this.parsedQuestions = null;
        this.currentStep = 1;
        this.selectedFile = null;
        this.currentEditIndex = null;

        this.init();
    }

    init() {
        this.loadQuizzes();
        this.attachEventListeners();
    }

    attachEventListeners() {
        // Step 1 - Quiz Selection
        $('#quizSelect').on('change', () => this.onQuizChange());
        $('#proceedToUploadBtn').on('click', () => this.showStep(2));

        // Step 2 - File Upload
        const dropZone = document.getElementById('uploadDropZone');
        const fileInput = document.getElementById('excelFileInput');

        dropZone.addEventListener('click', () => fileInput.click());
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });
        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                this.handleFileSelect(files[0]);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                this.handleFileSelect(e.target.files[0]);
            }
        });

        $('#backToStep1Btn').on('click', () => this.showStep(1));

        // Step 3 - Preview
        $('#backToStep2Btn').on('click', () => this.showStep(2));
        $('#submitQuestionsBtn').on('click', () => this.submitQuestions());
        $('#expandAllBtn').on('click', () => this.expandAll());
        $('#collapseAllBtn').on('click', () => this.collapseAll());
        $('#saveQuestionEditBtn').on('click', () => this.saveQuestionEdit());

        // Step 4 - Success
        $('#uploadAnotherBtn').on('click', () => this.reset());
    }

    loadQuizzes() {
        $.ajax({
            url: '/admin/quiz/get-quiz-list',
            method: 'GET',
            success: (response) => {
                if (response.success) {
                    const select = $('#quizSelect');
                    select.empty().append('<option value="">-- Select Quiz --</option>');
                    response.quizzes.forEach(quiz => {
                        select.append(`<option value="${quiz.id}" data-category="${quiz.category}">${quiz.name}</option>`);
                    });
                }
            },
            error: (xhr) => {
                this.showError('Failed to load quizzes');
            }
        });
    }

    onQuizChange() {
        const select = $('#quizSelect');
        const quizId = select.val();
        
        if (quizId) {
            this.selectedQuizId = quizId;
            const quizName = select.find('option:selected').text();
            const category = select.find('option:selected').data('category');
            
            $('#quizName').text(quizName);
            $('#quizCategory').text(`Category: ${category}`);
            $('#quizInfo').show();
            $('#proceedToUploadBtn').prop('disabled', false);
        } else {
            $('#quizInfo').hide();
            $('#proceedToUploadBtn').prop('disabled', true);
        }
    }

    handleFileSelect(file) {
        if (!file.name.match(/\.(xlsx|xls)$/)) {
            this.showError('Please select a valid Excel file (.xlsx or .xls)');
            return;
        }

        this.selectedFile = file;
        
        $('#fileName').text(file.name);
        $('#fileSize').text(this.formatFileSize(file.size));
        $('#fileInfo').show();

        // Auto-parse the file
        this.parseFile();
    }

    parseFile() {
        if (!this.selectedFile || !this.selectedQuizId) {
            this.showError('Please select a quiz and file');
            return;
        }

        $('#uploadProgress').show();

        const formData = new FormData();
        formData.append('excel_file', this.selectedFile);
        formData.append('quiz_id', this.selectedQuizId);

        $.ajax({
            url: '/admin/quiz/parse-bulk-questions',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                $('#uploadProgress').hide();
                if (response.success) {
                    this.parsedQuestions = response.data.questions;
                    this.showStep(3);
                    this.renderPreview(response.data);
                } else {
                    this.showError(response.message || 'Failed to parse file');
                }
            },
            error: (xhr) => {
                $('#uploadProgress').hide();
                const error = xhr.responseJSON?.message || 'Failed to upload file';
                this.showError(error);
            }
        });
    }

    renderPreview(data) {
        // Render validation summary
        let summaryHtml = '';
        if (data.errors.length > 0) {
            summaryHtml += `<div class="alert alert-danger">
                <h5><i class="las la-exclamation-triangle"></i> ${data.errors.length} Error(s) Found</h5>
                <ul class="mb-0">`;
            data.errors.forEach(err => {
                summaryHtml += `<li>Row ${err.row} (${err.id}): ${err.message}</li>`;
            });
            summaryHtml += `</ul></div>`;
        }

        if (data.warnings.length > 0) {
            summaryHtml += `<div class="alert alert-warning">
                <h5><i class="las la-exclamation-circle"></i> ${data.warnings.length} Warning(s)</h5>
                <ul class="mb-0">`;
            data.warnings.forEach(warn => {
                summaryHtml += `<li>Row ${warn.row} (${warn.id}): ${warn.message}</li>`;
            });
            summaryHtml += `</ul></div>`;
        }

        if (data.errors.length === 0 && data.warnings.length === 0) {
            summaryHtml = `<div class="alert alert-success">
                <i class="las la-check-circle"></i> All ${data.total} questions validated successfully!
            </div>`;
        }

        $('#validationSummary').html(summaryHtml);
        $('#validQuestionsCount').text(`${data.valid} Valid`);
        $('#invalidQuestionsCount').text(`${data.invalid} Errors`);

        // Render question cards
        let questionsHtml = '';
        this.parsedQuestions.forEach((q, index) => {
            const hasError = q.errors && q.errors.length > 0;
            const cardClass = hasError ? 'question-card has-error' : 'question-card';
            
            questionsHtml += `<div class="${cardClass}" data-index="${index}">
                <div class="question-header" onclick="bulkUpload.toggleQuestion(${index})">
                    <div>
                        <span class="badge badge--info">${q.id}</span>
                        ${hasError ? '<span class="badge badge--danger ml-2"><i class="las la-times"></i> Has Errors</span>' : '<span class="badge badge--success ml-2"><i class="las la-check"></i> Valid</span>'}
                        <span class="ml-3">${this.truncate(q.question_text, 80)}</span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn--primary mr-2" onclick="event.stopPropagation(); bulkUpload.editQuestion(${index});">
                            <i class="las la-edit"></i> Edit
                        </button>
                        <button type="button" class="btn btn-sm btn--danger" onclick="event.stopPropagation(); bulkUpload.deleteQuestion(${index});">
                            <i class="las la-trash"></i>
                        </button>
                        <i class="las la-angle-down ml-2"></i>
                    </div>
                </div>
                <div class="question-body" id="question-body-${index}">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <strong>Question:</strong>
                            <p>${q.question_text}</p>
                            ${q.question_image ? `<img src="/assets/QuestionsImage/${q.question_image}" class="img-thumbnail" style="max-width: 200px;">` : ''}
                        </div>
                        <div class="col-md-12 mb-3">
                            <strong>Options:</strong>
                            ${q.options.map(opt => `
                                <div class="option-item ${opt.is_correct ? 'correct' : ''}">
                                    ${opt.is_correct ? '<i class="las la-check-circle text-success"></i>' : '<i class="lar la-circle text-muted"></i>'}
                                    <strong>${opt.label}:</strong> ${opt.option_text}
                                    ${opt.option_image ? `<br><img src="/assets/QuestionsImage/${opt.option_image}" class="img-thumbnail mt-2" style="max-width: 150px;">` : ''}
                                </div>
                            `).join('')}
                        </div>
                        ${q.explanation ? `
                        <div class="col-md-12 mb-3">
                            <strong>Explanation:</strong>
                            <p>${q.explanation}</p>
                        </div>
                        ` : ''}
                        <div class="col-md-12">
                            <small class="text-muted">Marks: ${q.marks} | Sort Order: ${q.sort_order}</small>
                        </div>
                        ${hasError ? `
                        <div class="col-md-12 mt-3">
                            <div class="alert alert-danger mb-0">
                                <strong>Errors:</strong>
                                <ul class="mb-0">
                                    ${q.errors.map(err => `<li>${err}</li>`).join('')}
                                </ul>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>`;
        });

        $('#questionsPreview').html(questionsHtml);
    }

    toggleQuestion(index) {
        $(`#question-body-${index}`).toggleClass('show');
    }

    expandAll() {
        $('.question-body').addClass('show');
    }

    collapseAll() {
        $('.question-body').removeClass('show');
    }

    editQuestion(index) {
        const question = this.parsedQuestions[index];
        this.currentEditIndex = index;

        let modalHtml = `
            <div class="form-group">
                <label>Question Text <span class="text-danger">*</span></label>
                <textarea class="form-control" id="edit-question-text" rows="3">${question.question_text}</textarea>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Marks</label>
                        <input type="number" class="form-control" id="edit-marks" value="${question.marks}" min="1" max="10">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" class="form-control" id="edit-sort-order" value="${question.sort_order}" min="0">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Options (Check correct answers)</label>
                <div id="edit-options">`;

        question.options.forEach((opt, i) => {
            modalHtml += `
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input type="checkbox" id="edit-opt-correct-${i}" ${opt.is_correct ? 'checked' : ''}>
                        </div>
                        <span class="input-group-text"><strong>${opt.label}</strong></span>
                    </div>
                    <input type="text" class="form-control" id="edit-opt-text-${i}" value="${opt.option_text}" placeholder="Option ${opt.label}">
                </div>`;
        });

        modalHtml += `
                </div>
            </div>
            <div class="form-group">
                <label>Explanation</label>
                <textarea class="form-control" id="edit-explanation" rows="3">${question.explanation || ''}</textarea>
            </div>`;

        $('#editModalContent').html(modalHtml);
        $('#editQuestionModal').modal('show');
    }

    saveQuestionEdit() {
        const index = this.currentEditIndex;
        const question = this.parsedQuestions[index];

        // Update question data
        question.question_text = $('#edit-question-text').val();
        question.marks = parseInt($('#edit-marks').val());
        question.sort_order = parseInt($('#edit-sort-order').val());
        question.explanation = $('#edit-explanation').val();

        // Update options
        question.options.forEach((opt, i) => {
            opt.option_text = $(`#edit-opt-text-${i}`).val();
            opt.is_correct = $(`#edit-opt-correct-${i}`).is(':checked') ? 1 : 0;
        });

        // Re-validate
        question.errors = [];
        if (!question.question_text) {
            question.errors.push('Question text is required');
        }

        const hasCorrect = question.options.some(opt => opt.is_correct);
        if (!hasCorrect) {
            question.errors.push('At least one correct answer is required');
        }

        $('#editQuestionModal').modal('hide');
        
        // Re-render preview
        this.renderPreview({
            total: this.parsedQuestions.length,
            valid: this.parsedQuestions.filter(q => q.errors.length === 0).length,
            invalid: this.parsedQuestions.filter(q => q.errors.length > 0).length,
            questions: this.parsedQuestions,
            errors: [],
            warnings: []
        });

        this.showSuccess('Question updated successfully');
    }

    deleteQuestion(index) {
        if (confirm('Are you sure you want to delete this question?')) {
            this.parsedQuestions.splice(index, 1);
            
            // Re-render
            this.renderPreview({
                total: this.parsedQuestions.length,
                valid: this.parsedQuestions.filter(q => q.errors.length === 0).length,
                invalid: this.parsedQuestions.filter(q => q.errors.length > 0).length,
                questions: this.parsedQuestions,
                errors: [],
                warnings: []
            });

            this.showSuccess('Question deleted');
        }
    }

    submitQuestions() {
        const validQuestions = this.parsedQuestions.filter(q => q.errors.length === 0);
        
        if (validQuestions.length === 0) {
            this.showError('No valid questions to submit');
            return;
        }

        if (!confirm(`Submit ${validQuestions.length} questions?`)) {
            return;
        }

        $('#submitQuestionsBtn').prop('disabled', true).html('<i class="las la-spinner la-spin"></i> Submitting...');

        $.ajax({
            url: '/admin/quiz/submit-bulk-questions',
            method: 'POST',
            data: JSON.stringify({ questions: validQuestions }),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                if (response.success) {
                    const inserted = response.data?.inserted || 0;
                    const errors = response.data?.errors || 0;
                    let message = `${inserted} question${inserted !== 1 ? 's' : ''} uploaded successfully`;
                    if (errors > 0) {
                        message += ` (${errors} failed)`;
                    }
                    $('#successMessage').text(message);
                    
                    // Log error details if any
                    if (response.data?.error_messages && response.data.error_messages.length > 0) {
                        console.error('Upload errors:', response.data.error_messages);
                    }
                    
                    this.showStep(4);
                } else {
                    this.showError(response.message);
                    $('#submitQuestionsBtn').prop('disabled', false).html('<i class="las la-check-circle"></i> Submit Questions');
                }
            },
            error: (xhr) => {
                const error = xhr.responseJSON?.message || 'Failed to submit questions';
                this.showError(error);
                $('#submitQuestionsBtn').prop('disabled', false).html('<i class="las la-check-circle"></i> Submit Questions');
            }
        });
    }

    showStep(step) {
        $('.step-content').hide();
        $(`#step${step}`).show();
        this.currentStep = step;
    }

    reset() {
        this.selectedQuizId = null;
        this.parsedQuestions = null;
        this.selectedFile = null;
        $('#quizSelect').val('');
        $('#quizInfo').hide();
        $('#fileInfo').hide();
        $('#excelFileInput').val('');
        this.showStep(1);
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    truncate(text, length) {
        if (text.length <= length) return text;
        return text.substr(0, length) + '...';
    }

    showError(message) {
        Toast.fire({
            icon: 'error',
            title: message
        });
    }

    showSuccess(message) {
        Toast.fire({
            icon: 'success',
            title: message
        });
    }
}
