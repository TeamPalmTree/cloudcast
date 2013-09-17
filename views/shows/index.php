<div id="shows-index" class="cloudcast-section">
    <div class="cloudcast-section-inner">
        <div class="cloudcast-header">
            <h4>Single Shows</h4>
        </div>
        <?php foreach ($single_shows as $show): ?>
            <div class="cloudcast-item">
                <div class="cloudcast-item-date"><?php echo $show->user_start_on_date(); ?></div>
                <div class="cloudcast-item-time">
                    <span class="cloudcast-item-time-start"><?php echo $show->user_start_on_timeday(); ?></span>
                    <span class="cloudcast-item-time-end"><?php echo $show->user_end_at_timeday(); ?></span>
                </div>
                <div class="cloudcast-item-section">
                    <a name="delete" title="Delete" class="btn btn-mini btn-danger" href="#" data-id="<?php echo $show->id; ?>" data-title="<?php echo $show->title; ?>"><i class="icon-remove"></i></a>
                </div>
                <div class="cloudcast-item-section">
                    <a title="Edit" class="btn btn-mini" href="/shows/edit/<?php echo $show->id; ?>"><i class="icon-edit"></i></a>
                    <?php if ($show->block != null): ?><a title="Layout" href="/blocks/layout/<?php echo $show->block->id; ?>" class="btn btn-mini"><i class="icon-stop"></i> <?php echo $show->block->title; ?></a><?php endif; ?>
                </div>
                <div class="cloudcast-item-section">
                    <strong><?php echo $show->title; ?></strong>
                </div>
                <div class="cloudcast-item-section">
                    <?php if ($show->block == null): ?><span class="label label-warning"><i class="icon-warning-sign"></i> NO BLOCK</span><?php endif; ?>
                    <?php if (count($show->users) > 0): ?><span class="label label-info"><i class="icon-headphones"></i> HOSTED</span><?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="cloudcast-header">
            <h4>Repeating Shows</h4>
        </div>
        <?php foreach ($repeat_days as $day => $shows): ?>
        <div class="cloudcast-header">
            <h5><?php echo $day; ?></h5>
        </div>
        <?php foreach ($shows as $show): ?>
        <div class="cloudcast-item">
            <div class="cloudcast-item-time">
                <span class="cloudcast-item-time-start"><?php echo $show->user_start_on_time(); ?></span>
                <span class="cloudcast-item-time-end"><?php echo $show->user_end_at_time(); ?></span>
            </div>
            <div class="cloudcast-item-section">
                <a name="delete" title="Delete" class="btn btn-mini btn-danger" href="#" data-id="<?php echo $show->id; ?>" data-title="<?php echo $show->title; ?>"><i class="icon-remove"></i></a>
            </div>
            <div class="cloudcast-item-section">
                <a title="Edit" class="btn btn-mini" href="/shows/edit/<?php echo $show->id; ?>"><i class="icon-edit"></i></a>
                <?php if ($show->block != null): ?><a title="Layout" href="/blocks/layout/<?php echo $show->block->id; ?>" class="btn btn-mini"><i class="icon-stop"></i> <?php echo $show->block->title; ?></a><?php endif; ?>
            </div>
            <div class="cloudcast-item-section">
                <strong><?php echo $show->title; ?></strong>
            </div>
            <div class="cloudcast-item-section">
                <?php if ($show->block == null): ?><span class="label label-warning"><i class="icon-warning-sign"></i> NO BLOCK</span><?php endif; ?>
                <?php if (count($show->users) > 0): ?><span class="label label-info"><i class="icon-headphones"></i> HOSTED</span><?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
</div>