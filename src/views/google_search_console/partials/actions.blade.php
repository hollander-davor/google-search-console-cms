<div class="btn-group col-4">
    @if (isset($entity->excluded))
        @if ($entity->excluded == 0)
            <button type="button" class="btn btn-danger" title="@lang('Exclude')" data-title="@lang('Exclude')"
                data-action="exclude" data-id="{{ $entity->id }}" data-text="@lang('Are You sure that You want to exclude this query from future reports ')"
                data-label="{{ $entity->query }}"
                data-ajax-url="{{ route('google_search_console.toggle_exclude', ['query' => $query]) }}">
                <i class="fa fa-minus-circle"></i>
            </button>
        @elseif($entity->excluded == 1)
            <button type="button" class="btn btn-primary" title="@lang('Include')" data-title="@lang('Include')"
                data-action="exclude" data-id="{{ $entity->id }}" data-text="@lang('Are You sure that You want to include this query from future reports ')"
                data-label="{{ $entity->query }}"
                data-ajax-url="{{ route('google_search_console.toggle_exclude', ['query' => $query]) }}">
                <i class="fa fa-plus-circle"></i>
            </button>
        @endif
    @else
        <button type="button" class="btn btn-danger" title="@lang('Exclude')" data-title="@lang('Exclude')"
            data-action="exclude" data-id="{{ $query->id }}" data-text="@lang('Are You sure that You want to exclude this query from future reports ')"
            data-label="{{ $query->query }}"
            data-ajax-url="{{ route('google_search_console.new_status', ['status' => 'excluded', 'query' => $query]) }}">
            <i class="fa fa-minus-circle"></i>
        </button>
    @endif
    @if (isset($entity->fixed))
        @if ($entity->fixed == 0)
            <button type="button" class="btn btn-success" title="@lang('Mark as Fixed')" data-title="@lang('Mark as Fixed')"
                data-action="fixed" data-id="{{ $entity->id }}" data-text="@lang('Are You sure that You want to mark this query as FIXED in future reports ')"
                data-label="{{ $entity->query }}"
                data-ajax-url="{{ route('google_search_console.toggle_fixed', ['query' => $query]) }}">
                <i class="fa fa-check"></i>
            </button>
        @elseif($entity->fixed == 1)
            <button type="button" class="btn btn-warning" title="@lang('Unmark as Fixed')" data-title="@lang('Unmark as Fixed')"
                data-action="fixed" data-id="{{ $entity->id }}" data-text="@lang('Are You sure that You want to unmark this query as FIXED in future reports ')"
                data-label="{{ $entity->query }}"
                data-ajax-url="{{ route('google_search_console.toggle_fixed', ['query' => $query]) }}">
                <i class="fa fa-exclamation-triangle"></i>
            </button>
        @endif
    @else
        <button type="button" class="btn btn-success" title="@lang('Mark as Fixed')" data-title="@lang('Mark as Fixed')"
            data-action="fixed" data-id="{{ $query->id }}" data-text="@lang('Are You sure that You want to mark this query as FIXED in future reports ')"
            data-label="{{ $query->query }}"
            data-ajax-url="{{ route('google_search_console.new_status', ['status' => 'fixed', 'query' => $query]) }}">
            <i class="fa fa-check"></i>
        </button>
    @endif
    @if (isset($entity->delegated))
        @if ($entity->delegated == 0)
            <button type="button" class="btn btn-secondary" title="@lang('Delegate')" data-title="@lang('Delegate')"
                data-action="delegated" data-id="{{ $query->id }}" data-text="@lang('Are You sure that You want to DELEGATE this query in future reports ')"
                data-label="{{ $query->query }}"
                data-ajax-url="{{ route('google_search_console.toggle_delegate', ['query' => $query]) }}">
                <i class="fa fa-telegram" aria-hidden="true"></i>
            </button>
        @elseif($entity->delegated == 1)
            <button type="button" class="btn btn-danger" title="@lang('Dismiss')" data-title="@lang('Dismiss')"
                data-action="delegated-query" data-id="{{ $entity->id }}" data-text="@lang('Are You sure that You want to DISMISS this query in future reports ')"
                data-label="{{ $entity->query }}"
                data-ajax-url="{{ route('google_search_console.toggle_delegate', ['query' => $query]) }}">
                <i class="fa fa-exclamation-triangle"></i>
            </button>
        @endif
    @else
        <button type="button" class="btn btn-secondary" title="@lang('Delegate')" data-title="@lang('Delegate')"
            data-action="delegated" data-id="{{ $query->id }}" data-text="@lang('Are You sure that You want to DELEGATE this query in future reports ')"
            data-label="{{ $query->query }}"
            data-ajax-url="{{ route('google_search_console.new_status', ['status' => 'delegated', 'query' => $query]) }}">
            <i class="fa fa-telegram" aria-hidden="true"></i>
        </button>
    @endif
    <a href="{{ route('google_search_console.pages', ['query' => $query]) }}" class="btn btn-info"
        title="@lang('See Pages')" target="_blank">
        <i class="fa fa-external-link"></i>
    </a>
    @if ($critical == 1)
        <button disabled style="margin-left:10px" type="button"
            class="btn btn-info">{{ config('gsc-cms.critical_query_text') }}</button>
    @endif
    @if ($lhf == 1)
        <button disabled style="margin-left:10px" type="button"
            class="btn btn-info">{{ config('gsc-cms.lhf_query_text') }}</button>
    @endif
    @if (isset($entity->excluded) && $entity->excluded == 1)
        <button disabled style="margin-left:10px" type="button"
            class="btn btn-danger">{{ config('gsc-cms.excluded_query_text') }}</button>
    @endif
    @if (isset($entity->fixed) && $entity->fixed == 1)
        <button disabled style="margin-left:10px" type="button"
            class="btn btn-success">{{ config('gsc-cms.fixed_query_text') }}</button>
    @endif
    @if (isset($entity->delegated) && $entity->delegated == 1)
        <button disabled style="margin-left:10px" type="button"
            class="btn btn-success">{{ config('gsc-cms.delegated_query_text') }}</button>
    @endif

</div>
