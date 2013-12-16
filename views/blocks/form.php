<div id="block-form" class="standard-section">
    <?php echo $file_viewer; ?>
    <div class="standard-section-toolbar">
        <nav class="navbar navbar-default">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#block-form-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div id="block-form-collapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active" data-bind="if: block">
                        <a data-bind="text: block().id() ? ('Edit ' + block().title()) : ('Create ' + (block().title() ? block().title() : ''))"></a>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right" data-bind="visible: !sidebar()">
                    <li>
                        <button title="View Files" class="btn btn-default navbar-btn" data-bind="click: function() { sidebar('files'); }"><span class="glyphicon glyphicon-folder-open"></span></button>
                        <button title="Cancel" class="btn btn-default navbar-btn" data-bind="click: cancel"><span class="glyphicon glyphicon-floppy-remove"></span></button>
                        <button title="Save" class="btn btn-default navbar-btn" data-bind="css: { 'active': saving }, click: save"><span class="glyphicon glyphicon-floppy-saved"></span></button>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <div class="standard-section-content">
        <div class="standard-section-inner">
            <form class="form-horizontal" data-bind="with: block">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="title">Title</label>
                    <div class="col-sm-10">
                        <input name="title" type="text" class="form-control" data-bind="nowValue: title, validate: $root.errors" placeholder="Title" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="description">Description</label>
                    <div class="col-sm-10">
                        <input name="description" type="text" class="form-control" data-bind="nowValue: description" placeholder="Description" />
                    </div>
                </div>
                <h5>Queries</h5>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="file_query">File Query</label>
                    <div class="col-sm-10">
                        <textarea name="file_query" class="form-control" rows="5" placeholder="File Query" data-bind="nowValue: file_query"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="weighted">Weighted</label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input name="weighted" type="checkbox" data-bind="checked: weighted" />
                            </label>
                        </div>
                    </div>
                </div>
                <!-- ko if: block_weights().length > 0 -->
                <div data-bind="foreach: block_weights">
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="text" class="form-control" data-bind="nowValue: weight, attr: { name: 'block_weights[' + $index() + '][weight]' }, validate: $root.errors" placeholder="Weight"/>
                            <textarea class="form-control" rows="3" placeholder="Weight Query" data-bind="nowValue: file_query, attr: { name: 'block_weights[' + $index() + '][file_query]' }"></textarea>
                            <button name="remove" type="button" class="btn btn-danger" data-bind="visible: ($parent.block_weights().length > 1), click: $parent.remove_block_weight"><span class="glyphicon glyphicon-remove"></span></button>
                            <button name="add" type="button" class="btn btn-info" data-bind="visible: $index() == ($parent.block_weights().length - 1), click: $parent.add_block_weight"><span class="glyphicon glyphicon-plus"></span></button>
                        </div>
                    </div>
                </div>
                <!-- /ko -->
                <h5>Backup</h5>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="backup_blocked">Backup Blocked</label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input name="backup_blocked" type="checkbox" data-bind="checked: backup_blocked" />
                            </label>
                        </div>
                    </div>
                </div>
                <!-- ko if: backup_block -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="backup_block">Backup Block</label>
                    <div class="col-sm-10">
                        <input name="backup_block[title]" type="text" class="form-control" data-bind="typeaheadJS: backup_block().title, typeaheadJSOptions: { remote: '/blocks/titles/%QUERY' }, validate: $root.errors" />
                    </div>
                </div>
                <!-- /ko -->
                <h5>Harmony</h5>
                <!-- ko foreach: harmonics -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="harmonic_key" data-bind="text: title"></label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" data-bind="checked: $parent.block_harmonic_names, checkedValue: name" />
                                <span class="text-muted" data-bind="text: description"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <!-- /ko -->
            </form>
        </div>
    </div>
</div>