<div id="cloudcast_display" data-bind="with: status">
    <div class="cloudcast_display-left">
        <div class="cloudcast_display-file">
            <div class="cloudcast_display-block-inner">
                <div><strong>CURRENT SONG</strong> <span data-bind="text: current_file_artist"></span> - <span data-bind="text: current_file_title"></span></div>
                <div class="progress progress-striped cloudcast_display-progress">
                    <div class="bar bar-warning" data-bind="style: { width: current_file_percentage() + '%' }"></div>
                    <div class="cloudcast_display-progress-time"><span data-bind="text: current_file_elapsed"></span> / <span data-bind="text: current_file_duration"></span></div>
                </div>
                <div><strong>NEXT SONG</strong> <span data-bind="text: next_file_artist"></span> - <span data-bind="text: next_file_title"></span></div>
            </div>
        </div>
        <div class="cloudcast_display-show">
            <div class="cloudcast_display-block-inner">
                <div><strong>CURRENT SHOW</strong> <span data-bind="text: current_show_title"></span></div>
                <div class="progress progress-striped cloudcast_display-progress">
                    <div class="bar bar-warning" data-bind="style: { width: current_show_percentage() + '%' }"></div>
                    <div class="cloudcast_display-progress-time"><span data-bind="text: current_show_elapsed"></span> / <span data-bind="text: current_show_duration"></span></div>
                </div>
                <div><strong>NEXT SHOW</strong> <span data-bind="text: next_show_title"></span></div>
            </div>
        </div>
    </div>
    <div class="cloudcast_display-right">
        <div class="cloudcast_display-status">
            <div class="cloudcast_display-status-inner">
                <div class="cloudcast_display-status-time">
                    <span data-bind="text: updated_on_time"></span>
                </div>
                <div class="btn-group cloudcast_display-status-input">
                    <!-- schedule input -->
                    <button class="btn"
                            data-bind="css: { 'btn-primary': schedule_input_active, 'active': schedule_input_enabled }, popover: { title: 'Schedule Input', templateId: 'schedule_input_popover', container: 'body', trigger: 'hover', placement: 'bottom' }, click: function(data, event) { $parent.toggle_input_enabled('schedule') }">
                        <i class="icon-road"></i>
                    </button>
                    <!-- show input -->
                    <button class="btn"
                            data-bind="css: { 'btn-primary': show_input_active, 'active': show_input_enabled }, popover: { title: 'Show Input', templateId: 'show_input_popover', container: 'body', trigger: 'hover', placement: 'bottom' }, click: function(data, event) { $parent.toggle_input_enabled('show') }">
                        <i class="icon-headphones"></i>
                    </button>
                    <!-- talkover input -->
                    <button class="btn"
                            data-bind="css: { 'btn-primary': talkover_input_active, 'active': talkover_input_enabled }, popover: { title: 'Talkover Input', templateId: 'talkover_input_popover', container: 'body', trigger: 'hover', placement: 'bottom' }, click: function(data, event) { $parent.toggle_input_enabled('talkover') }">
                        <i class="icon-bullhorn"></i>
                    </button>
                    <!-- master input -->
                    <button class="btn"
                            data-bind="css: { 'btn-primary': master_input_active, 'active': master_input_enabled }, popover: { title: 'Master Input', templateId: 'master_input_popover', container: 'body', trigger: 'hover', placement: 'bottom' }, click: function(data, event) { $parent.toggle_input_enabled('master') }">
                        <i class="icon-glass"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="cloudcast_display-logo"></div>
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