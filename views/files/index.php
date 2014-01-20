<div id="files-index" class="standard-section">
    <?php echo $file_setter; ?>
    <div class="standard-sidebar" data-bind="visible: sidebar() == 'query'">
        <div class="standard-sidebar-toolbar">
            <nav class="navbar navbar-default">
                <ul class="nav navbar-nav navbar-right">
                    <li><button title="Close Query" class="btn btn-default navbar-btn" data-bind="click: function() { $root.sidebar(null); }"><span class="glyphicon glyphicon-remove"></span></button></li>
                </ul>
            </nav>
            <textarea class="form-control" rows="5" placeholder="Query" data-bind="immediate: query"></textarea>
        </div>
    </div>
    <div class="standard-sidebar" data-bind="visible: sidebar() == 'info'">
        <div class="standard-sidebar-toolbar">
            <nav class="navbar navbar-default">
                <ul class="nav navbar-nav navbar-right">
                    <li><button title="Close Information" class="btn btn-default navbar-btn" data-bind="click: function() { $root.sidebar(null); }"><span class="glyphicon glyphicon-remove"></span></button></li>
                </ul>
            </nav>
        </div>
        <div class="standard-sidebar-content">
            <div class="standard-sidebar-item">
                <div class="standard-sidebar-item-title">Total Files</div>
                <div><?php echo $files_count; ?></div>
            </div>
            <div class="standard-sidebar-item">
                <div class="standard-sidebar-item-title">Available Files</div>
                <div><?php echo $available_files_count; ?></div>
            </div>
            <div class="standard-sidebar-item">
                <div class="standard-sidebar-item-title">Unavilable Files</div>
                <div><?php echo $unavailable_files_count; ?></div>
            </div>
        </div>
    </div>
    <div class="standard-section-toolbar">
        <nav class="navbar navbar-default">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#files-index-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div id="files-index-collapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li>
                        <button title="Select All" class="btn btn-default navbar-btn" data-bind="click: select_all"><span class="glyphicon glyphicon-check"></span> <span data-bind="text: selected_files_count"></span></button>
                        <button title="Scan" class="btn btn-default navbar-btn" data-bind="css: { active: scanning }, click: scan"><span class="glyphicon glyphicon-eye-open"></span></button>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right" data-bind="visible: !sidebar()">
                    <li>
                        <button title="Set Properties" class="btn btn-default navbar-btn" data-bind="click: function() { sidebar('setter'); }"><span class="glyphicon glyphicon-pencil"></span></button>
                        <button title="View Information" class="btn btn-default navbar-btn" data-bind="click: function() { sidebar('info'); }"><span class="glyphicon glyphicon-info-sign"></span></button>
                        <button title="Query Files" class="btn btn-default navbar-btn" data-bind="click: function() { sidebar('query'); }"><span class="glyphicon glyphicon-search"></span></button>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <div class="standard-section-content">
        <div class="standard-section-inner">
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'artist' }">Artist</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'title' }">Title</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'available' }">Available</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'downs' }">Relevance</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'rating' }">Rating</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'album' }">Album</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'genre' }">Genre</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'BPM' }">BPM</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'key' }">Key</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'energy' }">Energy</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'ups' }">Ups</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'downs' }">Downs</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'duration' }">Duration</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'post' }">Post</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'user_found_on' }">Found On</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'user_modified_on' }">Modified On</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'user_last_played' }">Last Played</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'user_last_scheduled' }">Last Scheduled</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'date' }">Date</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'ISRC' }">ISRC</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'composer' }">Composer</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'conductor' }">Conductor</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'copyright' }">Copyright</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'language' }">Language</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'bit_rate' }">Bit Rate</a></th>
                        <th><a data-bind="orderable: { collection: 'files', field: 'sample_rate' }">Sample Rate</a></th>
                        <th><a>Name</a></th>
                    </tr>
                </thead>
                <tbody data-bind="foreach: files">
                    <tr data-bind="css: { 'selected': selected }, click: select">
                        <td>
                            <input type="checkbox" data-bind="checked: selected, click: function() { return true; }, clickBubble: false" />
                        </td>
                        <td data-bind="text: artist"></td>
                        <td data-bind="text: title"></td>
                        <td data-bind="text: available() == '1' ? 'Yes' : 'No'"></td>
                        <td data-bind="text: relevance"></td>
                        <td data-bind="text: rating"></td>
                        <td data-bind="text: album"></td>
                        <td data-bind="text: genre"></td>
                        <td data-bind="text: BPM"></td>
                        <td data-bind="text: key"></td>
                        <td data-bind="text: energy"></td>
                        <td data-bind="text: ups"></td>
                        <td data-bind="text: downs"></td>
                        <td data-bind="text: duration"></td>
                        <td data-bind="text: post"></td>
                        <td data-bind="text: user_found_on"></td>
                        <td data-bind="text: user_modified_on"></td>
                        <td data-bind="text: user_last_played"></td>
                        <td data-bind="text: user_last_scheduled"></td>
                        <td data-bind="text: date"></td>
                        <td data-bind="text: ISRC"></td>
                        <td data-bind="text: composer"></td>
                        <td data-bind="text: conductor"></td>
                        <td data-bind="text: copyright"></td>
                        <td data-bind="text: language"></td>
                        <td data-bind="text: bit_rate"></td>
                        <td data-bind="text: sample_rate"></td>
                        <td data-bind="text: name"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>