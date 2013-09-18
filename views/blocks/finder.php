<script>
    var blocks_js = <?php echo Format::forge($blocks)->to_json(); ?>
</script>
<div class="cloudcast-section-sidebar-content" data-bind="with: block_finder">
    <table>
        <tr>
            <td class="cloudcast-section-sidebar-content-toolbar">
                <div class="navbar">
                    <div class="navbar-inner">
                        <ul class="nav">
                            <li><a href="#">Blocks</a></li>
                        </ul>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="cloudcast-section-sidebar-content-bottom">
                <div class="cloudcast-section-sidebar-content-bottom-inner">
                    <div data-bind="foreach: blocks">
                        <div>
                            <i class="icon-stop"></i>
                            <a class="cloudcast-popover" href="#" data-bind="popover: { title: title, content: description, container: 'body', trigger: 'hover', placement: 'right' }, click: $parents[1].add_block">
                                <span data-bind="text: title"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>