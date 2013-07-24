<script>
    var block_js = <?php echo isset($block) ? Format::forge($block)->to_json() : 'null'; ?>
</script>

<div id="blocks_form">
    <div class="cloudcast_sidebar-one">
        <div class="cloudcast_sidebar-content">
            <?php echo $files_finder; ?>
        </div>
    </div>
    <div class="cloudcast_content-one">
        <div class="cloudcast_header">
            <h4><?php echo $header; ?></h4>
            <div class="cloudcast_header-right">
                <button type="submit" class="btn btn-primary" form="blocks_formm">SAVE</button>
                <a href="/blocks" class="btn">CANCEL</a>
            </div>
            <div class="clearfix"></div>
        </div>

        <!-- block form -->
        <?php echo Form::open(array('id' => 'blocks_formm', 'action' => $action, 'class' => 'form-horizontal', 'data-bind' => 'with: block')); ?>

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
        <div class="control-group">
            <?php echo Form::label('Harmonic', 'harmonic', array('class' => 'control-label')); ?>
            <div class="controls">
                <input name="harmonic" type="checkbox" data-bind="checked: harmonic" />
            </div>
        </div>
        <div class="control-group">
            <?php echo Form::label('File Query', 'file_query', array('class' => 'control-label')); ?>
            <div class="controls">
                <?php echo Form::textarea('file_query', null, array('rows' => '5', 'placeholder' => 'File Query', 'data-bind' => "value: file_query, valueUpdate: 'afterkeydown'" )); ?>
            </div>
        </div>

        <?php echo Form::close(); ?>
    </div>
</div>