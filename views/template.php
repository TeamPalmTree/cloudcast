<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CloudCast.<?php echo $section; ?>.<?php echo $title; ?></title>
    <!-- css -->
    <?php echo Asset::css('bootstrap-datetimepicker.css'); ?>
    <!-- less -->
    <?php echo Asset::less('template.less'); ?>
    <!-- universal scripts -->
    <?php echo Asset::js('jquery.js'); ?>
    <?php echo Asset::js('knockout.js'); ?>
    <?php echo Asset::js('knockout.mapping.js'); ?>
    <?php echo Asset::js('knockout.bindingHandlers.js'); ?>
    <?php echo Asset::js('knockout.orderable.js'); ?>
    <?php echo Asset::js('dateformat.js'); ?>
    <?php echo Asset::js('helper.js'); ?>
    <?php echo Asset::js('template.js'); ?>
    <!-- environmental scripts -->
    <?php if (Fuel::$env !== Fuel::PRODUCTION): ?>
    <?php echo Asset::js('bootstrap.js'); ?>
    <?php echo Asset::js('bootstrap-datetimepicker.js'); ?>
    <?php echo Asset::js('jquery-ui.js'); ?>
    <?php echo Asset::js('jquery.tablesorter.js'); ?>
    <?php echo Asset::js('knockout-sortable.js'); ?>
    <?php else: ?>
    <?php echo Asset::js('bootstrap.min.js'); ?>
    <?php echo Asset::js('bootstrap-datetimepicker.min.js'); ?>
    <?php echo Asset::js('jquery-ui.min.js'); ?>
    <?php echo Asset::js('jquery.tablesorter.min.js'); ?>
    <?php echo Asset::js('knockout-sortable.min.js'); ?>
    <?php endif; ?>
</head>
<body>
<?php echo $modals; ?>
<div class="cloudcast_display">
    <?php echo $display; ?>
</div>
<div class="cloudcast_navigation">
    <?php echo $navigation; ?>
</div>
<div class="cloudcast_content">
    <?php echo $content; ?>
</div>
</body>
</html>
