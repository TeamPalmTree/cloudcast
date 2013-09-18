<div class="cloudcast-section-sidebar-content" data-bind="with: file_finder">
    <table>
        <tr>
            <td class="cloudcast-section-sidebar-content-toolbar">
                <div class="navbar">
                    <div class="navbar-inner">
                        <ul class="nav">
                            <li><a href="#">Files</a></li>
                        </ul>
                        <ul class="nav pull-right">
                            <li><a href="#" data-bind="click: clear"><i class="icon-remove"></i></a></li>
                            <li><a href="#" data-bind="click: select_all"><i class="icon-ok"></i></a></li>
                            <li><a href="#" data-bind="click: $root.add_files"><i class="icon-arrow-right"></i></a></li>
                        </ul>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="cloudcast-section-sidebar-content-query">
                <?php echo Form::textarea('query', null, array('rows' => '3', 'placeholder' => 'Query', 'data-bind' => "value: query, valueUpdate: 'afterkeydown'" )); ?>
            </td>
        </tr>
        <tr>
            <td class="cloudcast-section-sidebar-content-bottom">
                <div class="cloudcast-section-sidebar-content-bottom-inner">
                    <div data-bind="foreach: files">
                        <div class="cloudcast-section-sidebar-item">
                            <label class="checkbox" data-bind="event: { contextmenu: show_info }"">
                                <input type="checkbox" data-bind="checked: selected">
                                <strong><span class="cloudcast-popover" data-bind="text: title"></span></strong>
                            </label>
                            <div><i class="icon-user"></i> <span data-bind="text: artist"></span></div>
                            <div><i class="icon-time"></i> <span data-bind="text: duration"></span></div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>
