<script>
    var users_js = <?php echo Format::forge($users)->to_json(); ?>
</script>
<div id="users-index" class="cloudcast-section">
    <table class="table">
        <thead>
        <tr>
            <th></th>
            <th><a href="#" data-bind="orderable: { collection: 'users', field: 'username' }">Username</a></th>
            <th><a href="#" data-bind="orderable: { collection: 'users', field: 'group_name' }">Group</a></th>
            <th><a href="#" data-bind="orderable: { collection: 'users', field: 'first_name' }">First Name</a></th>
            <th><a href="#" data-bind="orderable: { collection: 'users', field: 'last_name' }">Last Name</a></th>
            <th><a href="#" data-bind="orderable: { collection: 'users', field: 'email' }">Email</a></th>
            <th><a href="#" data-bind="orderable: { collection: 'users', field: 'phone' }">Phone</a></th>
            <th><a href="#" data-bind="orderable: { collection: 'users', field: 'user_last_login' }">Last Login</a></th>
        </tr>
        </thead>
        <tbody data-bind="foreach: users">
        <tr>
            <td>
                <div class="cloudcast-item-section">
                    <a name="delete" title="Delete" class="btn btn-mini btn-danger" href="#" data-bind="click: $parent.delete"><i class="icon-remove"></i></a>
                </div>
                <div class="cloudcast-item-section">
                    <a class="btn btn-mini" title="Edit" href="#" data-bind="attr: { href: 'users/edit/' + username }"><i class="icon-edit"></i></a>
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