<div id="settings-index" class="standard-section">
    <div class="standard-section-toolbar">
        <nav class="navbar navbar-default">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#settings-index-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div id="settings-index-collapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <button title="Save" class="btn btn-default navbar-btn" data-bind="css: { 'active': saving }, click: save"><span class="glyphicon glyphicon-floppy-saved"></span></button>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <div class="standard-section-content">
        <div class="standard-section-inner">
            <form class="form-horizontal" data-bind="foreach: categories">
                <h5 data-bind="text: human_name"></h5>
                <!-- ko foreach: settings -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" data-bind="text: human_name"></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" data-bind="nowValue: value, attr: { name: name, placeholder: human_name }" />
                    </div>
                </div>
                <!-- /ko -->
            </form>
        </div>
    </div>
</div>