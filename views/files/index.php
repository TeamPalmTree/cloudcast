<div id="files_index">
    <div class="cloudcast_sidebar-one">
        <div class="cloudcast_sidebar-content">
            <div class="cloudcast_header">
                <h4>Query</h4>
                <div class="cloudcast_header-right">
                    <a class="btn btn-mini btn-danger" href="#" data-bind="click: clear"><i class="icon-remove"></i></a>
                </div>
            </div>
            <?php echo Form::textarea('query', null, array('class' => 'cloudcast_sidebar-query', 'rows' => '5', 'placeholder' => 'Query', 'data-bind' => "value: query, valueUpdate: 'afterkeydown'" )); ?>
        </div>
    </div>
    <div class="cloudcast_content-one">
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th><a href="#" data-bind="orderable: { collection: 'files', field: 'artist' }">Artist</a></th>
                    <th><a href="#" data-bind="orderable: { collection: 'files', field: 'title' }">Title</a></th>
                    <th><a href="#" data-bind="orderable: { collection: 'files', field: 'album' }">Album</a></th>
                    <th><a href="#" data-bind="orderable: { collection: 'files', field: 'available' }">Available</a></th>
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
                <tr>
                    <td>
                        <a title="Deactivate" class="btn btn-mini btn-warning" href="#" data-bind="visible: available() == '1', click: deactivate"><i class="icon-ban-circle"></i></a>
                        <a title="Activate" class="btn btn-mini btn-primary" href="#" data-bind="visible: available() == '0', click: activate"><i class="icon-ok-sign"></i></a>
                    </td>
                    <td data-bind="text: artist"></td>
                    <td data-bind="text: title"></td>
                    <td data-bind="text: album"></td>
                    <td data-bind="text: available() == '1' ? 'Yes' : 'No'"></td>
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
                    <td>
                        <a class="btn btn-mini" href="#" data-bind="tooltip: { title: name, placement: 'left', trigger: 'click' }"><i class="icon-info-sign"></i></a>
                    </td>
                </tr>
            </tbody>
        </table>

        <h4>Total Files: <?php echo $files_count; ?></h4>

    </div>
</div>