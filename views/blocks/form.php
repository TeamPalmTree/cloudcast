<script>
    var block_js = <?php echo isset($block) ? Format::forge($block)->to_json() : 'null'; ?>
</script>
<div id="blocks-form" class="cloudcast-section">
    <table>
        <tr>
            <td class="cloudcast-section-sidebar">
                <?php echo $files_finder; ?>
            </td>
            <td class="cloudcast-section-content">
                <div class="cloudcast-section-content-inner">
                    <div class="cloudcast-super-header-form">
                        <div class="cloudcast-super-header-section">
                            <h4><?php echo isset($block) ? 'Edit ' . $block->title : 'Create Block'; ?></h4>
                        </div>
                        <div class="cloudcast-super-header-section-right">
                            <button type="submit" class="btn btn-mini btn-primary" form="blocks_formm">SAVE</button>
                            <?php if (isset($block)): ?>
                                <a href="/blocks/layout/<?php echo $block->id; ?>" class="btn btn-mini">LAYOUT</a>
                            <?php endif; ?>
                            <a href="/blocks" class="btn btn-mini">CANCEL</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <!-- block form -->
                    <?php echo Form::open(array('id' => 'blocks_formm', 'action' => isset($block) ? '/blocks/edit/' . $block->id : '/blocks/create', 'class' => 'form-horizontal', 'data-bind' => 'with: block')); ?>
                    <!-- block -->
                    <div class="control-group">
                        <?php echo Form::label('Title', 'title', array('class' => 'control-label')); ?>
                        <div class="controls">
                            <input name="title" type="text" data-bind="value: title" placeholder="Title" />
                        </div>
                    </div>
                    <div class="control-group">
                        <?php echo Form::label('Description', 'description', array('class' => 'control-label')); ?>
                        <div class="controls">
                            <input name="description" type="text" data-bind="value: description" placeholder="Description" />
                        </div>
                    </div>
                    <h5>Queries</h5>
                    <div class="control-group">
                        <?php echo Form::label('File Query', 'file_query', array('class' => 'control-label')); ?>
                        <div class="controls">
                            <?php echo Form::textarea('file_query', null, array('rows' => '5', 'placeholder' => 'File Query', 'data-bind' => "value: file_query, valueUpdate: 'afterkeydown'" )); ?>
                        </div>
                    </div>
                    <!-- criteria (weighted) -->
                    <div class="control-group">
                        <?php echo Form::label('Weighted', 'weighted', array('class' => 'control-label')); ?>
                        <div class="controls">
                            <input name="weighted" type="checkbox" data-bind="checked: weighted" />
                        </div>
                    </div>
                    <div data-bind="visible: weighted">
                        <div class="control-group" data-bind="foreach: block_weights">
                            <div class="controls">
                                <input type="text" data-bind="value: weight, attr: { name: 'block_weights[' + $index() + '][weight]' }" placeholder="Weight"/>
                                <button name="remove" type="button" class="btn btn-mini btn-danger" data-bind="visible: ($parent.block_weights().length > 1), click: $parent.remove_block_weight"><i class="icon-remove"></i></button>
                                <button name="add" type="button" class="btn btn-mini btn-info" data-bind="visible: $index() == ($parent.block_weights().length - 1), click: $parent.add_block_weight"><i class="icon-plus"></i></button>
                            </div>
                            <div class="controls">
                                <?php echo Form::textarea('file_query', null, array('rows' => '3', 'placeholder' => 'Weight Query', 'data-bind' => "value: file_query, valueUpdate: 'afterkeydown', attr: { name: 'block_weights[' + \$index() + '][file_query]' }" )); ?>
                            </div>
                        </div>
                    </div>
                    <h5>Harmony</h5>
                    <div class="control-group">
                        <?php echo Form::label('Harmonic Key', 'harmonic_key', array('class' => 'control-label')); ?>
                        <div class="controls form-inline">
                            <?php foreach ($options as $option_key => $option_value): ?>
                                <label class="radio">
                                    <input name="harmonic_key" type="radio" value="<?php echo $option_key; ?>" data-bind="checked: harmonic_key" />
                                    <?php echo $option_value; ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div data-bind="visible: harmonic_key() == '1'">
                        <div class="control-group form-inline">
                            <?php echo Form::label('Initial Key', 'initial_key', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <input name="initial_key" type="text" data-bind="value: initial_key" placeholder="Initial Key" />
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <?php echo Form::label('Harmonic Energy', 'harmonic_energy', array('class' => 'control-label')); ?>
                        <div class="controls form-inline">
                            <?php foreach ($options as $option_key => $option_value): ?>
                                <label class="radio">
                                    <input name="harmonic_energy" type="radio" value="<?php echo $option_key; ?>" data-bind="checked: harmonic_energy" />
                                    <?php echo $option_value; ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div data-bind="visible: harmonic_energy() == '1'">
                        <div class="control-group">
                            <?php echo Form::label('Initial Energy', 'initial_energy', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <input name="initial_energy" type="text" data-bind="value: initial_energy" placeholder="Initial Energy" />
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <?php echo Form::label('Harmonic Genre', 'harmonic_genre', array('class' => 'control-label')); ?>
                        <div class="controls form-inline">
                            <?php foreach ($options as $option_key => $option_value): ?>
                                <label class="radio">
                                    <input name="harmonic_genre" type="radio" value="<?php echo $option_key; ?>" data-bind="checked: harmonic_genre" />
                                    <?php echo $option_value; ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div data-bind="visible: harmonic_genre() == '1'">
                        <div class="control-group">
                            <?php echo Form::label('Initial Genre', 'initial_genre', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <input name="initial_genre" type="text" data-bind="value: initial_genre" placeholder="Initial Genre" />
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <?php echo Form::label('Separate Similar', 'separate_similar', array('class' => 'control-label')); ?>
                        <div class="controls form-inline">
                            <?php foreach ($options as $option_key => $option_value): ?>
                                <label class="radio">
                                    <input name="separate_similar" type="radio" value="<?php echo $option_key; ?>" data-bind="checked: separate_similar" />
                                    <?php echo $option_value; ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php echo Form::close(); ?>
                </div>
            </td>
        </tr>
    </table>
</div>