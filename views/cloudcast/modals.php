<div id="cloudcast-modal-success" class="modal hide cloudcast-modal-success">
    <div class="modal-body">
        <?php echo implode('</p><p>', e((array) Session::get_flash('success'))); ?>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">OK</button>
    </div>
</div>
<div id="cloudcast-modal-error" class="modal hide cloudcast-modal-error">
    <div class="modal-body">
        <?php echo implode('</p><p>', e((array) Session::get_flash('error'))); ?>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">OK</button>
    </div>
</div>
<div id="cloudcast-modal-delete" class="modal hide cloudcast-modal-error">
    <div class="modal-body">
        Are you sure?
    </div>
    <div class="modal-footer">
        <button name="delete" class="btn btn-danger" aria-hidden="true">Delete</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
    </div>
</div>