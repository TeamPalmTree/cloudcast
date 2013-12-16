<div class="standard-sidebar" data-bind="visible: sidebar() == 'blocks', with: block_finder">
    <div class="standard-sidebar-toolbar">
        <nav class="navbar navbar-default">
            <ul class="nav navbar-nav navbar-right">
                <li><button title="Close Blocks" class="btn btn-default navbar-btn" data-bind="click: function() { $root.sidebar(null); }"><span class="glyphicon glyphicon-remove"></span></button></li>
            </ul>
        </nav>
    </div>
    <div class="standard-sidebar-content" data-bind="foreach: blocks">
        <div class="standard-sidebar-item" data-bind="draggable: { data: $data, options: { appendTo: 'body', zIndex: 2, cursor: 'move', cursorAt: { left: 0, top: 0 }}}">
            <div class="standard-sidebar-item-title-move"><span data-bind="text: title"></span></div>
            <div><span data-bind="text: description"></span></div>
        </div>
    </div>
</div>