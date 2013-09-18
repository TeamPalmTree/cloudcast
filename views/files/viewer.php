<div class="cloudcast-section-sidebar-content" data-bind="with: file_viewer">
    <table>
        <tr>
            <td class="cloudcast-section-sidebar-content-toolbar">
                <div class="navbar">
                    <div class="navbar-inner">
                        <ul class="nav">
                            <li><a href="#">Files</a></li>
                        </ul>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="cloudcast-section-sidebar-content-bottom">
                <div class="cloudcast-section-sidebar-content-bottom-inner" data-bind="foreach: files">
                    <div class="cloudcast-section-sidebar-item">
                        <label data-bind="event: { contextmenu: show_info }">
                            <i class="icon-music"></i>
                            <strong><span class="cloudcast-popover" data-bind="text: title"></span></strong>
                        </label>
                        <div><i class="icon-user"></i> <span data-bind="text: artist"></span></div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>