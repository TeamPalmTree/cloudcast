<div id="cloudcast-display" class="cloudcast-display" data-bind="with: status">
    <div class="cloudcast-display-file">
        <div><strong>CURRENT FILE</strong> <span data-bind="text: current_file_artist"></span> - <span data-bind="text: current_file_title"></span></div>
        <div class="progress cloudcast-display-progress">
            <div class="progress-bar progress-bar-warning" data-bind="style: { width: current_file_percentage() + '%' }"></div>
            <div class="cloudcast-display-progress-post" data-bind="style: { width: current_file_post_percentage() + '%' }"></div>
            <div class="cloudcast-display-progress-elapsed">
                <a data-bind="text: current_file_post, visible: current_file_post, click: $parent.show_post"></a>
                <a data-bind="visible: !current_file_post(), click: $parent.show_post">SET POST</a>
                / <span data-bind="text: current_file_elapsed"></span> / <span data-bind="text: current_file_duration"></span>
            </div>
            <div class="cloudcast-display-progress-remaining"><span data-bind="text: current_file_remaining"></span></div>
        </div>
        <div><strong>NEXT FILE</strong> <span data-bind="text: next_file_artist"></span> - <span data-bind="text: next_file_title"></span></div>
    </div>
    <div class="cloudcast-display-show">
        <div><strong>CURRENT SHOW</strong> <span data-bind="text: current_show_title"></span></div>
        <div class="progress cloudcast-display-progress active">
            <div class="progress-bar progress-bar-warning" data-bind="style: { width: current_show_percentage() + '%' }"></div>
            <div class="cloudcast-display-progress-elapsed"><span data-bind="text: current_show_elapsed"></span> / <span data-bind="text: current_show_duration"></span></div>
            <div class="cloudcast-display-progress-remaining"><span data-bind="text: current_show_remaining"></span></div>
        </div>
        <div><strong>NEXT SHOW</strong> <span data-bind="text: next_show_title"></span></div>
    </div>
    <div class="cloudcast-display-status">
        <div class="cloudcast-display-status-time">
            <span data-bind="text: updated_on_time"></span>
        </div>
        <div class="cloudcast-display-status-input">
            <ul class="nav nav-pills">
                <li data-bind="css: { 'active': schedule_input_active }, popover: { title: 'Schedule Input', templateId: 'schedule_input_popover', container: 'body', trigger: 'hover', placement: 'bottom' }, click: function() { $parent.toggle_input_enabled('schedule') }">
                    <a><span class="glyphicon glyphicon-road" data-bind="css: { 'glyphicon-white': schedule_input_enabled }"></span></a>
                </li>
                <li data-bind="css: { 'active': show_input_active }, popover: { title: 'Show Input', templateId: 'show_input_popover', container: 'body', trigger: 'hover', placement: 'bottom' }, click: function() { $parent.toggle_input_enabled('show') }">
                    <a><span class="glyphicon glyphicon-headphones" data-bind="css: { 'glyphicon-white': show_input_enabled }"></span></a>
                </li>
                <li data-bind="css: { 'active': talkover_input_active }, popover: { title: 'Talkover Input', templateId: 'talkover_input_popover', container: 'body', trigger: 'hover', placement: 'bottom' }, click: function() { $parent.toggle_input_enabled('talkover') }">
                    <a><span class="glyphicon glyphicon-bullhorn" data-bind="css: { 'glyphicon-white': talkover_input_enabled }"></span></a>
                </li>
                <li data-bind="css: { 'active': master_input_active }, popover: { title: 'Master Input', templateId: 'master_input_popover', container: 'body', trigger: 'hover', placement: 'bottom' }, click: function() { $parent.toggle_input_enabled('master') }">
                    <a><span class="glyphicon glyphicon-glass" data-bind="css: { 'glyphicon-white': master_input_enabled }"></span></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="cloudcast-display-logo"></div>
</div>
<script id="schedule_input_popover" type="text/html">
    <span class="glyphicon glyphicon-ok-circle" data-bind="visible: schedule_input_enabled"></span>
    <span class="glyphicon glyphicon-ban-circle" data-bind="visible: !schedule_input_enabled()"></span>
    <span data-bind="text: schedule_input_enabled() ? 'ENABLED' : 'DISABLED'"></span>
</script>
<script id="show_input_popover" type="text/html">
    <div>
        <span class="glyphicon glyphicon-ok-circle" data-bind="visible: show_input_enabled"></span>
        <span class="glyphicon glyphicon-ban-circle" data-bind="visible: !show_input_enabled()"></span>
        <span data-bind="text: show_input_enabled() ? 'ENABLED' : 'DISABLED'"></span>
    </div>
    <div>
        <span class="glyphicon glyphicon-user"></span> <span data-bind="text: show_input_username"></span>
    </div>
</script>
<script id="talkover_input_popover" type="text/html">
    <div>
        <span class="glyphicon glyphicon-ok-circle" data-bind="visible: talkover_input_enabled"></span>
        <span class="glyphicon glyphicon-ban-circle" data-bind="visible: !talkover_input_enabled()"></span>
        <span data-bind="text: talkover_input_enabled() ? 'ENABLED' : 'DISABLED'"></span>
    </div>
    <div>
        <span class="glyphicon glyphicon-user"></span> <span data-bind="text: talkover_input_username"></span>
    </div>
</script>
<script id="master_input_popover" type="text/html">
    <div>
        <span class="glyphicon glyphicon-ok-circle" data-bind="visible: master_input_enabled"></span>
        <span class="glyphicon glyphicon-ban-circle" data-bind="visible: !master_input_enabled()"></span>
        <span data-bind="text: master_input_enabled() ? 'ENABLED' : 'DISABLED'"></span>
    </div>
    <div>
        <span class="glyphicon glyphicon-user"></span> <span data-bind="text: master_input_username"></span>
    </div>
</script>