<script>
    var schedule_dates_js = <?php echo Format::forge($schedule_dates)->to_json(); ?>
</script>
<div id="schedules-index" class="cloudcast-section">
    <table>
        <tr>
            <td class="cloudcast-section-sidebar">
                <?php echo $files_finder; ?>
            </td>
            <td class="cloudcast-section-content">
                <table>
                    <tr>
                        <td class="cloudcast-section-content-toolbar">
                            <div class="navbar">
                                <div class="navbar-inner">
                                    <div class="container">
                                        <a class="btn btn-navbar" data-toggle="collapse" data-target="#files-collapse">
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                        </a>
                                        <div id="files-collapse" class="nav-collapse collapse">
                                            <!-- ko if: !editing_schedule() -->
                                            <ul class="nav">
                                                <li><a href="#" data-bind="click: select_all"><i class="icon-check"></i></a></li>
                                                <li><a href="#" data-bind="click: deactivate"><i class="icon-ban-circle"></i> DEACTIVATE SCHEDULES</a></li>
                                            </ul>
                                            <ul class="nav pull-right">
                                                <li class="divider-vertical"></li>
                                                <li><a href="#">Selected Schedules: <span data-bind="text: selected_schedules_count"></span></a></li>
                                            </ul>
                                            <!-- /ko -->
                                            <!-- ko if: editing_schedule -->
                                            <ul class="nav">
                                                <li class="active">
                                                    <a href="#" data-bind="text: editing_schedule().show.title, click: focus_editing_schedule"></a>
                                                </li>
                                                <li><a href="#" data-bind="click: editing_schedule().select_all"><i class="icon-check"></i></a></li>
                                                <li><a href="#" data-bind="click: editing_schedule().remove"><i class="icon-ban-circle"></i> REMOVE SCHEDULE FILES</a></li>
                                            </ul>
                                            <ul class="nav pull-right">
                                                <li class="divider-vertical"></li>
                                                <li><a href="#">Selected Schedule Files: <span data-bind="text: editing_schedule().selected_schedule_files_count"></span></a></li>
                                            </ul>
                                            <!-- /ko -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="cloudcast-section-content-bottom">
                            <div class="cloudcast-section-content-bottom-content">
                                <div class="cloudcast-section-content-bottom-content-inner" data-bind="foreach: schedule_dates">
                                    <div class="cloudcast-super-header">
                                        <h4 data-bind="text: date"></h4>
                                    </div>
                                    <div data-bind="foreach: schedules">
                                        <div class="cloudcast-item" data-bind="css: { 'selected': selected }, click: function() { if (!$root.editing_schedule()) select(); }">
                                            <div class="cloudcast-item-section" data-bind="visible: !$root.editing_schedule()">
                                                <input type="checkbox" data-bind="checked: selected, click: function() { return true; }, clickBubble: false" />
                                            </div>
                                            <div class="cloudcast-item-time">
                                                <span class="cloudcast-item-time-start" data-bind="text: user_start_on_timeday"></span>
                                                <span class="cloudcast-item-time-end" data-bind="text: user_end_at_timeday"></span>
                                            </div>
                                            <div class="cloudcast-item-section">
                                                <a href="#" class="btn btn-mini" data-bind="attr: { title: expanded() ? 'Collapse' : 'Expand' }, click: expand_collapse, clickBubble: false">
                                                    <i class="icon-chevron-down" data-bind="css: { 'icon-chevron-down' : !expanded(), 'icon-chevron-up' : expanded }"></i>
                                                </a>
                                                <button title="Edit" href="#" class="btn btn-mini " data-bind="css: { 'btn-warning': editing }, hasFocus: focused, click: $root.edit_schedule, clickBubble: false">
                                                    <i class="icon-edit"></i>
                                                </button>
                                                <a href="#" class="btn btn-mini btn-primary" data-bind="visible: editing, click: $root.save_schedule, clickBubble: false">SAVE</a>
                                            </div>
                                            <div class="cloudcast-item-section">
                                                <strong><span data-bind="text: show.title"></span></strong>
                                            </div>
                                            <div class="cloudcast-item-section-right">
                                                <div class="cloudcast-item-section-right-section">
                                                    <span class="label" data-bind="visible: jingles_album"><i class="icon-bell"></i> JINGLES</span>
                                                    <span class="label" data-bind="visible: bumpers_album"><i class="icon-bell"></i> BUMPERS</span>
                                                    <span class="label" data-bind="visible: sweepers_album"><i class="icon-bell"></i> SWEEPERS</span>
                                                </div>
                                                <div class="cloudcast-item-section-right-section">
                                                    <span class="label label-info"><i class="icon-music"></i> <span data-bind="text: schedule_files().length"></span> FILES</span>
                                                    <span class="label label-info"><i class="icon-time"></i> <span data-bind="text: total_duration"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- ko if: expanded -->
                                        <div data-bind="sortable: { data: schedule_files, options: { cancel: '.no-sort'}}">
                                            <div class="cloudcast-item"
                                                 data-bind="css: { 'no-sort': !$parent.editing() || played_on() || (queued() == '1'), 'selected': selected }, click: function() { if ($parent.editing() && !played_on() && (queued() == '0')) select(); }">
                                                <div class="cloudcast-item-section" data-bind="visible: $parent.editing() && !played_on() && (queued() == '0')">
                                                    <input type="checkbox" data-bind="checked: selected, click: function() { return true; }, clickBubble: false" />
                                                </div>
                                                <div class="cloudcast-item-section" data-bind="visible: queued() == '1' || played_on()">
                                                    <span class="label label-warning" data-bind="visible: queued() == '1'"><i class="icon-download-alt"></i> QUEUED</span>
                                                    <span class="label label-success" data-bind="visible: played_on"><i class="icon-ok"></i> PLAYED</span>
                                                </div>
                                                <div class="cloudcast-item-section">
                                                    <strong><span data-bind="text: file().artist"></span></strong> - <strong><span data-bind="text: file().title"></span></strong>
                                                </div>
                                                <div class="cloudcast-item-section-right">
                                                    <span class="label label-info"><i class="icon-music"></i> <span data-bind="text: file().genre"></span> (<span data-bind="text: file().key"></span>-<span data-bind="text: file().energy"></span>)</span>
                                                    <span class="label label-info"><i class="icon-time"></i> <span data-bind="text: file().duration"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /ko -->
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                 </table>
            </td>
        </tr>
    </table>
</div>