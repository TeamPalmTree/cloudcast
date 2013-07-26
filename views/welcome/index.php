<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CloudCast</title>
    <?php echo Asset::css('bootstrap-datetimepicker.css'); ?>
    <?php echo Asset::less('template.less'); ?>
    <?php echo Asset::js('jquery.js'); ?>
    <?php echo Asset::js('jquery.tablesorter.js'); ?>
    <?php echo Asset::js('bootstrap.js'); ?>
    <?php echo Asset::js('bootstrap-datetimepicker.js'); ?>
    <?php echo Asset::js('knockout.js'); ?>
    <?php echo Asset::js('knockout.mapping.js'); ?>
    <?php echo Asset::js('knockout.bindingHandlers.js'); ?>
    <?php echo Asset::js('knockout.orderable.js'); ?>
    <?php echo Asset::js('dateformat.js'); ?>
    <?php echo Asset::js('helper.js'); ?>
</head>
<body>
<div class="cloudcast_welcome_logo"></div>
<div class="cloudcast_welcome_form">
<?php echo Form::open(array('id' => 'welcome_form', 'action' => 'welcome/login', 'class' => 'form-horizontal')); ?>
<div class="control-group">
    <input name="username" type="text" placeholder="Username" />
</div>
<div class="control-group">
    <input name="password" type="password" placeholder="Password" />
</div>
<div class="control-group">
    <button type="submit" class="btn btn-primary" form="welcome_form">LOGIN</button>
</div>
<?php echo Form::close(); ?>
</div>
<script>
    $(function() { $('input[name=username]').focus(); });
</script>
</body>
</html>