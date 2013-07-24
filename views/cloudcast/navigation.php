<div class="navbar navbar-inverse">
    <div class="navbar-inner">
        <ul class="nav">
            <li class="<?php if ($section == 'Reports') echo 'active'; ?>"><a href="/schedules">REPORTS</a></li>
            <li class="dropdown<?php if ($section == 'Schedules') echo ' active'; ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">SCHEDULES <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="/schedules"><i class="icon-road"></i> View Schedules</a></li>
                    <li class="divider"></li>
                    <li><a href="/schedules/generate/redirect"><i class="icon-tasks"></i> Generate Schedules</a></li>
                </ul>
            </li>
            <li class="dropdown<?php if ($section == 'Shows') echo ' active'; ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">SHOWS <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="/shows"><i class="icon-calendar"></i> View Shows</a></li>
                    <li class="divider"></li>
                    <li><a href="/shows/create"><i class="icon-plus-sign"></i> Create Show</a></li>
                </ul>
            </li>
            <li class="dropdown<?php if ($section == 'Blocks') echo ' active'; ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">BLOCKS <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="/blocks"><i class="icon-th-large"></i> View Blocks</a></li>
                    <li class="divider"></li>
                    <li><a href="/blocks/create"><i class="icon-plus-sign"></i> Create Block</a></li>
                </ul>
            </li>
            <li class="dropdown<?php if ($section == 'Files') echo ' active'; ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">FILES <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="/files"><i class="icon-music"></i> View Files</a></li>
                    <li class="divider"></li>
                    <li><a href="/files/scan/redirect"><i class="icon-eye-open"></i> Scan Files</a></li>
                </ul>
            </li>
            <li class="dropdown<?php if ($section == 'Users') echo ' active'; ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">USERS <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="/users"><i class="icon-user"></i> View Users</a></li>
                    <li class="divider"></li>
                    <li><a href="/users/create"><i class="icon-plus-sign"></i> Create User</a></li>
                </ul>
            </li>
            <li class="dropdown<?php if ($section == 'Streams') echo ' active'; ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">STREAMS <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="/streams"><i class="icon-volume-up"></i> View Streams</a></li>
                    <li class="divider"></li>
                    <li><a href="/streams/create"><i class="icon-plus-sign"></i> Create Stream</a></li>
                </ul>
            </li>
            <li class="<?php if ($section == 'Settings') echo 'active'; ?>"><a href="/settings">SETTINGS</a></li>
        </ul>
        <ul class="nav pull-right">
            <li class="dropdown<?php if ($section == 'Profile') echo ' active'; ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo Auth::get_screen_name(); ?> <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="/users/edit/<?php echo Auth::get_screen_name(); ?>"><i class="icon-heart"></i> Profile</a></li>
                    <li class="divider"></li>
                    <li><a href="/users/logout"><i class="icon-off"></i> Log Out</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>