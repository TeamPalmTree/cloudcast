<div id="blocks_index">
    <?php foreach ($blocks as $block): ?>
        <div class="cloudcast_item">
            <div class="cloudcast_section">
                <a name="delete" title="Delete" class="btn btn-mini btn-danger" href="#" data-id="<?php echo $block->id; ?>" data-title="<?php echo $block->title; ?>"><i class="icon-remove"></i></a>
            </div>
            <div class="cloudcast_section">
                <a title="Edit" class="btn btn-mini" href="/blocks/edit/<?php echo $block->id; ?>"><i class="icon-edit"></i></a>
                <a title="Layout" href="/blocks/layout/<?php echo $block->id; ?>" class="btn btn-mini"><i class="icon-stop"></i></a>
            </div>
            <div class="cloudcast_section">
                <strong><?php echo $block->title; ?></strong> <span class="text-info"><?php echo $block->description; ?></span>
            </div>
            <div class="cloudcast_section">
                <?php if ($block->harmonic == '1'): ?><span class="label label-info"><i class="icon-music"></i> HARMONIC</span><?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>