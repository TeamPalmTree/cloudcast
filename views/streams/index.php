<script>
    var streams_js = <?php echo Format::forge($streams)->to_json(); ?>
</script>
<div id="streams-index" class="cloudcast-section">
    <div class="cloudcast-section-inner" data-bind="foreach: streams">
        <div class="cloudcast-item">
            <div class="cloudcast-item-section">
                <a title="Deactivate" class="btn btn-mini btn-warning" href="#" data-bind="visible: active() == '1', click: deactivate"><i class="icon-ban-circle"></i></a>
                <a title="Activate" class="btn btn-mini btn-primary" href="#" data-bind="visible: active() == '0', click: activate"><i class="icon-ok-sign"></i></a>
            </div>
            <div class="cloudcast-item-section">
                <a title="Delete" class="btn btn-mini btn-danger" href="#" data-bind="click: $parent.delete"><i class="icon-remove"></i></a>
            </div>
            <div class="cloudcast-item-section">
                <a class="btn btn-mini" href="#" data-bind="attr: { href: 'streams/edit/' + id() }"><i class="icon-edit"></i></a>
            </div>
            <div class="cloudcast-item-section">
                <strong data-bind="text: name"></strong>
            </div>
            <div class="cloudcast-item-section">
                <span class="badge badge-info"><i class="icon-volume-up"></i> <span data-bind="text: type_name"></span></span>
            </div>
        </div>
    </div>
</div>