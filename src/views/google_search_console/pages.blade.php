@extends('_layout.layout')

@section('head_title', __('Google Search Console'))

@push('head_links')
@endpush

@section('content')

    {{-- this is example breadcrumbs with website picker --}}
    @include('google_search_console.partials.breadcrumbs', [
        'pageTitle' => __('Google Search Console'),
        'websitePicker' => 0,
        'routeName' => 'google_search_console.index',
    ])
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-heading d-flex justify-content-between">
                    <div class="card-heading-title">
                        <h3 class="card-title">@lang('Google Search Console - Query Pages for '){{ $query->query }}</h3>
                    </div>
                </div>
                <div class="card-body">
                    <table id="entities-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>@lang('Pages')</th>
                                <th>@lang('Clicks')</th>
                                <th>@lang('Impressions')</th>
                                <th>@lang('CTR')</th>
                                <th>@lang('Position')</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('footer_scripts')
    <script>
        $(document).ready(function() {
            let ItemsDatatable = $('#entities-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "@route('google_search_console.pages_datatable')",
                    type: "POST",
                    data: function(dtData) {
                        dtData["_token"] = "{{ csrf_token() }}";
                        dtData["query_id"] = "{{ $query->id }}"
                    }
                },
                "columns": [{
                        "data": "page",
                        "orderable": false,
                        "render": function(data, type, row, meta) {
                            if (type === 'display') {
                                data = '<a href="' + data + '" target="_blanc">' + data + '</a>';
                            }

                            return data;
                        }
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
                ]
            });

        });
    </script>
@endpush
