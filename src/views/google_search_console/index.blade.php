@extends('_layout.layout')

@section('head_title', __('Google Search Console'))

@push('head_links')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.0/nouislider.min.css" rel="stylesheet">
@endpush

@section('content')
<style>
    .slider {
        margin: 20px;
        width: 300px;
    }
</style>
    {{-- this is example breadcrumbs with website picker --}}
    @include('google_search_console.partials.breadcrumbs', [
        'pageTitle' => __('Google Search Console'),
        'websitePicker' => 1,
        'routeName' => 'google_search_console.index',
    ])
    @if ($activeWebsite)
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
                                    <option selected value="all">@lang('All')</option>
                                    <option value="excluded">@lang('Excluded')</option>
                                    <option value="not-excluded">@lang('Not excluded')</option>

                                </select>
                            </div>
                            <div class="form-group col-2">
                                <label class="control-label">@lang('Filter by status FIXED')</label>
                                <select id="select-fixed-status" class="form-control" name='fixed-status'>
                                    <option selected value="all">@lang('All')</option>
                                    <option value="fixed">@lang('Fixed')</option>
                                    <option value="not-fixed">@lang('Not fixed')</option>

                                </select>
                            </div>
                            <div class="form-group col-2">
                                <label class="control-label">@lang('Filter by status DELEGATED')</label>
                                <select id="select-delegated-status" class="form-control" name='delegated-status'>
                                    <option selected value="all">@lang('All')</option>
                                    <option value="delegated">@lang('Delegated')</option>
                                    <option value="not-delegated">@lang('Not delegated')</option>

                                </select>
                            </div>
                            <div class="form-group col-2">
                                <label
                                    class="control-label">@lang('Filter ')"{{ config('gsc-cms.critical_query_text') }}"</label>
                                <select id="select-critical-status" class="form-control" name='critical-status'>
                                    <option selected value="all">@lang('All')</option>
                                    <option value="true">@lang('True')</option>
                                    <option value="false">@lang('False')</option>

                                </select>
                            </div>
                            <div class="form-group col-2">
                                <label
                                    class="control-label">@lang('Filter ')"{{ config('gsc-cms.lhf_query_text') }}"</label>
                                <select id="select-lhf-status" class="form-control" name='lhf-status'>
                                    <option selected value="all">@lang('All')</option>
                                    <option value="true">@lang('True')</option>
                                    <option value="false">@lang('False')</option>

                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-3">
                                <label class="control-label" style="margin-bottom: 20px;">@lang('Filter by clicks value')</label>
                                <div id="slider1" class="slider"></div>
                            </div>
                            <div class="form-group col-3">
                                <label class="control-label" style="margin-bottom: 20px;">@lang('Filter by impressions value')</label>
                                <div id="slider2" class="slider"></div>
                            </div>
                            <div class="form-group col-3">
                                <label class="control-label" style="margin-bottom: 20px;">@lang('Filter by CTR value')</label>
                                <div id="slider3" class="slider"></div>
                            </div>
                            <div class="form-group col-3">
                                <label class="control-label" style="margin-bottom: 20px;">@lang('Filter by position value')</label>
                                <div id="slider4" class="slider"></div>
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
                                    <th>@lang('Delegated to')</th>
                                    <th>@lang('Query status')</th>
                                    <th>@lang('Answer')</th>
                                    <th>@lang('Actions')</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- Modal -->
    @include('google_search_console.partials.delegate_modal.modal')
@endsection

