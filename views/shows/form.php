<div id="show-form" class="standard-section">
    <div class="standard-section-toolbar">
        <nav class="navbar navbar-default">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#show-form-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div id="show-form-collapse" class="collapse navbar-collapse">
                <div id="show-form-collapse" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="active" data-bind="if: show">
                            <a data-bind="text: show().id() ? ('Edit ' + show().title()) : ('Create ' + (show().title() ? show().title() : ''))"></a>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <button title="Cancel" class="btn btn-default navbar-btn" data-bind="visible: !saving(), click: cancel"><span class="glyphicon glyphicon-floppy-remove"></span></button>
                            <button title="Save" class="btn btn-default navbar-btn" data-bind="css: { 'active': saving }, click: save"><span class="glyphicon glyphicon-floppy-saved"></span></button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <div class="standard-section-content">
        <div class="standard-section-inner">
            <form class="form-horizontal" data-bind="with: show">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="title">Title</label>
                    <div class="col-sm-10">
                        <input name="title" type="text" class="form-control" data-bind="nowValue: title, validate: $root.errors" placeholder="Title" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="description">Description</label>
                    <div class="col-sm-10">
                        <input name="description" type="text" class="form-control" data-bind="nowValue: description, validate: $root.errors" placeholder="Description" />
                    </div>
                </div>
                <h5>Time</h5>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="user_start_on">Start On</label>
                    <div class="col-sm-10">
                        <input name="user_start_on" type="text" class="form-control" data-bind="datetimepicker: user_start_on" placeholder="Start On" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="user_end_at">End At</label>
                    <div class="col-sm-10">
                        <input name="user_end_at" type="text" class="form-control" data-bind="datetimepicker: user_end_at, datetimepickerOptions: { startView: 0 }" placeholder="End At" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="duration">Duration</label>
                    <div class="col-sm-10">
                        <input name="duration" type="text" class="form-control" data-bind="nowValue: duration, validate: $root.errors" placeholder="Duration" readonly />
                    </div>
                </div>
                <h5>Block</h5>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="blocked">Blocked</label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input name="blocked" type="checkbox" data-bind="checked: blocked" />
                            </label>
                        </div>
                    </div>
                </div>
                <!-- ko if: block -->
                <div id="show_form_block">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="block">Block</label>
                        <div class="col-sm-10">
                            <input name="block[title]" type="text" class="form-control" data-bind="typeaheadJS: block().title, typeaheadJSOptions: { remote: '/blocks/titles/%QUERY' }, validate: $root.errors" />
                        </div>
                    </div>
                </div>
                <!-- /ko -->
                <h5>Repeat</h5>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="repeated">Repeated</label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input name="repeated" type="checkbox" data-bind="checked: repeated" />
                            </label>
                        </div>
                    </div>
                </div>
                <!-- ko if: show_repeat -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="description">Repeat Days</label>
                    <div class="col-sm-10" data-bind="validate: $root.errors, validateFor: 'show_repeat'">
                        <label class="checkbox-inline">
                            <input name="show_repeat[Sunday]" type="checkbox" data-bind="numChecked: show_repeat().Sunday" /> Sunday
                        </label>
                        <label class="checkbox-inline">
                            <input name="show_repeat[Monday]" type="checkbox" data-bind="numChecked: show_repeat().Monday" /> Monday
                        </label>
                        <label class="checkbox-inline">
                            <input name="show_repeat[Tuesday]" type="checkbox" data-bind="numChecked: show_repeat().Tuesday" /> Tuesday
                        </label>
                        <label class="checkbox-inline">
                            <input name="show_repeat[Wednesday]" type="checkbox" data-bind="numChecked: show_repeat().Wednesday" /> Wednesday
                        </label>
                        <label class="checkbox-inline">
                            <input name="show_repeat[Thursday]" type="checkbox" data-bind="numChecked: show_repeat().Thursday" /> Thursday
                        </label>
                        <label class="checkbox-inline">
                            <input name="show_repeat[Friday]" type="checkbox" data-bind="numChecked: show_repeat().Friday" /> Friday
                        </label>
                        <label class="checkbox-inline">
                            <input name="show_repeat[Saturday]" type="checkbox" data-bind="numChecked: show_repeat().Saturday" /> Saturday
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="show_repeat[ends]">Repeat Ends</label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input name="show_repeat[ends]" type="checkbox" data-bind="checked: show_repeat().ends" />
                            </label>
                        </div>
                    </div>
                </div>
                <!-- ko if: show_repeat().ends -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="show_repeat[user_end_on]">Repeat Ends On</label>
                    <div class="col-sm-10">
                        <input class="datetime" name="show_repeat[user_end_on]" type="text" class="form-control" data-bind="datetimepicker: show_repeat().user_end_on, validate: $root.errors" placeholder="End On" />
                    </div>
                </div>
                <!-- /ko -->
                <!-- /ko -->
                <h5>Hosting</h5>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="hosted">Hosted</label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input name="hosted" type="checkbox" data-bind="checked: hosted" />
                            </label>
                        </div>
                    </div>
                </div>
                <!-- ko if: show_users().length > 0 -->
                <div data-bind="foreach: show_users">
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="text" class="form-control" placeholder="Username"
                                   data-bind="typeaheadJS: user().username, typeaheadJSOptions: { remote: '/users/usernames/%QUERY' }, attr: { name: 'show_users[' + $index() + '][user][username]' }, validate: $root.errors" />
                            <select class="form-control" data-bind="options: $parents[1].input_names, nowValue: input_name"></select>
                            <button name="remove" type="button" class="btn btn-danger" data-bind="visible: ($parent.show_users().length > 1), click: $parent.remove_show_user"><span class="glyphicon glyphicon-remove"></span></button>
                            <button name="add" type="button" class="btn btn-info" data-bind="visible: $index() == ($parent.show_users().length - 1), click: $parent.add_show_user"><span class="glyphicon glyphicon-plus"></span></button>
                        </div>
                    </div>
                </div>
                <!-- /ko -->
                <h5>Promos</h5>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="sweepers">Sweepers</label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input name="sweepers" type="checkbox" data-bind="checked: sweepers" />
                            </label>
                        </div>
                    </div>
                </div>
                <!-- ko if: sweepers -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="sweepers_album">Sweepers Album</label>
                    <div class="col-sm-10">
                        <input name="sweepers_album" type="text" class="form-control" data-bind="nowValue: sweepers_album" placeholder="Sweepers Album" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="sweeper_interval">Sweeper Interval</label>
                    <div class="col-sm-10">
                        <input name="sweeper_interval" type="text" class="form-control" data-bind="nowValue: sweeper_interval, validate: $root.errors" placeholder="Sweeper Interval" />
                    </div>
                </div>
                <!-- /ko -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="jingles">Jingles</label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input name="jingles" type="checkbox" data-bind="checked: jingles" />
                            </label>
                        </div>
                    </div>
                </div>
                <!-- ko if: jingles -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="jingles_album">Jingles Album</label>
                    <div class="col-sm-10">
                        <input name="jingles_album" type="text" class="form-control" data-bind="nowValue: jingles_album" placeholder="Jingles Album" />
                    </div>
                </div>
                <!-- /ko -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="bumpers">Bumpers</label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input name="bumpers" type="checkbox" data-bind="checked: bumpers" />
                            </label>
                        </div>
                    </div>
                </div>
                <!-- ko if: bumpers -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="bumpers_album">Bumpers Album</label>
                    <div class="col-sm-10">
                        <input name="bumpers_album" type="text" class="form-control" data-bind="nowValue: bumpers_album" placeholder="Bumpers Album" />
                    </div>
                </div>
                <!-- /ko -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="intros">Intros</label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input name="intros" type="checkbox" data-bind="checked: intros" />
                            </label>
                        </div>
                    </div>
                </div>
                <!-- ko if: intros -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="intros_album">Intros Album</label>
                    <div class="col-sm-10">
                        <input name="intros_album" type="text" class="form-control" data-bind="nowValue: intros_album" placeholder="Intros Album" />
                    </div>
                </div>
                <!-- /ko -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="closers">Closers</label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input name="closers" type="checkbox" data-bind="checked: closers" />
                            </label>
                        </div>
                    </div>
                </div>
                <!-- ko if: closers -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="closers_album">Closers Album</label>
                    <div class="col-sm-10">
                        <input name="closers_album" type="text" class="form-control" data-bind="nowValue: closers_album" placeholder="Closers Album" />
                    </div>
                </div>
                <!-- /ko -->
            </form>
        </div>
    </div>
</div>