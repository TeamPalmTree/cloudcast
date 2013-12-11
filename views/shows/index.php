<div id="shows-index" class="cloudcast-section">
    <div class="cloudcast-section-toolbar">
        <nav class="navbar navbar-default">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#shows-index-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div id="shows-index-collapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li>
                        <button title="Select All (Single)" class="btn btn-default navbar-btn" data-bind="click: select_all_single"><span class="glyphicon glyphicon-check"></span> S<span data-bind="text: selected_single_shows_count"></span></button>
                        <button title="Select All (Repeat)" class="btn btn-default navbar-btn" data-bind="click: select_all_repeat"><span class="glyphicon glyphicon-check"></span> R<span data-bind="text: selected_repeat_shows_count"></span></button>
                        <button title="Deactivate" class="btn btn-default navbar-btn" data-bind="click: deactivate"><span class="glyphicon glyphicon-minus-sign"></span></button>
                        <button title="Create" class="btn btn-default navbar-btn" data-bind="click: create"><span class="glyphicon glyphicon-plus-sign"></span></button>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <div class="cloudcast-section-content">
        <div class="cloudcast-section-inner">
            <div class="cloudcast-super-header">
                <h4>Single Shows</h4>
            </div>
            <div data-bind="template: { name: 'show-template', foreach: single_shows }"></div>
            <div class="cloudcast-super-header">
                <h4>Repeat Shows</h4>
            </div>
            <div data-bind="foreach: repeat_days">
                <div class="cloudcast-super-header">
                    <h5 data-bind="text: day"></h5>
                </div>
                <div data-bind="template: { name: 'show-template', foreach: shows }"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/html" id="show-template">
    <div class="cloudcast-item" data-bind="css: { 'selected': selected }, click: function() { select(); }">
        <div class="cloudcast-item-checkbox">
            <input type="checkbox" data-bind="checked: selected, click: function() { return true; }, clickBubble: false" />
        </div>
        <div class="cloudcast-item-content">
            <div class="cloudcast-item-header">
                <div class="cloudcast-item-time">
                    <span class="cloudcast-item-time-start" data-bind="text: show_full_date() ? user_start_on_timeday() : user_start_on_time()"></span>
                    <span class="cloudcast-item-time-end" data-bind="text: show_full_date() ? user_end_at_timeday() : user_end_at_time()"></span>
                </div>
                <div class="cloudcast-item-controls">
                    <button title="Edit" class="btn btn-default btn-xs" data-bind="click: edit"><span class="glyphicon glyphicon-edit"></span></button>
                    <!-- ko if: block -->
                    <button title="Edit Block" class="btn btn-default btn-xs" data-bind="click: edit_block"><span class="glyphicon glyphicon-edit"></span><span class="glyphicon glyphicon-stop"></span></button>
                    <button title="Layout Block" class="btn btn-default btn-xs" data-bind="click: layout_block"><span class="glyphicon glyphicon-list"></span><span class="glyphicon glyphicon-stop"></span></button>
                    <!-- /ko -->
                </div>
            </div>
            <div class="cloudcast-item-footer">
                <div class="cloudcast-item-title">
                    <span data-bind="text: title"></span>
                </div>
                <div class="cloudcast-item-info">
                    <!-- ko if: !block() -->
                    <span class="label label-warning"><span class="glyphicon glyphicon-warning-sign"></span> NO BLOCK</span>
                    <!-- /ko -->
                    <!-- ko if: block -->
                    <span class="label label-primary"><span class="glyphicon glyphicon-stop"></span> <span data-bind="text: block().title"></span></span>
                    <!-- /ko -->
                    <span class="label label-primary" data-bind="visible: hosted"><span class="glyphicon glyphicon-headphones"></span> HOSTED</span>
                    <span class="label" data-bind="visible: jingles_album"><span class="glyphicon glyphicon-bell"></span> JINGLES</span>
                    <span class="label" data-bind="visible: bumpers_album"><span class="glyphicon glyphicon-bell"></span> BUMPERS</span>
                    <span class="label" data-bind="visible: sweepers_album"><span class="glyphicon glyphicon-bell"></span> SWEEPERS</span>
                </div>
            </div>
        </div>
    </div>
</script>