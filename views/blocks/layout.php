<script>
    var block_items_js = <?php echo Format::forge(array_values($block->block_items))->to_json(); ?>
</script>
<div id="blocks-layout" class="cloudcast-section">
    <table>
        <tr>
            <td class="cloudcast-section-sidebar">
                <div class="cloudcast-section-sidebar-inner">
                    <?php echo $blocks_finder; ?>
                </div>
            </td>
            <td class="cloudcast-section-sidebar">
                <div class="cloudcast-section-sidebar-inner">
                    <?php echo $files_finder; ?>
                </div>
            </td>
            <td class="cloudcast-section-content">
                <div class="cloudcast-section-content-inner">
                    <div class="cloudcast-header">
                        <h4>Layout <?php echo $block->title; ?></h4>
                        <span class="label label-info"><i class="icon-time"></i> <span data-bind="text: total_duration"></span></span>
                        <span class="label label-info"><strong>%</strong> <span data-bind="text: total_percentage"></span></span>
                        <div class="cloudcast-header-right">
                            <button type="submit" class="btn btn-primary" form="block_layout_form">SAVE</button>
                            <a href="/blocks" class="btn">CANCEL</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <?php echo Form::open(array('id' => 'block_layout_form', 'action' => '/blocks/layout/' . $block->id)); ?>
                    <div data-bind="sortable: block_items">
                        <div class="cloudcast-super-item">

                            <input type="hidden" data-bind="attr: { name: 'titles[' + $index() + ']' }, value: title" />
                            <div class="cloudcast-item-section">
                                <a title="Delete" class="btn btn-mini btn-danger" href="#" data-bind="click: $parent.remove_item"><i class="icon-remove"></i></a>
                            </div>

                            <!-- ko if: child_block -->

                            <div class="cloudcast-item-section">
                                <strong><span data-bind="text: child_block().title"></span></strong>
                            </div>

                            <input type="hidden" data-bind="attr: { name: 'child_block_ids[' + $index() + ']' }, value: child_block().id" />
                            <div class="cloudcast-item-section">
                                <label class="checkbox inline">
                                    <input type="checkbox" data-bind="checked: checked_percentage, enable: checked_duration, attr: { name: 'percentages[' + $index() + ']' }" /> <span class="label label-info">%</span>
                                </label>
                                <label class="checkbox inline">
                                    <input type="checkbox" data-bind="checked: checked_duration, enable: checked_percentage, attr: { name: 'durations[' + $index() + ']' }" /> <span class="label label-info"><i class="icon-time"></i></span>
                                </label>
                            </div>

                            <div class="cloudcast-item-section" data-bind="visible: checked_duration">
                                <input type="text" class="input-mini" data-bind="attr: { name: 'durations[' + $index() + ']' }, value: entered_duration" /> HH:MM:SS
                            </div>

                            <div class="cloudcast-item-section" data-bind="visible: checked_percentage">
                                <input type="text" class="input-mini" data-bind="attr: { name: 'percentages[' + $index() + ']' }, value: percentage" /> %
                            </div>

                            <!-- /ko -->
                            <!-- ko if: file-->

                            <div class="cloudcast-item-section">
                                <strong><span data-bind="text: file().artist"></span></strong> - <strong><span data-bind="text: file().title"></span></strong>
                            </div>

                            <input type="hidden" data-bind="attr: { name: 'file_ids[' + $index() + ']' }, value: file().id" />
                            <div class="cloudcast-item-section">
                                <span class="label label-info"><i class="icon-time"></i> <span data-bind="text: file().duration"></span></span>
                            </div>

                            <!-- /ko -->

                        </div>
                    </div>
                    <?php echo Form::close(); ?>
                </div>
            </td>
        </tr>
    </table>
</div>