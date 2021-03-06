<div class="standard-sidebar" data-bind="visible: sidebar() == 'files', with: file_viewer">
    <div class="standard-sidebar-toolbar">
        <nav class="navbar navbar-default">
            <ul class="nav navbar-nav navbar-right">
                <li><button title="Close Files" class="btn btn-default navbar-btn" data-bind="click: function() { $root.sidebar(null); }"><span class="glyphicon glyphicon-remove"></span></button></li>
            </ul>
        </nav>
    </div>
    <div class="standard-sidebar-content" data-bind="foreach: files">
        <div class="standard-sidebar-item" data-bind="event: { contextmenu: show_info }">
            <div class="standard-sidebar-item-title"><span data-bind="text: title"></span></div>
            <div><span class="glyphicon glyphicon-user"></span> <span data-bind="text: artist"></span></div>
        </div>
    </div>
</div>
