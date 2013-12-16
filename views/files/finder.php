<div class="standard-sidebar" data-bind="visible: sidebar() == 'files', with: file_finder">
    <div class="standard-sidebar-toolbar">
        <nav class="navbar navbar-default">
            <ul class="nav navbar-nav">
                <li><button title="Add All Files" class="btn btn-default navbar-btn" data-bind="click: function() { $root.add_all_files() };"><span class="glyphicon glyphicon-backward"></span></button></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><button title="Close Files" class="btn btn-default navbar-btn" data-bind="click: function() { $root.sidebar(null); }"><span class="glyphicon glyphicon-remove"></span></button></li>
            </ul>
        </nav>
        <textarea class="form-control" rows="3" placeholder="Query" data-bind="nowValue: query"></textarea>
    </div>
    <div class="standard-sidebar-content" data-bind="foreach: files">
        <div class="standard-sidebar-item" data-bind="draggable: { data: $data, options: { appendTo: 'body', zIndex: 2, cursor: 'move', cursorAt: { left: 0, top: 0 }}}, event: { contextmenu: show_info }">
            <div class="standard-sidebar-item-title-move"><span data-bind="text: title"></span></div>
            <div><span class="glyphicon glyphicon-user"></span> <span data-bind="text: artist"></span></div>
            <div><span class="glyphicon glyphicon-time"></span> <span data-bind="text: duration"></span></div>
        </div>
    </div>
</div>
