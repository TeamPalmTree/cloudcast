<div class="cloudcast-sidebar" data-bind="visible: sidebar() == 'blocks', with: block_finder">
    <div class="cloudcast-sidebar-toolbar">
        <nav class="navbar navbar-default">
            <ul class="nav navbar-nav navbar-right">
                <li><button title="Close Blocks" class="btn btn-default navbar-btn" data-bind="click: function() { $root.sidebar(null); }"><span class="glyphicon glyphicon-remove"></span></button></li>
            </ul>
        </nav>
    </div>
    <div class="cloudcast-sidebar-content" data-bind="foreach: blocks">
        <div class="cloudcast-sidebar-item" data-bind="draggable: { data: $data, options: { appendTo: 'body', zIndex: 2, cursor: 'move', cursorAt: { left: 0, top: 0 }}}">
            <div class="cloudcast-sidebar-item-title-move"><span data-bind="text: title"></span></div>
            <div><span data-bind="text: description"></span></div>
        </div>
    </div>
</div>