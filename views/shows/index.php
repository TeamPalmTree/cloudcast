<div id="shows-index" class="cloudcast-section">
    <div class="cloudcast-section-inner">
        <div class="cloudcast-super-header">
            <h4>Single Shows</h4>
        </div>
        <?php echo View::Forge('shows/shows', array('single' => true, 'shows' => $single_shows)); ?>
        <div class="cloudcast-super-header">
            <h4>Repeating Shows</h4>
        </div>
        <?php foreach ($repeat_days as $day => $repeat_shows): ?>
        <div class="cloudcast-super-header">
            <h5><?php echo $day; ?></h5>
        </div>
        <?php echo View::Forge('shows/shows', array('single' => false, 'shows' => $repeat_shows)); ?>
        <?php endforeach; ?>
    </div>
</div>