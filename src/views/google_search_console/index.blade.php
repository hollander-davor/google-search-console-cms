@extends('_layout.layout')

@section('head_title', __('Google Search Console'))

@push('head_links')

@endpush

@section('content')

{{-- this is example breadcrumbs with website picker --}}
@include('_layout.partials.breadcrumbs', [
    'pageTitle' => __("Google Search Console"),
    'websitePicker' => 1,
    'routeName' => 'google_search_console.index'
])
@if($activeWebsite)
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-heading d-flex justify-content-between">
                    <div class="card-heading-title">
                        <h3 class="card-title">@lang('Google Search Console')</h3>
                    </div>
                </div>
                <div class="card-body">
                    <table id="entities-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>@lang('Query')</th>
                                <th>@lang('Clicks')</th>
                                <th>@lang('Impressions')</th>
                                <th>@lang('CTR')</th>
                                <th>@lang('Position')</th>
                                <th>@lang('Actions')</th>
                            </tr>
                        </thead>
            
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection

@push('footer_scripts')
    <script>
        $(document).ready(function(){
            let ItemsDatatable = $('#entities-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "@route('google_search_console.datatable')",
                    type: "POST",
                    data: function(dtData){
                        dtData["_token"] = "{{csrf_token()}}";
                        dtData["activeWebsite"] = "{{$activeWebsite}}"
                    }
                },
                "columns": [
                    {"data": "query","orderable" : false},
                    {"data": "clicks","orderable" : true},
                    {"data": "impressions","orderable" : true},
                    {"data": "ctr","orderable" : true},
                    {"data": "position","orderable" : true},
                    {"data": "actions","orderable" : false},
                ]
            });

            $('#entities-table').questionPop({
                "liveSelector": '[data-action="exclude"]'
            }).on('success.qp', function() {
                $('#entities-table').DataTable().draw('page');

            });
        });
    </script>
@endpush