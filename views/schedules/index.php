<script>
    var schedule_dates_js = <?php echo Format::forge($schedule_dates)->to_json(); ?>
</script>
<div id="schedules-index" class="cloudcast-section">
    <table>
        <tr>
            <td class="cloudcast-section-sidebar">
                <div class="cloudcast-section-sidebar-inner">
                    <?php echo $files_finder; ?>
                </div>
            </td>
            <td class="cloudcast-section-content">
                <div class="cloudcast-section-content-inner" data-bind="foreach: schedule_dates">
                    <div class="cloudcast-super-header">
                        <h4 data-bind="text: date"></h4>
                    </div>
                    <div data-bind="foreach: schedules">
                        <div class="cloudcast-item">
                            <div class="cloudcast-item-section">
                                <a title="Delete" class="btn btn-mini btn-danger" href="#" data-bind="click: $parent.delete"><i class="icon-remove"></i></a>
                            </div>
                            <div class="cloudcast-item-section">
                                <a title="Edit" href="#" class="btn btn-mini" data-bind="css: { 'btn-warning': ($parents[1].editing_schedule() == $data) }, click: $parents[1].edit_schedule"><i class="icon-edit"></i></a>
                                <a href="#" class="btn btn-mini btn-primary" data-bind="visible: ($parents[1].editing_schedule() == $data), click: $parents[1].save_schedule">SAVE</a>
                            </div>
                            <div class="cloudcast-item-time">
                                <span class="cloudcast-item-time-start" data-bind="text: show.user_start_on_timeday"></span>
                                <span class="cloudcast-item-time-end" data-bind="text: show.user_end_at_timeday"></span>
                            </div>
                            <div class="cloudcast-item-section">
                                <strong><span data-bind="text: show.title"></span></strong>
                            </div>
                            <div class="cloudcast-item-section">
                                <span class="label label-info"><i class="icon-time"></i> <span data-bind="text: total_duration"></span></span>
                            </div>
                        </div>
                        <div data-bind="sortable: { data: schedule_files, options: { cancel: '.no-sort'}}">
                            <div class="cloudcast-item" data-bind="css: { 'no-sort': ($parents[2].editing_schedule() != $parent) }">
                                <!-- ko if: ($parents[2].editing_schedule() == $parent) -->
                                <div class="cloudcast-item-section">
                                    <a title="Delete" class="btn btn-mini btn-danger" href="#" data-bind="click: $parent.delete_file"><i class="icon-remove"></i></a>
                                </div>
                                <!-- /ko -->
                                <div class="cloudcast-item-section" data-bind="visible: played_on">
                                    <span class="label label-success"><i class="icon-ok"></i> PLAYED</span>
                                </div>
                                <div class="cloudcast-item-section">
                                    <strong><span data-bind="text: file().artist"></span></strong> - <strong><span data-bind="text: file().title"></span></strong>
                                </div>
                                <div class="cloudcast-item-section">
                                    <span class="label label-info"><i class="icon-time"></i> <span data-bind="text: file().duration"></span></span>
                                    <span class="label label-info"><i class="icon-music"></i> <span data-bind="text: file().genre"></span> (<span data-bind="text: file().key"></span>-<span data-bind="text: file().energy"></span>)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>