<script>
    var stream_js = <?php echo isset($stream) ? Format::forge($stream)->to_json() : 'null'; ?>
</script>
<div class="cloudcast-section">
    <div class="cloudcast-section-inner">
        <div class="cloudcast-header">
            <h4><?php echo $header; ?></h4>
            <div class="cloudcast-header-right">
                <button type="submit" class="btn btn-primary" form="streams-form">SAVE</button>
                <a href="/streams" class="btn">CANCEL</a>
            </div>
            <div class="clearfix"></div>
        </div>
        <!-- stream form -->
        <?php echo Form::open(array('id' => 'streams-form', 'action' => $action, 'class' => 'form-horizontal', 'data-bind' => 'with: stream')); ?>
        <!-- stream -->
        <div class="control-group">
            <?php echo Form::label('Name', 'name', array('class' => 'control-label')); ?>
            <div class="controls">
                <input name="name" type="text" data-bind="value: name" placeholder="Name" />
            </div>
        </div>
        <div class="control-group">
            <?php echo Form::label('Type', 'type', array('class' => 'control-label')); ?>
            <div class="controls">
                <select name="type" data-bind="value: type">
                    <?php foreach(Model_Stream::$types as $type_index => $type_name): ?>
                    <option value="<?php echo $type_index; ?>"><?php echo $type_name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <?php echo Form::label('Host', 'host', array('class' => 'control-label')); ?>
            <div class="controls">
                <input name="host" type="text" data-bind="value: host" placeholder="Host" />
            </div>
        </div>
        <div class="control-group">
            <?php echo Form::label('Port', 'port', array('class' => 'control-label')); ?>
            <div class="controls">
                <input name="port" type="text" data-bind="value: port" placeholder="Port" />
            </div>
        </div>
        <div class="control-group">
            <?php echo Form::label('Source Username', 'source_username', array('class' => 'control-label')); ?>
            <div class="controls">
                <input name="source_username" type="text" data-bind="value: source_username" placeholder="Source Username" />
            </div>
        </div>
        <div class="control-group">
            <?php echo Form::label('Source Password', 'source_password', array('class' => 'control-label')); ?>
            <div class="controls">
                <input name="source_password" type="password" data-bind="value: source_password" placeholder="Source Password" />
            </div>
        </div>
        <div class="control-group">
            <?php echo Form::label('Admin Username', 'admin_username', array('class' => 'control-label')); ?>
            <div class="controls">
                <input name="admin_username" type="text" data-bind="value: admin_username" placeholder="Admin Username" />
            </div>
        </div>
        <div class="control-group">
            <?php echo Form::label('Admin Password', 'admin_password', array('class' => 'control-label')); ?>
            <div class="controls">
                <input name="admin_password" type="password" data-bind="value: admin_password" placeholder="Admin Password" />
            </div>
        </div>
        <div class="control-group">
            <?php echo Form::label('Mount', 'mount', array('class' => 'control-label')); ?>
            <div class="controls">
                <input name="mount" type="text" data-bind="value: mount" placeholder="Mount" />
            </div>
        </div>
        <?php echo Form::close(); ?>
    </div>
</div>