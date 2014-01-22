<div id="schedules-index" class="standard-section">
    <?php echo $file_finder; ?>
    <div class="standard-section-toolbar">
        <nav class="navbar navbar-default">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#schedules-index-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div id="schedules-index-collapse" class="collapse navbar-collapse">
                <!-- ko if: !editing_schedule() -->
                <ul class="nav navbar-nav">
                    <li>
                        <button title="Select All" class="btn btn-default navbar-btn" data-bind="click: select_all"><span class="glyphicon glyphicon-check"></span> <span data-bind="text: selected_schedules_count"></span></button>
                        <button title="Deactivate" class="btn btn-default navbar-btn" data-bind="click: deactivate"><span class="glyphicon glyphicon-minus-sign"></span></button>
                        <button title="Generate" class="btn btn-default navbar-btn" data-bind="css: { active: generating }, click: generate"><span class="glyphicon glyphicon-retweet"></span></button>
                        <button title="Auto Refresh" class="btn btn-default navbar-btn" data-bind="css: { active: auto_refresh }, click: toggle_auto_refresh"><span class="glyphicon glyphicon-refresh"></span></button>
                        <button title="Auto Focus" class="btn btn-default navbar-btn" data-bind="css: { active: auto_focus }, visible: auto_refresh, click: toggle_auto_focus"><span class="glyphicon glyphicon-facetime-video"></span></button>
                    </li>
                </ul>
                <!-- /ko -->
                <!-- ko if: editing_schedule -->
                <ul class="nav navbar-nav">
                    <li class="active"><a data-bind="text: editing_schedule().show.title, click: focus_editing_schedule"></a></li>
                    <li>
                        <button title="Select All" class="btn btn-default navbar-btn" data-bind="click: editing_schedule().select_all"><span class="glyphicon glyphicon-check"></span> <span data-bind="text: editing_schedule().selected_schedule_files_count"></span></button>
                        <button title="Remove" class="btn btn-default navbar-btn" data-bind="click: editing_schedule().remove"><span class="glyphicon glyphicon-minus-sign"></span></button>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right" data-bind="visible: !sidebar()">
                    <li>
                        <button title="Choose Files" class="btn btn-default navbar-btn" data-bind="click: function() { sidebar('files'); }"><span class="glyphicon glyphicon-folder-open"></span></button>
                        <button title="Cancel" class="btn btn-default navbar-btn" data-bind="click: cancel_edit_schedule"><span class="glyphicon glyphicon-floppy-remove"></span></button>
                        <button title="Save" class="btn btn-default navbar-btn" data-bind="css: { 'active': saving }, click: save_schedule"><span class="glyphicon glyphicon-floppy-saved"></span></button>
                    </li>
                </ul>
                <!-- /ko -->
            </div>
        </nav>
    </div>
    <div class="standard-section-content">
        <div class="standard-section-inner" data-bind="foreach: schedule_dates">
            <h4 data-bind="text: date"></h4>
            <div data-bind="foreach: schedules">
                <div class="standard-item" data-bind="css: { 'selected': selected }, click: function() { if (!$root.editing_schedule() && !static()) select(); }">
                    <div class="standard-item-checkbox" data-bind="visible: !$root.editing_schedule() && !static()">
                        <input type="checkbox" class="checkbox-inline" data-bind="checked: selected, click: function() { return true; }, clickBubble: false" />
                    </div>
                    <div class="standard-item-content">
                        <div class="standard-item-header">
                            <div class="standard-item-time">
                                <span class="standard-item-time-start" data-bind="text: user_start_on_timeday"></span>
                                <span class="standard-item-time-end" data-bind="text: user_end_at_timeday"></span>
                            </div>
                            <div class="standard-item-controls">
                                <a class="btn btn-default btn-xs" data-bind="attr: { title: expanded() ? 'Collapse' : 'Expand' }, click: expand_collapse, clickBubble: false">
                                    <span class="glyphicon glyphicon-chevron-down" data-bind="css: { 'glyphicon-chevron-down' : !expanded(), 'glyphicon-chevron-up' : expanded }"></span>
                                </a>
                                <button title="Edit" class="btn btn-default btn-xs " data-bind="visible: !editing(), hasFocus: focused, click: $root.edit_schedule, clickBubble: false">
                                    <span class="glyphicon glyphicon-edit"></span>
                                </button>
                            </div>
                        </div>
                        <div class="standard-item-footer">
                            <div class="standard-item-title">
                                <strong><span data-bind="text: show.title"></span> (<span data-bind="text: id"></span>)</strong>
                            </div>
                            <div class="standard-item-info">
                                <span class="label" data-bind="visible: show.jingles_album"><span class="glyphicon glyphicon-bell"></span> JINGLES</span>
                                <span class="label" data-bind="visible: show.hosted"><span class="glyphicon glyphicon-bell"></span> HOSTED</span>
                                <span class="label" data-bind="visible: show.intros_album"><span class="glyphicon glyphicon-bell"></span> INTROS</span>
                                <span class="label" data-bind="visible: show.closers_album"><span class="glyphicon glyphicon-bell"></span> CLOSERS</span>
                                <span class="label label-primary"><span class="glyphicon glyphicon-music"></span> <span data-bind="text: schedule_files().length"></span> FILES</span>
                                <span class="label label-primary"><span class="glyphicon glyphicon-time"></span> <span data-bind="text: total_duration"></span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ko if: expanded -->
                <div data-bind="sortable: { data: schedule_files, dragged: $root.create_schedule_file, isEnabled: editing, options: { cancel: '.static'}}">
                    <div class="standard-item" data-bind="css: { 'static': static, 'selected': selected }, scrollTo: focused, click: function() { if ($parent.editing() && !static()) select(); }">
                        <div class="standard-item-checkbox" data-bind="visible: $parent.editing() && !static()">
                            <input type="checkbox" data-bind="checked: selected, click: function() { return true; }, clickBubble: false" />
                        </div>
                        <div class="standard-item-content">
                            <div class="standard-item-title">
                                <!-- ko if: static -->
                                <span class="label label-warning" data-bind="visible: queued_on() && !played_on()"><span class="glyphicon glyphicon-download-alt"></span> QUEUED</span>
                                <span class="label label-success" data-bind="visible: played_on"><span class="glyphicon glyphicon-ok"></span> PLAYED</span>
                                <span class="label label-important" data-bind="visible: skipped_on"><span class="glyphicon glyphicon-remove"></span> SKIPPED</span>
                                <!-- /ko -->
                                <span data-bind="css: 'text-' + color()">
                                    <span data-bind="text: file().artist"></span> - <span data-bind="text: file().title"></span>
                                    <!-- ko if: id -->
                                    (<span data-bind="text: id"></span>)
                                    <!-- /ko -->
                                </span>
                            </div>
                            <div class="standard-item-info">
                                <span class="label label-primary">
                                    <span class="glyphicon glyphicon-music"></span>
                                    <span data-bind="text: file().genre"></span>
                                    <span data-bind="visible: file().key() && file().energy()">
                                     (<span data-bind="text: file().key"></span>-<span data-bind="text: file().energy"></span>)
                                    </span>
                                </span>
                                <!-- ko if: file().post -->
                                <span class="label label-warning"><span class="glyphicon glyphicon-signal"></span> <span data-bind="text: file().post"></span></span>
                                <!-- /ko -->
                                <span class="label label-primary"><span class="glyphicon glyphicon-time"></span> <span data-bind="text: file().duration"></span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /ko -->
            </div>
        </div>
    </div>
</div>