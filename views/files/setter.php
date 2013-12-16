<div class="standard-sidebar" data-bind="visible: sidebar() == 'setter', with: file_setter">
    <div class="standard-sidebar-toolbar">
        <nav class="navbar navbar-default">
            <ul class="nav navbar-nav">
                <li>
                    <button title="Set Properties" class="btn btn-default navbar-btn" data-bind="css: { 'active': $root.setting }, click: $root.set_properties"><span class="glyphicon glyphicon-floppy-saved"></span></button>
                    <button title="Clear Properties" class="btn btn-default navbar-btn" data-bind="click: clear_all"><span class="glyphicon glyphicon-unchecked"></span></button>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><button title="Close Properties" class="btn btn-default navbar-btn" data-bind="click: function() { $root.sidebar(null); }"><span class="glyphicon glyphicon-remove"></span></button></li>
            </ul>
        </nav>
    </div>
    <div class="standard-sidebar-content" data-bind="foreach: file_properties">
        <div class="standard-sidebar-item" data-bind="css: { active: active }">
            <div class="standard-sidebar-item-title">
                <label class="checkbox-inline">
                    <input type="checkbox" data-bind="checked: active" />
                    <span data-bind="text: human_name"></span>
                </label>
            </div>
            <div>
                <!-- ko if: !type() -->
                <input type="text" class="form-control" data-bind="nowValue: value" />
                <!-- /ko -->
                <!-- ko if: type() == 'five' -->
                <!-- ko foreach: [1, 2, 3, 4, 5] -->
                <label class="radio-inline">
                    <input type="radio" data-bind="checked: $parent.value, checkedValue: $data" /><span data-bind="text: $data"></span>
                </label>
                <!-- /ko -->
                <!-- /ko -->
                <!-- ko if: type() == 'ten' -->
                <!-- ko foreach: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10] -->
                <label class="radio-inline">
                    <input type="radio" data-bind="checked: $parent.value, checkedValue: $data" /><span data-bind="text: $data"></span>
                </label>
                <!-- /ko -->
                <!-- /ko -->
                <!-- ko if: type() == 'bool' -->
                <label class="radio-inline">
                    <input type="radio" data-bind="checked: value, checkedValue: true" />Yes
                </label>
                <label class="radio-inline">
                    <input type="radio" data-bind="checked: value, checkedValue: false" />No
                </label>
                <!-- /ko -->
                <!-- ko if: type() == 'datetime' -->
                <input type="text" class="form-control" data-bind="datetimepicker: value" />
                <!-- /ko -->
                <!-- ko if: type() == 'duration' -->
                <input type="text" class="form-control" data-bind="nowValue: value" placeholder="00:00:00" />
                <!-- /ko -->
            </div>
        </div>
    </div>
</div>