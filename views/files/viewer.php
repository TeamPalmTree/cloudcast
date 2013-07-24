<div data-bind="with: file_viewer">
    <div class="cloudcast_header">
        <h4>Files</h4>
    </div>
    <div data-bind="foreach: files">
        <div class="cloudcast_sidebar-item">
            <label data-bind="popover: { title: title, content: description, container: 'body', trigger: 'hover', placement: 'right', html: true, delay: 1000 }">
                <i class="icon-music"></i>
                <strong><span class="cloudcast_popover" data-bind="text: title"></span></strong>
            </label>
            <div><i class="icon-user"></i> <span data-bind="text: artist"></span></div>
        </div>
    </div>
</div>