@push('footer_scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.0/nouislider.min.js"></script>
    <script>
        $(document).ready(function() {
        // Slider za kolonu 1 (prirodni brojevi od 1 do 10,000)
        var slider1 = document.getElementById('slider1');
            noUiSlider.create(slider1, {
                start: [0, {{$maxValue}}],
                connect: true,
                range: {
                    'min': 0,
                    'max': {{$maxValue}}
                },
                tooltips: {
                    // tooltips are output only, so only a "to" is needed
                    to: function(numericValue) {
                        return numericValue.toFixed(0);
                    }
                },
                step: 100
            });
            slider1.noUiSlider.set(['1', '{{$maxValue}}']);

            // Slider za kolonu 2 (prirodni brojevi od 1 do 10,000)
            var slider2 = document.getElementById('slider2');
            noUiSlider.create(slider2, {
                start: [0, {{$maxValue}}],
                connect: true,
                range: {
                    'min': 0,
                    'max': {{$maxValue}}
                },
                tooltips: {
                    // tooltips are output only, so only a "to" is needed
                    to: function(numericValue) {
                        return numericValue.toFixed(0);
                    }
                },
                step: 100
            });
            slider2.noUiSlider.set(['1', '{{$maxValue}}']);

            // Slider za kolonu 3 (brojevi sa dve decimale između 0 i 1)
            var slider3 = document.getElementById('slider3');
            noUiSlider.create(slider3, {
                start: [0, 1],
                connect: true,
                range: {
                    'min': 0,
                    'max': 1
                },
                tooltips: {
                    // tooltips are output only, so only a "to" is needed
                    to: function(numericValue) {
                        return numericValue.toFixed(2);
                    }
                },
                step: 0.01
            });
            slider3.noUiSlider.set(['0', '1']);

            // Slider za kolonu 4 (brojevi od 1 do 100 sa dve decimale)
            var slider4 = document.getElementById('slider4');
            noUiSlider.create(slider4, {
                start: [0, 100],
                connect: true,
                range: {
                    'min': 0,
                    'max': 100
                },
                tooltips: {
                    // tooltips are output only, so only a "to" is needed
                    to: function(numericValue) {
                        return numericValue.toFixed(2);
                    }
                },
                step: 0.01
            });
            slider4.noUiSlider.set(['0', '100']);

            let ItemsDatatable = $('#entities-table').DataTable({
                "processing": true,
                "serverSide": true,
                'createdRow': function(row, data, dataIndex) {
                    if (data.critical == 1) {
                        $(row).css('background-color', '{{ config('gsc-cms.critical_query_color') }}');
                    }
                    if (data.low_hanging_fruit == 1) {
                        $(row).css('background-color', '{{ config('gsc-cms.lhf_query_color') }}');
                    }
                    if (data.query_status != null) {
                        if (data.query_status.excluded == 1) {
                            $(row).css('background-color',
                                '{{ config('gsc-cms.excluded_query_color') }}');
                        }
                        if (data.query_status.fixed == 1) {
                            $(row).css('background-color', '{{ config('gsc-cms.fixed_query_color') }}');
                        }
                        if (data.query_status.delegated == 1) {
                            $(row).css('background-color',
                                '{{ config('gsc-cms.delegated_query_color') }}');
                        }
                    }
                },
                "ajax": {
                    url: "@route('google_search_console.datatable')",
                    type: "POST",
                    data: function(dtData) {
                        var slider1Values = $('#slider1')[0].noUiSlider.get();
                        var slider2Values = $('#slider2')[0].noUiSlider.get();
                        var slider3Values = $('#slider3')[0].noUiSlider.get();
                        var slider4Values = $('#slider4')[0].noUiSlider.get();
                        dtData['slider1_min'] = slider1Values[0];
                        dtData['slider1_max'] = slider1Values[1];
                        dtData['slider2_min'] = slider2Values[0];
                        dtData['slider2_max'] = slider2Values[1];
                        dtData['slider3_min'] = slider3Values[0];
                        dtData['slider3_max'] = slider3Values[1];
                        dtData['slider4_min'] = slider4Values[0];
                        dtData['slider4_max'] = slider4Values[1];

                        dtData["_token"] = "{{ csrf_token() }}";
                        dtData["activeWebsite"] = "{{ $activeWebsite }}"
                        dtData["excludedStatus"] = $('#select-excluded-status').val()
                        dtData["fixedStatus"] = $('#select-fixed-status').val()
                        dtData["delegatedStatus"] = $('#select-delegated-status').val()
                        dtData["criticalStatus"] = $('#select-critical-status').val()
                        dtData["lhfStatus"] = $('#select-lhf-status').val()
                    }
                },
                "columns": [{
                        "data": "query",
                        "orderable": false
                    },
                    {
                        "data": "clicks",
                        "orderable": true
                    },
                    {
                        "data": "impressions",
                        "orderable": true
                    },
                    {
                        "data": "ctr",
                        "orderable": true
                    },
                    {
                        "data": "position",
                        "orderable": true
                    },
                    {
                        "data": "delegated_to",
                        "orderable": true
                    },
                    {
                        "data": "query_statuses",
                        "orderable": false
                    },
                    {
                        "data": "answer",
                        "orderable": false
                    },
                    {
                        "data": "actions",
                        "orderable": false
                    },
                ]
            });

            $('#select-excluded-status').on('change', function(e) {
                e.preventDefault();
                ItemsDatatable.ajax.reload(null, true);
            });

            $('#select-fixed-status').on('change', function(e) {
                e.preventDefault();
                ItemsDatatable.ajax.reload(null, true);
            });

            $('#select-delegated-status').on('change', function(e) {
                e.preventDefault();
                ItemsDatatable.ajax.reload(null, true);
            });

            $('#select-critical-status').on('change', function(e) {
                e.preventDefault();
                ItemsDatatable.ajax.reload(null, true);
            });

            $('#select-lhf-status').on('change', function(e) {
                e.preventDefault();
                ItemsDatatable.ajax.reload(null, true);
            });

            $('#slider1')[0].noUiSlider.on('change', function () {
                ItemsDatatable.ajax.reload(null, true);
            });
            $('#slider2')[0].noUiSlider.on('change', function () {
                ItemsDatatable.ajax.reload(null, true);
            });
            $('#slider3')[0].noUiSlider.on('change', function () {
                ItemsDatatable.ajax.reload(null, true);
            });
            $('#slider4')[0].noUiSlider.on('change', function () {
                ItemsDatatable.ajax.reload(null, true);
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

            $('#entities-table').questionPop({
                "liveSelector": '[data-action="delegated-query"]'
            }).on('success.qp', function() {
                $('#entities-table').DataTable().draw('page');
            });

            //otvaranje modala
            $(document).on('click', 'button[data-action="delegated"]', function(e) {
                e.preventDefault();
                $('#comment').val('');

                    var queryId = $(this).data('id'); // Uzimamo query ID
                    var ajaxUrl = $(this).data('ajax-url'); // Uzimamo URL za AJAX

                    // Postavljanje forme u modal
                    $('#delegateForm').attr('action', ajaxUrl); // Set AJAX URL na formu
                    $('#queryId').val(queryId); // Postavljamo ID query-a u hidden input

                    // Ako želiš da dinamički popuniš selekt polje sa korisnicima putem AJAX-a
                    $.ajax({
                        url: "{{ route('google_search_console.get_users') }}", // API ili ruta koja vraća listu korisnika
                        method: 'GET',
                        success: function(response) {
                            var userSelect = $('#userSelect');
                            userSelect.empty(); // Brisanje starih opcija
                            $.each(response.users, function(index, user) {
                                $('#userSelect').append('<option value="' + user
                                    .id + '">' +
                                    user.first_name + ' ' + user.last_name +
                                    '</option>'
                                );
                            });

                        }
                    });

                    // Otvaranje modala
                    $('#delegateModal').modal('show');
            });

            $(document).on('click', '#submitDelegated', function(e) {
                e.preventDefault();

                var userId = $('#userSelect').val();
                var comment = $('#comment').val();
                var queryId = $('#queryId').val();

                $.ajax({
                    url: "{{ route('google_search_console.store_delegate') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: userId,
                        comment: comment,
                        query_id: queryId
                    },
                    success: function(response) {
                        $('#delegateModal').modal('hide');
                        ItemsDatatable.ajax.reload(null, true);
                    }
                });
            });

        });
    </script>
@endpush
