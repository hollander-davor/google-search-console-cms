<div class="btn-group col-4 ">
        <button
            type="button"
            class="btn btn-secondary"
            title="@lang('Exclude')"
            data-title="@lang('Exclude')"
            data-action="exclude"
            data-id="{{$entity->id}}"
            data-text="@lang('Are You sure that You want to exclude this query from future reports ')"
            data-label="{{$entity->query}}"
            data-ajax-url="{{route('google_search_console.exclude', ['query' => $entity])}}"
        >
            <i class="fa fa-minus-circle"></i>
        </button>
        <a href="{{route('google_search_console.pages', ['query' => $entity])}}"
            class="btn btn-warning"
            title="@lang('See Pages')"
        >
                <i class="fa fa-external-link"></i>
            </a>

</div>
