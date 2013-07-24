<script>
    var blocks_js = <?php echo Format::forge($blocks)->to_json(); ?>
</script>

<div data-bind="with: block_finder">
    <div class="cloudcast_header">
        <h4>Blocks</h4>
    </div>
    <div data-bind="foreach: blocks">
        <div>
            <i class="icon-stop"></i>
            <a class="cloudcast_popover" href="#" data-bind="popover: { title: title, content: description, container: 'body', trigger: 'hover', placement: 'right' }, click: $parents[1].add_block">
                <span data-bind="text: title"></span>
            </a>
        </div>
    </div>
</div>