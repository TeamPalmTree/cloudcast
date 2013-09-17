<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CloudCast</title>
    <?php echo Asset::less('template.less'); ?>
    <?php echo Asset::js('jquery.js'); ?>
    <?php echo Asset::js('bootstrap.js'); ?>
</head>
<body>
<div class="cloudcast-welcome-logo"></div>
<div class="cloudcast-welcome-form">
<?php echo Form::open(array('id' => 'welcome-form', 'action' => 'welcome/login', 'class' => 'form-horizontal')); ?>
<div class="control-group">
    <input name="username" type="text" placeholder="Username" />
</div>
<div class="control-group">
    <input name="password" type="password" placeholder="Password" />
</div>
<div class="control-group">
    <button type="submit" class="btn btn-primary" form="welcome-form">LOGIN</button>
</div>
<?php echo Form::close(); ?>
</div>
<script>
    $(function() { $('input[name=username]').focus(); });
</script>
</body>
</html>