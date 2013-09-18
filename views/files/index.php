<div id="files-index" class="cloudcast-section">
    <table>
        <tr>
            <td class="cloudcast-section-sidebar">
                <div class="cloudcast-section-sidebar-content">
                    <table>
                        <tr>
                            <td class="cloudcast-section-sidebar-content-toolbar">
                                <div class="navbar">
                                    <div class="navbar-inner">
                                        <ul class="nav">
                                            <li><a href="#">Query</a></li>
                                        </ul>
                                        <ul class="nav pull-right">
                                            <li><a href="#" data-bind="click: clear"><i class="icon-remove"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="cloudcast-section-sidebar-content-query">
                                <?php echo Form::textarea('query', null, array('class' => 'cloudcast-section-sidebar-query', 'rows' => '5', 'placeholder' => 'Query', 'data-bind' => "value: query, valueUpdate: 'afterkeydown'" )); ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td class="cloudcast-section-content">
                <table>
                    <tr>
                        <td class="cloudcast-section-content-toolbar">
                            <div class="navbar">
                                <div class="navbar-inner">
                                    <div class="container">
                                        <a class="btn btn-navbar" data-toggle="collapse" data-target="#files-collapse">
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                        </a>
                                        <div id="files-collapse" class="nav-collapse collapse">
                                            <ul class="nav">
                                                <li><a href="#" data-bind="click: select_all"><i class="icon-check"></i></a></li>
                                                <li><a href="#" data-bind="click: deactivate"><i class="icon-ban-circle"></i> DEACTIVATE</a></li>
                                                <li><a href="#"><i class="icon-ok-sign"></i> ACTIVATE</a></li>
                                                <li><a href="#">RELEVANCE</a></li>
                                                <li class="navbar-form">
                                                    <input type="text" class="span1" data-bind="value: relevance">
                                                    <button class="btn" data-bind="click: set_relevance">SET</button>
                                                </li>
                                            </ul>
                                            <ul class="nav pull-right">
                                                <li class="divider-vertical"></li>
                                                <li><a href="#">Selected Files: <span data-bind="text: selected_file_ids_count"></span></a></li>
                                                <li class="divider-vertical"></li>
                                                <li><a href="#">Total Files: <?php echo $files_count; ?></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="cloudcast-section-content-bottom">
                            <div class="cloudcast-section-content-bottom-content">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'artist' }">Artist</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'title' }">Title</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'available' }">Available</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'downs' }">Relevance</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'album' }">Album</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'genre' }">Genre</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'ISRC' }">ISRC</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'composer' }">Composer</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'conductor' }">Conductor</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'copyright' }">Copyright</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'label' }">Label</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'language' }">Language</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'mood' }">Mood</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'ups' }">Ups</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'downs' }">Downs</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'found_on' }">Found On</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'last_play' }">Last Play</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'date' }">Date</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'track' }">Track</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'BPM' }">BPM</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'rating' }">Rating</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'bit_rate' }">Bit Rate</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'sample_rate' }">Sample Rate</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'duration' }">Duration</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'key' }">Key</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'energy' }">Energy</a></th>
                                            <th><a href="#" data-bind="orderable: { collection: 'files', field: 'website' }">Website</a></th>
                                            <th><a href="#">Name</a></th>
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
                                            <td data-bind="text: album"></td>
                                            <td data-bind="text: genre"></td>
                                            <td data-bind="text: ISRC"></td>
                                            <td data-bind="text: composer"></td>
                                            <td data-bind="text: conductor"></td>
                                            <td data-bind="text: copyright"></td>
                                            <td data-bind="text: label"></td>
                                            <td data-bind="text: language"></td>
                                            <td data-bind="text: mood"></td>
                                            <td data-bind="text: ups"></td>
                                            <td data-bind="text: downs"></td>
                                            <td data-bind="text: found_on"></td>
                                            <td data-bind="text: last_play"></td>
                                            <td data-bind="text: date"></td>
                                            <td data-bind="text: track"></td>
                                            <td data-bind="text: BPM"></td>
                                            <td data-bind="text: rating"></td>
                                            <td data-bind="text: bit_rate"></td>
                                            <td data-bind="text: sample_rate"></td>
                                            <td data-bind="text: duration"></td>
                                            <td data-bind="text: key"></td>
                                            <td data-bind="text: energy"></td>
                                            <td data-bind="text: website"></td>
                                            <td data-bind="text: name"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>