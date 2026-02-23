@extends('admin.layouts.app')

@section('panel')
@if(@json_decode($general->system_info)->message)
<div class="row">
    @foreach(json_decode($general->system_info)->message as $msg)
    <div class="col-md-12">
        <div class="alert border border--primary" role="alert">
            <div class="alert__icon bg--primary"><i class="far fa-bell"></i></div>
            <p class="alert__message">@php echo $msg; @endphp</p>
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    </div>
    @endforeach
</div>
@endif 


<div class="row mb-3">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 bg-primary">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-md font-weight-bold text-white mb-1">{{ __('Total User') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-white"></div>
                        {{$user}}
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-ticket fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 bg-success">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-md font-weight-bold text-white mb-1">{{ __('Total Category') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-white"></div>

                    </div>
                    <div class="col-auto">
                        <i class="fas fa-ticket fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 bg-danger">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-md font-weight-bold text-white mb-1">{{ __('Total Spotters') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-white"></div>
                        {{$spotters}}
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-ticket fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 bg-secondary">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-md font-weight-bold text-white mb-1">{{ __('Total Notes') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-white"></div>
                        {{$blog}}
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-ticket fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 
@endsection

@push('script')
   
@endpush
