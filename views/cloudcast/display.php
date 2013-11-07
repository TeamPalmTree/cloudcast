<div id="cloudcast-display" class="cloudcast-display" data-bind="with: status">
    <div class="cloudcast-display-left">
        <div class="cloudcast-display-file">
            <div class="cloudcast-display-block-inner">
                <div><strong>CURRENT SONG</strong> <span data-bind="text: current_file_artist"></span> - <span data-bind="text: current_file_title"></span></div>
                <div class="progress progress-striped cloudcast-display-progress">
                    <div class="bar bar-warning" data-bind="style: { width: current_file_percentage() + '%' }"></div>
                    <div class="cloudcast-display-progress-post" data-bind="style: { width: current_file_post_percentage() + '%' }"></div>
                    <div class="cloudcast-display-progress-elapsed">
                        <a href="#" data-bind="text: current_file_post, visible: current_file_post, click: $parent.set_post"></a>
                        <a href="#" data-bind="visible: !current_file_post(), click: $parent.set_post">SET POST</a>
                        / <span data-bind="text: current_file_elapsed"></span> / <span data-bind="text: current_file_duration"></span>
                    </div>
                    <div class="cloudcast-display-progress-remaining"><span data-bind="text: current_file_remaining"></span></div>
                </div>
                <div><strong>NEXT SONG</strong> <span data-bind="text: next_file_artist"></span> - <span data-bind="text: next_file_title"></span></div>
            </div>
        </div>
        <div class="cloudcast-display-show">
            <div class="cloudcast-display-block-inner">
                <div><strong>CURRENT SHOW</strong> <span data-bind="text: current_show_title"></span></div>
                <div class="progress progress-striped cloudcast-display-progress">
                    <div class="bar bar-warning" data-bind="style: { width: current_show_percentage() + '%' }"></div>
                    <div class="cloudcast-display-progress-elapsed"><span data-bind="text: current_show_elapsed"></span> / <span data-bind="text: current_show_duration"></span></div>
                    <div class="cloudcast-display-progress-remaining"><span data-bind="text: current_show_remaining"></span></span></div>
                </div>
                <div><strong>NEXT SHOW</strong> <span data-bind="text: next_show_title"></span></div>
            </div>
        </div>
    </div>
    <div class="cloudcast-display-right">
        <div class="cloudcast-display-status">
            <div class="cloudcast-display-status-inner">
                <div class="cloudcast-display-status-time">
                    <span data-bind="text: updated_on_time"></span>
                </div>
                <ul class="nav nav-pills cloudcast-display-status-input">
                    <!-- schedule input -->
                    <li data-bind="css: { 'active': schedule_input_active }, popover: { title: 'Schedule Input', templateId: 'schedule_input_popover', container: 'body', trigger: 'hover', placement: 'bottom' }, click: function() { $parent.toggle_input_enabled('schedule') }">
                        <a href="#"><i class="icon-road" data-bind="css: { 'icon-white': schedule_input_enabled }"></i></a>
                    </li>
                    <!-- show input -->
                    <li data-bind="css: { 'active': show_input_active }, popover: { title: 'Show Input', templateId: 'show_input_popover', container: 'body', trigger: 'hover', placement: 'bottom' }, click: function() { $parent.toggle_input_enabled('show') }">
                        <a href="#"><i class="icon-headphones" data-bind="css: { 'icon-white': show_input_enabled }"></i></a>
                    </li>
                    <!-- talkover input -->
                    <li data-bind="css: { 'active': talkover_input_active }, popover: { title: 'Talkover Input', templateId: 'talkover_input_popover', container: 'body', trigger: 'hover', placement: 'bottom' }, click: function() { $parent.toggle_input_enabled('talkover') }">
                        <a href="#"><i class="icon-bullhorn" data-bind="css: { 'icon-white': talkover_input_enabled }"></i></a>
                    </li>
                    <!-- master input -->
                    <li data-bind="css: { 'active': master_input_active }, popover: { title: 'Master Input', templateId: 'master_input_popover', container: 'body', trigger: 'hover', placement: 'bottom' }, click: function() { $parent.toggle_input_enabled('master') }">
                        <a href="#"><i class="icon-glass" data-bind="css: { 'icon-white': master_input_enabled }"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="cloudcast-display-logo"></div>
</div>
<script id="schedule_input_popover" type="text/html">
    <i class="icon-ok-circle" data-bind="visible: schedule_input_enabled"></i>
    <i class="icon-ban-circle" data-bind="visible: !schedule_input_enabled()"></i>
    <span data-bind="text: schedule_input_enabled() ? 'ENABLED' : 'DISABLED'"></span>
</script>
<script id="show_input_popover" type="text/html">
    <div>
        <i class="icon-ok-circle" data-bind="visible: show_input_enabled"></i>
        <i class="icon-ban-circle" data-bind="visible: !show_input_enabled()"></i>
        <span data-bind="text: show_input_enabled() ? 'ENABLED' : 'DISABLED'"></span>
    </div>
    <div>
        <i class="icon-user"></i> <span data-bind="text: show_input_username"></span>
    </div>
</script>
<script id="talkover_input_popover" type="text/html">
    <div>
        <i class="icon-ok-circle" data-bind="visible: talkover_input_enabled"></i>
        <i class="icon-ban-circle" data-bind="visible: !talkover_input_enabled()"></i>
        <span data-bind="text: talkover_input_enabled() ? 'ENABLED' : 'DISABLED'"></span>
    </div>
    <div>
        <i class="icon-user"></i> <span data-bind="text: talkover_input_username"></span>
    </div>
</script>
<script id="master_input_popover" type="text/html">
    <div>
        <i class="icon-ok-circle" data-bind="visible: master_input_enabled"></i>
        <i class="icon-ban-circle" data-bind="visible: !master_input_enabled()"></i>
        <span data-bind="text: master_input_enabled() ? 'ENABLED' : 'DISABLED'"></span>
    </div>
    <div>
        <i class="icon-user"></i> <span data-bind="text: master_input_username"></span>
    </div>
</script>