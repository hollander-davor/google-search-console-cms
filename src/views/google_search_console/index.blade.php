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
                <div class="col-sm-12 pt-3" id="filter-datatable">
                    <div class="row">
                        <div class="form-group col-2">
                            <label class="control-label">@lang('Filter by status EXCLUDED')</label>
                            <select id="select-excluded-status" class="form-control" name='excluded-status'>
                                <option value="all">@lang("All")</option>
                                <option value="excluded">@lang("Excluded")</option>
                                <option selected value="not-excluded">@lang("Not excluded")</option>
    
                            </select>
                        </div>
                        <div class="form-group col-2">
                            <label class="control-label">@lang('Filter by status FIXED')</label>
                            <select id="select-fixed-status" class="form-control" name='fixed-status'>
                                <option value="all">@lang("All")</option>
                                <option value="fixed">@lang("Fixed")</option>
                                <option selected value="not-fixed">@lang("Not fixed")</option>
    
                            </select>
                        </div>
                        <div class="form-group col-2">
                            <label class="control-label">@lang('Filter ')"{{config('gsc-cms.critical_query_text')}}"</label>
                            <select id="select-critical-status" class="form-control" name='critical-status'>
                                <option selected value="all">@lang("All")</option>
                                <option value="true">@lang("True")</option>
                                <option value="false">@lang("False")</option>
    
                            </select>
                        </div>
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
                'createdRow': function( row, data, dataIndex ) {
                    if(data.critical == 1){
                        $(row).css('background-color', '{{config('gsc-cms.critical_query_color')}}');
                    }
                    if(data.excluded == 1){
                        $(row).css('background-color', '{{config('gsc-cms.excluded_query_color')}}');
                    }
                    if(data.fixed == 1){
                        $(row).css('background-color', '{{config('gsc-cms.fixed_query_color')}}');
                    }
                },
                "ajax": {
                    url: "@route('google_search_console.datatable')",
                    type: "POST",
                    data: function(dtData){
                        dtData["_token"] = "{{csrf_token()}}";
                        dtData["activeWebsite"] = "{{$activeWebsite}}"
                        dtData["excludedStatus"] = $('#select-excluded-status').val()
                        dtData["fixedStatus"] = $('#select-fixed-status').val()
                        dtData["criticalStatus"] = $('#select-critical-status').val()

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

            $('#entities-table').questionPop({
                "liveSelector": '[data-action="fixed"]'
            }).on('success.qp', function() {
                $('#entities-table').DataTable().draw('page');
            });
        });
    </script>
@endpush