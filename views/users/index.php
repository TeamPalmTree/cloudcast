<script>
    var users_js = <?php echo Format::forge($users)->to_json(); ?>
</script>
<div id="users-index" class="cloudcast-section">
    <table class="table">
        <thead>
        <tr>
            <th></th>
            <th><a data-bind="orderable: { collection: 'users', field: 'username' }">Username</a></th>
            <th><a data-bind="orderable: { collection: 'users', field: 'group_name' }">Group</a></th>
            <th><a data-bind="orderable: { collection: 'users', field: 'first_name' }">First Name</a></th>
            <th><a data-bind="orderable: { collection: 'users', field: 'last_name' }">Last Name</a></th>
            <th><a data-bind="orderable: { collection: 'users', field: 'email' }">Email</a></th>
            <th><a data-bind="orderable: { collection: 'users', field: 'phone' }">Phone</a></th>
            <th><a data-bind="orderable: { collection: 'users', field: 'user_last_login' }">Last Login</a></th>
        </tr>
        </thead>
        <tbody data-bind="foreach: users">
        <tr>
            <td>
                <div class="cloudcast-item-section">
                    <a name="delete" title="Delete" class="btn btn-default btn-xs btn-danger" data-bind="click: $parent.delete"><span class="glyphicon glyphicon-remove"></span></a>
                </div>
                <div class="cloudcast-item-section">
                    <a class="btn btn-default btn-xs" title="Edit" data-bind="attr: { href: 'users/edit/' + username }"><span class="glyphicon glyphicon-edit"></span></a>
                </div>
            </td>
            <td><strong><span data-bind="text: username"></span></strong></td>
            <td data-bind="text: group_name"></td>
            <td data-bind="text: first_name"></td>
            <td data-bind="text: last_name"></td>
            <td data-bind="text: email"></td>
            <td data-bind="text: phone"></td>
            <td data-bind="text: user_last_login"></td>
        </tr>
        </tbody>
    </table>
</div>