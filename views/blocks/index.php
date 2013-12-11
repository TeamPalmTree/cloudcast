<div id="blocks-index" class="cloudcast-section">
    <div class="cloudcast-section-toolbar">
        <nav class="navbar navbar-default">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#blocks-index-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div id="blocks-index-collapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li>
                        <button title="Select All" class="btn btn-default navbar-btn" data-bind="click: select_all"><span class="glyphicon glyphicon-check"></span> <span data-bind="text: selected_blocks_count"></span></button>
                        <button title="Delete" class="btn btn-default navbar-btn" data-bind="click: delete_block"><span class="glyphicon glyphicon-minus-sign"></span></button>
                        <button title="Create" class="btn btn-default navbar-btn" data-bind="click: create"><span class="glyphicon glyphicon-plus-sign"></span></button>
                    <li>
                </ul>
            </div>
        </nav>
    </div>
    <div class="cloudcast-section-content">
        <div class="cloudcast-section-inner" data-bind="foreach: blocks">
            <div class="cloudcast-item" data-bind="css: { 'selected': selected }, click: function() { select(); }">
                <div class="cloudcast-item-checkbox">
                    <input type="checkbox" data-bind="checked: selected, click: function() { return true; }, clickBubble: false" />
                </div>
                <div class="cloudcast-item-content">
                    <div class="cloudcast-item-header">
                        <div class="cloudcast-item-controls">
                            <button title="Edit" class="btn btn-default btn-xs" data-bind="click: edit"><span class="glyphicon glyphicon-edit"></span></button>
                            <button title="Layout" class="btn btn-default btn-xs" data-bind="click: layout"><span class="glyphicon glyphicon-list"></span></button>
                        </div>
                        <div class="cloudcast-item-title">
                            <span data-bind="text: title"></span> <span class="text-muted" data-bind="text: description"></span>
                        </div>
                    </div>
                    <div class="cloudcast-item-footer">
                        <div class="cloudcast-item-info">
                            <span class="label label-primary" data-bind="visible: weighted"><span class="glyphicon glyphicon-tasks"></span> WEIGHTED</span>
                            <span class="label label-primary" data-bind="visible: weighted"><span class="glyphicon glyphicon-list"></span> ITEMIZED</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>