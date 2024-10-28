@extends('_layout.layout')

@section('head_title', __('Google Search Console'))

@push('head_links')
@endpush

@section('content')

    {{-- this is example breadcrumbs with website picker --}}
    @include('_layout.partials.breadcrumbs', [
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
    <script>
        $(document).ready(function() {
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
