<div data-bind="with: file_finder">
    <div class="cloudcast_header">
        <h4>Files</h4>
        <div class="cloudcast_header-right">
            <a title="Clear" class="btn btn-mini btn-danger" href="#" data-bind="click: clear"><i class="icon-remove"></i></a>
            <a title="Check All" class="btn btn-mini" href="#" data-bind="click: toggle_select"><i class="icon-ok"></i></a>
            <a title="Add" class="btn btn-mini" href="#" data-bind="click: $parent.add_selected_files"><i class="icon-arrow-right"></i></a>
        </div>
    </div>
    <?php echo Form::textarea('query', null, array('class' => 'cloudcast_sidebar-query', 'rows' => '3', 'placeholder' => 'Query', 'data-bind' => "value: query, valueUpdate: 'afterkeydown'" )); ?>
    <div data-bind="foreach: files">
        <div class="cloudcast_sidebar-item">
            <label class="checkbox" data-bind="popover: { title: title, content: description, container: 'body', trigger: 'hover', placement: 'right', html: true, delay: 1000 }">
                <input type="checkbox" data-bind="checked: selected">
                <strong><span class="cloudcast_popover" data-bind="text: title"></span></strong>
            </label>
            <div><i class="icon-user"></i> <span data-bind="text: artist"></span></div>
            <div><i class="icon-time"></i> <span data-bind="text: duration"></span></div>
        </div>
    </div>
</div>