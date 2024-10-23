<div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel">@lang('Send a comment')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form fields inside modal -->
                    <form id="commentForm" method="POST" action="">
                        @csrf
                        <div class="form-group">
                            <label for="statusSelect">@lang('Select status')</label>
                            <select class="form-control" id="statusSelect" name="status">
                                <option selected value="3">@lang('Done')</option>
                                <option value="4">@lang('Delayed')</option>
                                <option value="5">@lang('In progress')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="comment">@lang('Comment')</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                        </div>
                        <input type="hidden" id="queryId" name="query_id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
                    <button id="submitComment" type="submit" class="btn btn-primary"
                        form="commentForm">@lang('Send')</button>
                </div>
            </div>
        </div>
    </div>