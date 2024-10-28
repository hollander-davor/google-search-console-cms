<style>
    .modal .modal-dialog .modal-content .modal-header {
        background-color: unset !important;
    }
</style>
<div class="modal fade" id="delegateModal" tabindex="-1" role="dialog" aria-labelledby="delegateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="delegateModalLabel" style="color: #313a46 !important;">@lang('Delegate Query')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form fields inside modal -->
                    <form id="delegateForm" method="POST" action="">
                        @csrf
                        <div class="form-group">
                            <label for="userSelect">@lang('Select User')</label>
                            <select class="form-control" id="userSelect" name="user_id">

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
                    <button id="submitDelegated" type="submit" class="btn btn-primary"
                        form="delegateForm">@lang('Delegate')</button>
                </div>
            </div>
        </div>
    </div>