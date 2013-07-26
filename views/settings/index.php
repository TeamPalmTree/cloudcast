<div class="cloudcast_header">
    <h4>Settings</h4>
    <div class="cloudcast_header-right">
        <button type="submit" class="btn btn-primary" form="settings_index">SAVE</button>
    </div>
    <div class="clearfix"></div>
</div>

<!-- settings form -->
<?php echo Form::open(array('id' => 'settings_index', 'action' => '/settings', 'class' => 'form-horizontal')); ?>
<?php
// keep track of current category
$category = null;
foreach ($settings as $setting):
?>
<?php
if ($setting->category != $category):
    $category = $setting->category;
?>
<div class="cloudcast_header">
    <h5><?php echo Helper::human_name($category); ?></h5>
</div>
<?php endif; ?>
<div class="control-group">
    <?php echo Form::label($setting->human_name(), $setting->name, array('class' => 'control-label')); ?>
    <div class="controls">
        <?php if ($setting->type == 'text'): ?>
        <input name="<?php echo $setting->name; ?>" type="text" value="<?php echo $setting->value; ?>" placeholder="<?php echo $setting->human_name(); ?>" />
        <?php elseif ($setting->type == 'password'): ?>
        <input name="<?php echo $setting->name; ?>" type="password" value="<?php echo $setting->value; ?>" placeholder="<?php echo $setting->human_name(); ?>" />
        <?php elseif ($setting->type == 'datetime'): ?>
        <input name="<?php echo $setting->name; ?>" type="text" class="datetime" value="<?php echo $setting->value; ?>" placeholder="<?php echo $setting->human_name(); ?>" data-bind="datetimepicker: '<?php echo $setting->value; ?>'" />
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>
<?php echo Form::close(); ?>