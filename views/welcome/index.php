<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CloudCast</title>
    <!-- css -->
    <?php echo Asset::css('reset.css'); ?>
    <?php echo Asset::css('template.css'); ?>
    <!-- js -->
    <?php echo Asset::js('jquery.min.js'); ?>
    <?php echo Asset::js('bootstrap.min.js'); ?>
</head>
<body>
<div class="cloudcast-welcome-logo"></div>
<div class="cloudcast-welcome-form">
<?php echo Form::open(array('id' => 'welcome-form', 'action' => 'welcome/login', 'class' => 'form-horizontal')); ?>
<div class="form-group">
    <input name="username" type="text" class="form-control" placeholder="Username" />
</div>
<div class="form-group">
    <input name="password" type="password" class="form-control" placeholder="Password" />
</div>
<div class="form-group">
    <button type="submit" class="btn btn-default btn-primary" form="welcome-form">LOGIN</button>
</div>
<?php echo Form::close(); ?>
</div>
<script>
    $(function() { $('input[name=username]').focus(); });
</script>
</body>
</html>