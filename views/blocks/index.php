<div id="blocks-index" class="cloudcast-section">
    <div class="cloudcast-section-inner">
        <?php foreach ($blocks as $block): ?>
            <div class="cloudcast-item">
                <div class="cloudcast-item-section">
                    <a name="delete" title="Delete" class="btn btn-mini btn-danger" href="#" data-id="<?php echo $block->id; ?>" data-title="<?php echo $block->title; ?>"><i class="icon-remove"></i></a>
                </div>
                <div class="cloudcast-item-section">
                    <a title="Edit" class="btn btn-mini" href="/blocks/edit/<?php echo $block->id; ?>"><i class="icon-edit"></i></a>
                    <a title="Layout" href="/blocks/layout/<?php echo $block->id; ?>" class="btn btn-mini"><i class="icon-list"></i></a>
                </div>
                <div class="cloudcast-item-section">
                    <strong><?php echo $block->title; ?></strong> <span class="text-info"><?php echo $block->description; ?></span>
                </div>
                <div class="cloudcast-item-section-right">
                    <?php if ($block->harmonic_key == '1'): ?><span class="label label-info"><i class="icon-music"></i> KEY</span><?php endif; ?>
                    <?php if ($block->harmonic_energy == '1'): ?><span class="label label-info"><i class="icon-music"></i> ENERGY</span><?php endif; ?>
                    <?php if (count($block->block_weights) > 0): ?><span class="label label-info"><i class="icon-tasks"></i> WEIGHTED</span><?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>