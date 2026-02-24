@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12"> 
        <div class="card b-radius--10 ">
            <div class="card-body">
                 <div class="d-flex flex-wrap justify-content-end mb-3">
                    <form action="" method="GET" class="form-inline">
                        <div class="input-group justify-content-end">
                            <input type="text" name="search" class="form-control bg--white" placeholder="@lang('Search by Category Name')" value="{{ request()->search }}">
                            <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two ">
                        <thead>
                            <tr> 
                                <th>@lang('Image')</th>
                                <th>@lang('Category Name')</th>
                                <th>@lang('Quizzes')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white"> 
                            @forelse($categories as $category)
                                <tr>
                                    <td>
                                        <img src="{{getImage(getFilePath('QuestionsImage').'/'.@$category->image)}}" height="50" width="50"/>
                                    </td>
                                    <td>{{ $category->name }}</td>
                                    <td>
                                        <span class="badge badge--info">
                                            <i class="las la-clipboard-list"></i> {{ $category->quizzes_count }} {{ $category->quizzes_count == 1 ? 'Quiz' : 'Quizzes' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($category->status == 1)
                                            <span class="badge badge--success">@lang('Active')</span>
                                        @else
                                            <span class="badge badge--warning">@lang('Inactive')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a title="@lang('Edit')" href="{{ route('admin.quiz.category.edit', $category->id)}}" class="btn btn-sm btn--primary"><i class="la la-pen"></i></a>
                                        <button class="btn btn-outline--danger btn-sm confirmationBtn" data-question="@lang('Are you sure that you want to delete?')" data-action="{{ route('admin.quiz.category.delete', $category->id)}}" title="Delete"><i class="las la-trash"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No Categories Found!</td>
                                </tr>
                            @endif
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>

            <div class="card-footer py-4">
                {{ paginateLinks($categories) }}
            </div>

        </div><!-- card end -->
    </div>
</div>

<x-confirmation-modal></x-confirmation-modal>

@endsection

@push('breadcrumb-plugins')
<a href="{{route('admin.quiz.category.create')}}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="las la-plus-circle"></i>@lang('Add Category')</a>
@endpush
