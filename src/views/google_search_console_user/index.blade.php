@extends('_layout.layout')

@section('head_title', __('Google Search Console'))

@push('head_links')
@endpush

@section('content')

    {{-- this is example breadcrumbs with website picker --}}
    @include('_layout.partials.breadcrumbs', [
        'pageTitle' => __('Google Search Console'),
        'websitePicker' => 1,
        'routeName' => 'google_search_console_user.index',
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
                                <label class="control-label">@lang('Filter by status')</label>
                                <select id="select-status" class="form-control" name='status'>
                                    <option selected value="all">@lang('All')</option>
                                    <option value="done">@lang('Done')</option>
                                    <option value="delayed">@lang('Delayed')</option>
                                    <option value="in_progress">@lang('In progress')</option>
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
                                    <th>@lang('Delegated by')</th>
                                    <th>@lang('Comment')</th>
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
    @include('google_search_console_user.partials.comment_modal.modal')
@endsection

@push('footer_scripts')
    <script>
        $(document).ready(function() {
            let ItemsDatatable = $('#entities-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "@route('google_search_console_user.datatable')",
                    type: "POST",
                    data: function(dtData) {
                        dtData["_token"] = "{{ csrf_token() }}";
                        dtData["activeWebsite"] = "{{ $activeWebsite }}"
                        dtData["status"] = $('#select-status').val()
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
                        "data": "delegated_by",
                        "orderable": true
                    },
                    {
                        "data": "comment",
                        "orderable": true
                    },
                    {
                        "data": "actions",
                        "orderable": false
                    },
                ]
            });

            $('#select-status').on('change', function(e) {
                e.preventDefault();
                ItemsDatatable.ajax.reload(null, true);
            });

            //otvaranje modala
            $(document).on('click', 'button[data-action="comment"]', function(e) {
                e.preventDefault();
                $('#comment').val('');

                    var queryId = $(this).data('id'); // Uzimamo query ID
                    var ajaxUrl = $(this).data('ajax-url'); // Uzimamo URL za AJAX

                    // Postavljanje forme u modal
                    $('#commentForm').attr('action', ajaxUrl); // Set AJAX URL na formu
                    $('#queryId').val(queryId); // Postavljamo ID query-a u hidden input

                    // Otvaranje modala
                    $('#commentModal').modal('show');
            });

            $(document).on('click', '#submitComment', function(e) {
                e.preventDefault();

                var statusId = $('#statusSelect').val();
                var comment = $('#comment').val();
                var queryId = $('#queryId').val();

                $.ajax({
                    url: "{{ route('google_search_console_user.store_comment') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status_id: statusId,
                        comment: comment,
                        query_id: queryId
                    },
                    success: function(response) {
                        $('#commentModal').modal('hide');
                        ItemsDatatable.ajax.reload(null, true);
                    }
                });
            });

        });
    </script>
@endpush
