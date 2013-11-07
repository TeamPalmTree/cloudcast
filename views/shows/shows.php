<?php foreach ($shows as $show): ?>
<div class="cloudcast-item">
    <div class="cloudcast-item-time">
        <span class="cloudcast-item-time-start"><?php echo $single ? $show->user_start_on_timeday() : $show->user_start_on_time(); ?></span>
        <span class="cloudcast-item-time-end"><?php echo $single ? $show->user_end_at_timeday() : $show->user_end_at_time(); ?></span>
    </div>
    <div class="cloudcast-item-section">
        <a name="delete" title="Delete" class="btn btn-mini btn-danger" href="#" data-id="<?php echo $show->id; ?>" data-title="<?php echo $show->title; ?>"><i class="icon-remove"></i></a>
    </div>
    <div class="cloudcast-item-section">
        <a title="Edit Show" class="btn btn-mini" href="/shows/edit/<?php echo $show->id; ?>"><i class="icon-edit"></i></a>
    </div>
    <div class="cloudcast-item-section">
        <strong><?php echo $show->title; ?></strong>
    </div>
    <?php if ($show->block != null): ?>
    <div class="cloudcast-item-section">
        <a title="Edit Block" href="/blocks/edit/<?php echo $show->block->id; ?>" class="btn btn-mini"><i class="icon-edit"></i><i class="icon-stop"></i></a>
        <a title="Layout Block" href="/blocks/layout/<?php echo $show->block->id; ?>" class="btn btn-mini"><i class="icon-list"></i><i class="icon-stop"></i></a>
    </div>
    <?php endif; ?>
    <div class="cloudcast-item-section">
        <?php if ($show->block == null): ?><span class="label label-warning"><i class="icon-warning-sign"></i> NO BLOCK</span><?php endif; ?>
        <?php if ($show->block != null): ?><span class="label label-info"><i class="icon-stop"></i> <?php echo $show->block->title; ?></span><?php endif; ?>
    </div>
    <div class="cloudcast-item-section-right">
        <?php if (count($show->users) > 0): ?><span class="label label-info"><i class="icon-headphones"></i> HOSTED</span><?php endif; ?>
        <span class="label" data-bind="visible: jingles_album"><i class="icon-bell"></i> JINGLES</span>
        <span class="label" data-bind="visible: bumpers_album"><i class="icon-bell"></i> BUMPERS</span>
        <span class="label" data-bind="visible: sweepers_album"><i class="icon-bell"></i> SWEEPERS
            (<?php if ($show->sweeper_interval == '0'): ?>AUTO<?php else: ?><?php echo $show->sweeper_interval; ?><?php endif; ?>)
        </span>
    </div>
</div>
<?php endforeach; ?>