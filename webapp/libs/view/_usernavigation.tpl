
    <div id="menu">
      <ul class="list-unstyled menu-options">
        <li><a href="{$site_root_path}">Home</a></li>
        <li><a href="{$site_root_path}user/" class="active">Settings</a></li>
        {if isset($logged_in_user)}
        <li class="service active"><a href="{$thinkup_url}account/?p=facebook">Facebook <i class="fa fa-check-circle icon"></i></a></li>
        <li class="service error"><a href="{$thinkup_url}account/?p=twitter">Twitter <i class="fa fa-exclamation-triangle icon"></i></a></li>
        <!--
        <li class="service inactive"><a href="{$thinkup_url}account/?p=instagram">Instagram <i class="fa fa-instagram icon"></i></a></li>
        -->
        <li><a href="#">Subscription</a></li>
        {/if}
        <li><a href="#">Help</a></li>
        {if isset($logged_in_user)}
        <li class="user-info logged-in">
          <img src="http://avatars.io/{$instances[1]->network}/{$instances[1]->network_username}" class="user-photo img-circle">
          <div class="current-user">
            <div class="label">Logged in as</div>
            {$logged_in_user}
          </div>
        </li>
        <li><a href="{$site_root_path}user/logout.php">Log out</a></li>
        {/if}
      </ul>
    </div>

    <div id="page-content">
      <nav class="navbar navbar-default" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button class="btn menu-trigger">
            <i class="fa fa-bars"></i>
          </button>
          <a class="navbar-brand" href="{$thinkup_url}"><strong>Think</strong>Up</span></a>
        </div>
      </nav>    
