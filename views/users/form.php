<script>
    var user_js = <?php echo isset($user) ? Format::forge($user)->to_json() : 'null'; ?>
</script>
<div class="cloudcast-section">
    <div class="cloudcast-section-inner">
        <div class="cloudcast-header">
            <h4><?php echo $header; ?></h4>
            <div class="cloudcast-header-right">
                <button type="submit" class="btn btn-primary" form="user-form">SAVE</button>
                <a href="/users" class="btn">CANCEL</a>
            </div>
            <div class="clearfix"></div>
        </div>
        <!-- user form -->
        <?php echo Form::open(array('id' => 'user-form', 'action' => $action, 'class' => 'form-horizontal', 'data-bind' => 'with: user')); ?>
        <div class="control-group">
            <?php echo Form::label('Username', 'username', array('class' => 'control-label')); ?>
            <div class="controls">
                <input name="username" type="text" data-bind="value: username" placeholder="Username" />
            </div>
        </div>
        <?php if (isset($user)): ?>
        <div class="control-group">
            <?php echo Form::label('Old Password', 'old_password', array('class' => 'control-label')); ?>
            <div class="controls">
                <input name="old_password" type="password" data-bind="value: old_password" placeholder="Old Password" />
            </div>
        </div>
        <?php endif; ?>
        <div class="control-group">
            <?php echo Form::label('Password', 'password', array('class' => 'control-label')); ?>
            <div class="controls">
                <input name="password" type="password" data-bind="value: password" placeholder="Password" />
            </div>
        </div>
        <div class="control-group">
            <?php echo Form::label('Group', 'group', array('class' => 'control-label')); ?>
            <div class="controls">
                <?php foreach ($groups as $group_key => $group_value): ?>
                <label class="radio">
                    <input name="group" type="radio" value="<?php echo $group_key; ?>" data-bind="checked: group" />
                    <?php echo $group_value['name']; ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="control-group">
            <?php echo Form::label('Email', 'email', array('class' => 'control-label')); ?>
            <div class="controls">
                <input name="email" type="text" data-bind="value: email" placeholder="Email" />
            </div>
        </div>
        <div class="control-group">
            <?php echo Form::label('First Name', 'first_name', array('class' => 'control-label')); ?>
            <div class="controls">
                <input name="first_name" type="text" data-bind="value: first_name" placeholder="First Name" />
            </div>
        </div>
        <div class="control-group">
            <?php echo Form::label('Last Name', 'last_name', array('class' => 'control-label')); ?>
            <div class="controls">
                <input name="last_name" type="text" data-bind="value: last_name" placeholder="Last Name" />
            </div>
        </div>
        <div class="control-group">
            <?php echo Form::label('Phone', 'phone', array('class' => 'control-label')); ?>
            <div class="controls">
                <input name="phone" type="text" data-bind="value: phone" placeholder="Phone" />
            </div>
        </div>
        <?php echo Form::close(); ?>
    </div>
</div>