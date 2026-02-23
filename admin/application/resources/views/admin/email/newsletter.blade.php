@extends('admin.layouts.app')

@section('panel')
<div class="row">

    <div class="col-lg-12"> 
        <div class="card b-radius--10 ">
        <div class="card-header">
        <h5>Send Emails</h5>
    </div>
            <div class="card-body">
            <form action="{{ route('admin.email.newsletter.send') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="subject" class="font-weight-bold">Subject</label>
                <input type="text" name="subject" id="subject" class="form-control" placeholder="Enter Subject" required>
            </div>
            <div class="form-group">
                <label for="message" class="font-weight-bold">Message</label>
                <textarea name="message" id="message" class="form-control" rows="6" placeholder="Enter Newsletter Message" required></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn--primary btn-block">Send Newsletter</button>
            </div>
        </form>
            </div>
             
            <div class="card-footer py-4">
                
            </div>
            
        </div><!-- card end -->
    </div>
</div>


<x-confirmation-modal></x-confirmation-modal>

@endsection


@push('breadcrumb-plugins')

@endpush




@push('style-lib')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="{{asset('assets/admin/css/datepicker.min.css')}}">
@endpush


@push('script-lib')
<script src="{{ asset('assets/admin/js/datepicker.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('assets/admin/js/datepicker.en.js') }}"></script>
@endpush

@push('script')
<script>
    $(document).ready(function() {
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    });
</script>
@endpush



