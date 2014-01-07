<div class="cloudcast-navigation">
    <nav class="navbar navbar-inverse">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div id="navigation-collapse" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <!--<li class="<?php if ($section == 'Reports') echo 'active'; ?>"><a href="/schedules">REPORTS</a></li>-->
                <li class="<?php if ($section == 'Schedules') echo 'active'; ?>"><a href="/schedules">SCHEDULES</a></li>
                <li class="<?php if ($section == 'Shows') echo 'active'; ?>"><a href="/shows">SHOWS</a></li>
                <li class="<?php if ($section == 'Blocks') echo 'active'; ?>"><a href="/blocks">BLOCKS</a></li>
                <li class="<?php if ($section == 'Files') echo 'active'; ?>"><a href="/files">FILES</a></li>
                <li class="<?php if ($section == 'Streams') echo 'active'; ?>"><a href="/streams">STREAMS</a></li>
                <li class="<?php if ($section == 'Settings') echo 'active'; ?>"><a href="/settings">SETTINGS</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown<?php if ($section == 'Profile') echo ' active'; ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo Auth::get_screen_name(); ?></a>
                    <ul class="dropdown-menu">
                        <!--<li><a href="/users/edit/<?php echo Auth::get_screen_name(); ?>"><span class="glyphicon glyphicon-heart"></span> Profile</a></li>
                        <li class="divider"></li>-->
                        <li><a href="/promoter/logout"><span class="glyphicon glyphicon-off"></span> LOG OUT</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</div>