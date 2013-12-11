<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CloudCast.<?php echo $section; ?>.<?php echo $title; ?></title>
    <!-- viewport -->
    <meta name="viewport" content="width=device-width">
    <meta name="viewport" content="initial-scale=0.8, user-scalable=no">
    <!-- css -->
    <?php echo Asset::css('reset.css'); ?>
    <?php echo Asset::css('template.css'); ?>
    <!-- js -->
    <?php echo Asset::js('jquery.min.js'); ?>
    <?php echo Asset::js('dateformat.js'); ?>
    <?php echo Asset::js('bootstrap.min.js'); ?>
    <?php echo Asset::js('bootstrap-datetimepicker.min.js'); ?>
    <?php echo Asset::js('typeahead.min.js'); ?>
    <?php echo Asset::js('knockout.min.js'); ?>
    <?php echo Asset::js('knockout.mapping.min.js'); ?>
    <?php echo Asset::js('knockout.bindingHandlers.js'); ?>
    <?php echo Asset::js('knockout-bootstrap.min.js'); ?>
    <?php echo Asset::js('knockout.orderable.js'); ?>
    <?php echo Asset::js('jquery-ui.min.js'); ?>
    <?php echo Asset::js('jquery.tablesorter.min.js'); ?>
    <?php echo Asset::js('knockout-sortable.min.js'); ?>
    <?php echo Asset::js('helper.js'); ?>
    <?php echo Asset::js('template.js'); ?>
</head>
<body>
    <div class="cloudcast">
        <?php echo $modal; ?>
        <?php echo $display; ?>
        <?php echo $navigation; ?>
        <?php echo $content; ?>
    </div>
</body>
</html>
