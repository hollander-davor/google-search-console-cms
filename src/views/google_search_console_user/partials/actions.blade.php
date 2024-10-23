<div class="btn-group col-4">
    @if(isset($entity->delegated))
        <button type="button" class="btn btn-secondary" title="@lang('Send a comment')" data-title="@lang('Send a comment')"
            data-action="comment" data-id="{{ $query->id }}"
            data-ajax-url="{{ route('google_search_console_user.send_comment', ['query' => $query]) }}">
            <i class="fa fa-telegram" aria-hidden="true"></i>
        </button>
    @endif
    <a href="{{ route('google_search_console_user.pages', ['query' => $query]) }}" class="btn btn-info"
        title="@lang('See Pages')" target="_blank">
        <i class="fa fa-external-link"></i>
    </a>
    @if (isset($entity->slave_status) && $entity->slave_status == 3)
        <button disabled style="margin-left:10px" type="button"
            class="btn btn-success">{{ config('gsc-cms.done_query_text') }}</button>
    @endif
    @if (isset($entity->slave_status) && $entity->slave_status == 4)
        <button disabled style="margin-left:10px" type="button"
            class="btn btn-warning">{{ config('gsc-cms.delayed_query_text') }}</button>
    @endif
    @if (isset($entity->slave_status) && $entity->slave_status == 5)
        <button disabled style="margin-left:10px" type="button"
            class="btn btn-info">{{ config('gsc-cms.in_progress_query_text') }}</button>
    @endif
</div>
