<script>
    var show_js = <?php echo isset($show) ? Format::forge($show)->to_json() : 'null'; ?>
</script>

<div class="cloudcast_header">
    <h4><?php echo $header; ?></h4>
    <div class="cloudcast_header-right">
        <button type="submit" class="btn btn-primary" form="shows_form">SAVE</button>
        <a href="/shows" class="btn">CANCEL</a>
    </div>
    <div class="clearfix"></div>
</div>

<!-- show form -->
<?php echo Form::open(array('id' => 'shows_form', 'action' => $action, 'class' => 'form-horizontal', 'data-bind' => 'with: show')); ?>

<!-- show -->
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
    <?php echo Form::label('Start On', 'user_start_on', array('class' => 'control-label')); ?>
    <div class="controls">
        <input class="datetime" name="user_start_on" type="text" data-bind="datetimepicker: user_start_on" placeholder="Start On" />
    </div>
</div>
<div class="control-group">
    <?php echo Form::label('End At', 'user_end_at', array('class' => 'control-label')); ?>
    <div class="controls">
        <input class="datetime" name="user_end_at" type="text" data-bind="datetimepicker: user_end_at" placeholder="End At" />
    </div>
</div>
<div class="control-group">
    <?php echo Form::label('Duration', 'duration', array('class' => 'control-label')); ?>
    <div class="controls">
        <input name="duration" type="text" data-bind="value: duration" placeholder="Duration" readonly />
    </div>
</div>

<!-- blocked -->
<div class="control-group">
    <?php echo Form::label('Blocked', 'blocked', array('class' => 'control-label')); ?>
    <div class="controls">
        <input name="blocked" type="checkbox" data-bind="checked: blocked" />
    </div>
</div>
<div id="show_form_block" data-bind="visible: blocked">
    <div class="control-group">
        <?php echo Form::label('Block', 'block', array('class' => 'control-label')); ?>
        <div class="controls">
            <input name="block" class="typeahead" type="text" data-bind="typeahead: block().title, typeaheadOptions: { source: $parent.query_blocks }" />
        </div>
    </div>
</div>

<!-- repeat -->
<div class="control-group">
    <?php echo Form::label('Repeated', 'repeated', array('class' => 'control-label')); ?>
    <div class="controls">
        <input name="repeated" type="checkbox" data-bind="checked: repeated" />
    </div>
</div>
<div id="show_form_repeat" data-bind="visible: repeated">
    <div class="control-group">
        <?php echo Form::label('Repeat Days', null, array('class' => 'control-label')); ?>
        <div class="controls">
            <label class="checkbox inline">
                <input name="show_repeat[Sunday]" type="checkbox" data-bind="checked: show_repeat().Sunday" /> Sunday
            </label>
            <label class="checkbox inline">
                <input name="show_repeat[Monday]" type="checkbox" data-bind="checked: show_repeat().Monday" /> Monday
            </label>
            <label class="checkbox inline">
                <input name="show_repeat[Tuesday]" type="checkbox" data-bind="checked: show_repeat().Tuesday" /> Tuesday
            </label>
            <label class="checkbox inline">
                <input name="show_repeat[Wednesday]" type="checkbox" data-bind="checked: show_repeat().Wednesday" /> Wednesday
            </label>
            <label class="checkbox inline">
                <input name="show_repeat[Thursday]" type="checkbox" data-bind="checked: show_repeat().Thursday" /> Thursday
            </label>
            <label class="checkbox inline">
                <input name="show_repeat[Friday]" type="checkbox" data-bind="checked: show_repeat().Friday" /> Friday
            </label>
            <label class="checkbox inline">
                <input name="show_repeat[Saturday]" type="checkbox" data-bind="checked: show_repeat().Saturday" /> Saturday
            </label>
        </div>
    </div>
    <div class="control-group">
        <?php echo Form::label('Repeat Ends', 'show_repeat[ends]', array('class' => 'control-label')); ?>
        <div class="controls">
            <input name="show_repeat[ends]" type="checkbox" data-bind="checked: show_repeat().ends" />
        </div>
    </div>
    <div data-bind="visible: show_repeat().ends">
        <div class="control-group">
            <?php echo Form::label('Repeat Ends On', 'show_repeat[user_end_on]', array('class' => 'control-label')); ?>
            <div class="controls">
                <input class="datetime" name="show_repeat[user_end_on]" type="text" data-bind="datetimepicker: show_repeat().user_end_on" placeholder="End On" />
            </div>
        </div>
    </div>
</div>

<!-- hosted -->
<div class="control-group">
    <?php echo Form::label('Hosted', 'hosted', array('class' => 'control-label')); ?>
    <div class="controls">
        <input name="hosted" type="checkbox" data-bind="checked: hosted" />
    </div>
</div>
<div data-bind="visible: hosted">
    <div class="control-group" data-bind="foreach: users">
        <div class="controls">
            <input class="typeahead" type="text" placeholder="Username" data-bind="typeahead: username, typeaheadOptions: { source: $parents[1].query_users }, attr: { name: 'users[' + $index() + '][username]' }" />
            <button name="remove" type="button" class="btn btn-danger" data-bind="visible: ($parent.users().length > 1), click: $parent.remove_user"><i class="icon-remove"></i></button>
            <button name="add" type="button" class="btn btn-info" data-bind="visible: $index() == ($parent.users().length - 1), click: $parent.add_user"><i class="icon-plus"></i></button>
        </div>
    </div>
</div>
<?php echo Form::close(); ?>