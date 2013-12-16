<div id="block-layout" class="standard-section" data-bind="if: block">
    <?php echo $blocks_finder; ?>
    <?php echo $files_finder; ?>
    <div class="standard-section-toolbar">
        <nav class="navbar navbar-default">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#block-layout-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div id="block-layout-collapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a data-bind="text: 'Layout ' + block().title()"></a></li>
                    <li>
                        <button title="Select All" class="btn btn-default navbar-btn" data-bind="click: select_all"><span class="glyphicon glyphicon-check"></span> <span data-bind="text: selected_block_items_count"></span></button>
                        <button title="Remove" class="btn btn-default navbar-btn" data-bind="click: remove"><span class="glyphicon glyphicon-minus-sign"></span></button>
                        <span class="label"><span class="glyphicon glyphicon-time"></span></span> <span data-bind="text: total_duration"></span>
                        <span class="label"><strong>%</strong></span> <span data-bind="text: total_percentage"></span>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right" data-bind="visible: !sidebar()">
                    <li>
                        <button title="Choose Blocks" class="btn btn-default navbar-btn" data-bind="click: function() { sidebar('blocks'); }"><span class="glyphicon glyphicon-th-large"></span></button>
                        <button title="Choose Files" class="btn btn-default navbar-btn" data-bind="click: function() { sidebar('files'); }"><span class="glyphicon glyphicon-folder-open"></span></button>
                        <button title="Cancel" class="btn btn-default navbar-btn" data-bind="click: cancel"><span class="glyphicon glyphicon-floppy-remove"></span></button>
                        <button title="Save" class="btn btn-default navbar-btn" data-bind="css: { 'active': saving }, click: save"><span class="glyphicon glyphicon-floppy-saved"></span></button>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <div class="standard-section-content">
        <div class="standard-section-inner">
            <div data-bind="validate: $root.errors, validateFor: 'total_percentage'"></div>
            <div style="min-height: 50px;" data-bind="sortable: { data: block().block_items, dragged: $root.create_block_item }">
                <div class="standard-item" data-bind="css: { 'selected': selected }, click: function() { select(); }">
                    <div class="standard-item-checkbox">
                        <input type="checkbox" class="checkbox-inline" data-bind="checked: selected, click: function() { return true; }, clickBubble: false" />
                    </div>
                    <div class="standard-item-content">
                        <div class="standard-item-header">
                            <div class="standard-item-title">
                                <!-- ko if: file -->
                                <span class="label"><span class="glyphicon glyphicon-music"></span></span>
                                <span data-bind="text: file().artist"></span> - <span data-bind="text: file().title"></span>
                                <!-- /ko -->
                                <!-- ko if: child_block -->
                                <span class="label"><span class="glyphicon glyphicon-stop"></span></span>
                                <span data-bind="text: child_block().title"></span>
                                <!-- /ko -->
                            </div>
                        </div>
                        <div class="standard-item-footer">
                            <div class="standard-item-info">
                                <!-- ko if: file -->
                                <span class="label label-primary"><span class="glyphicon glyphicon-time"></span> <span data-bind="text: file().duration"></span></span>
                                <!-- /ko -->
                                <!-- ko if: child_block -->
                                <div class="standard-item-form">
                                    <span class="label"><span class="glyphicon glyphicon-time"></span></span>
                                    <input type="text" class="form-control" placeholder="HH:MM:SS"
                                           data-bind="nowValue: entered_duration, attr: { name: 'block_items[' + $index() + '][duration]' }, css: { active: entered_duration }, click: function() { return false; }, clickBubble: false" />
                                    <span class="label"><strong>%</strong></span>
                                    <input type="text" class="form-control"
                                           data-bind="nowValue: entered_percentage, attr: { name: 'block_items[' + $index() + '][percentage]' }, css: { active: entered_percentage }, click: function() { return false; }, clickBubble: false" />
                                    <div data-bind="validate: $root.errors, validateFor: 'block_items[' + $index() + '][percentage_duration]'"></div>
                                    <div data-bind="validate: $root.errors, validateFor: 'block_items[' + $index() + '][duration]'"></div>
                                    <div data-bind="validate: $root.errors, validateFor: 'block_items[' + $index() + '][percentage]'"></div>
                                </div>
                                <!-- /ko -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>