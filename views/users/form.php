<script>
    var user_js = <?php echo isset($user) ? Format::forge($user)->to_json() : 'null'; ?>
</script>
<div class="cloudcast-section">
    <div class="cloudcast-section-inner">
        <div class="cloudcast-super-header-form">
            <div class="cloudcast-super-header-section">
                <h4><?php echo isset($user) ? 'Edit ' . $user->username : 'Create User'; ?></h4>
            </div>
            <div class="cloudcast-super-header-section-right">
                <button type="submit" class="btn btn-default btn-xs btn-primary" form="user-form">SAVE</button>
                <a href="/users" class="btn btn-default btn-xs">CANCEL</a>
            </div>
        </div>
        <!-- user form -->
        <?php echo Form::open(array('id' => 'user-form', 'action' => isset($user) ? '/users/edit/' . $user->id : '/users/create', 'class' => 'form-horizontal', 'data-bind' => 'with: user')); ?>
        <div class="form-group">
            <?php echo Form::label('Username', 'username', array('class' => 'control-label')); ?>
            <div class="col-sm-10">
                <input name="username" type="text" class="form-control" data-bind="nowValue: username" placeholder="Username" />
            </div>
        </div>
        <?php if (isset($user)): ?>
        <div class="form-group">
            <?php echo Form::label('Old Password', 'old_password', array('class' => 'control-label')); ?>
            <div class="col-sm-10">
                <input name="old_password" type="password" data-bind="nowValue: old_password" placeholder="Old Password" />
            </div>
        </div>
        <?php endif; ?>
        <div class="form-group">
            <?php echo Form::label('Password', 'password', array('class' => 'control-label')); ?>
            <div class="col-sm-10">
                <input name="password" type="password" data-bind="nowValue: password" placeholder="Password" />
            </div>
        </div>
        <div class="form-group">
            <?php echo Form::label('Group', 'group', array('class' => 'control-label')); ?>
            <div class="col-sm-10">
                <?php foreach ($groups as $group_key => $group_value): ?>
                <label class="radio">
                    <input name="group" type="radio" value="<?php echo $group_key; ?>" data-bind="checked: group" />
                    <?php echo $group_value['name']; ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo Form::label('Email', 'email', array('class' => 'control-label')); ?>
            <div class="col-sm-10">
                <input name="email" type="text" class="form-control" data-bind="nowValue: email" placeholder="Email" />
            </div>
        </div>
        <div class="form-group">
            <?php echo Form::label('First Name', 'first_name', array('class' => 'control-label')); ?>
            <div class="col-sm-10">
                <input name="first_name" type="text" class="form-control" data-bind="nowValue: first_name" placeholder="First Name" />
            </div>
        </div>
        <div class="form-group">
            <?php echo Form::label('Last Name', 'last_name', array('class' => 'control-label')); ?>
            <div class="col-sm-10">
                <input name="last_name" type="text" class="form-control" data-bind="nowValue: last_name" placeholder="Last Name" />
            </div>
        </div>
        <div class="form-group">
            <?php echo Form::label('Phone', 'phone', array('class' => 'control-label')); ?>
            <div class="col-sm-10">
                <input name="phone" type="text" class="form-control" data-bind="nowValue: phone" placeholder="Phone" />
            </div>
        </div>
        <?php echo Form::close(); ?>
    </div>
</div>