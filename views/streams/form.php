<div id="stream-form" class="standard-section">
    <div class="standard-section-toolbar">
        <nav class="navbar navbar-default">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#stream-form-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div id="stream-form-collapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active" data-bind="if: stream">
                        <a data-bind="text: stream().id() ? ('Edit ' + stream().name()) : ('Create ' + (stream().name() ? stream().name() : ''))"></a>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <button title="Cancel" class="btn btn-default navbar-btn" data-bind="click: cancel"><span class="glyphicon glyphicon-floppy-remove"></span></button>
                        <button title="Save" class="btn btn-default navbar-btn" data-bind="css: { 'active': saving }, click: save"><span class="glyphicon glyphicon-floppy-saved"></span></button>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <div class="standard-section-content">
        <div class="standard-section-inner">
            <form class="form-horizontal" data-bind="with: stream">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Name</label>
                    <div class="col-sm-10">
                        <input name="name" type="text" class="form-control" data-bind="nowValue: name, validate: $root.errors" placeholder="Name" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="type">Type</label>
                    <div class="col-sm-10">
                        <select name="type" class="form-control" data-bind="nowValue: type, validate: $root.errors">
                            <?php foreach(Model_Stream::$types as $type): ?>
                            <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <!-- ko if: type() != 'local' -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="host">Host</label>
                    <div class="col-sm-10">
                        <input name="host" type="text" class="form-control" data-bind="nowValue: host, validate: $root.errors" placeholder="Host" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="port">Port</label>
                    <div class="col-sm-10">
                        <input name="port" type="text" class="form-control" data-bind="nowValue: port, validate: $root.errors" placeholder="Port" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="format">Format</label>
                    <div class="col-sm-10">
                        <input name="port" type="text" class="form-control" data-bind="nowValue: format, validate: $root.errors" placeholder="Format" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="source_username">Source Username</label>
                    <div class="col-sm-10">
                        <input name="source_username" type="text" class="form-control" data-bind="nowValue: source_username, validate: $root.errors" placeholder="Source Username" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="source_password">Source Password</label>
                    <div class="col-sm-10">
                        <input name="source_password" type="password" class="form-control" data-bind="nowValue: source_password, validate: $root.errors" placeholder="Source Password" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="admin_username">Admin Username</label>
                    <div class="col-sm-10">
                        <input name="admin_username" type="text" class="form-control" data-bind="nowValue: admin_username, validate: $root.errors" placeholder="Admin Username" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="admin_password">Admin Password</label>
                    <div class="col-sm-10">
                        <input name="admin_password" type="password" class="form-control" data-bind="nowValue: admin_password, validate: $root.errors" placeholder="Admin Password" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="mount">Mount</label>
                    <div class="col-sm-10">
                        <input name="mount" type="text" class="form-control" data-bind="nowValue: mount, validate: $root.errors" placeholder="Mount" />
                    </div>
                </div>
                <!-- /ko -->
            </form>
        </div>
    </div>
</div>