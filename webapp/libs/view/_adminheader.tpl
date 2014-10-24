<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>ThinkUp {$page}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="{$site_root_path}assets/css/vendor/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        background-color: #f5f5f5;
      }

      /* Logo */
        div#logo {
        }

        div#logo {
            padding: 0;
            color: #00aeef;
            font-size: 20px;
            line-height: 20px;
        }

        div#logo span {
            color: #404040;
            font-weight: normal;
        }
    </style>
    <link href="{$site_root_path}assets/css/vendor/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="{$site_root_path}assets/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{$site_root_path}assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{$site_root_path}assets/ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{$site_root_path}assets/ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="{$site_root_path}assets/ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="{$site_root_path}assets/ico/favicon.png">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="{$site_root_path}assets/js/vendor/bootstrap.js"></script>
    <script type="text/javascript" src="{$site_root_path}assets/js/vendor/widgets.js"></script>
  </head>

  <body>

<nav class="navbar navbar-default" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="index.php?p=1"><div id="logo">Think<span>Up</span></div></a>
  </div>

  {if $hide_admin_nav_links neq true }
  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <ul class="nav navbar-nav">
      <li{if $page eq "Subscribers"} class="active"{/if}><a href="index.php">Subscribers</a></li>
    </ul>
    <form class="navbar-form navbar-left" role="search" action="index.php">
      <div class="form-group">
        <input type="text" class="form-control" placeholder="Email, name or status" name="q">
      </div>
      <button type="submit" class="btn btn-default">Search</button>
    </form>
    <ul class="nav navbar-nav navbar-right">
      <li{if $page eq "Error Log"} class="active"{/if}><a href="errorlog.php">Error Log</a></li>
      <li{if $page eq "Upgrade"} class="active"{/if}><a href="upgrade.php">Upgrade</a></li>
      <li><a href="https://www.thinkup.com/callbax/" target="_new">Callbax</a></li>
      <li><a href="https://www.thinkup.com/dispatch/admin.php" target="_new">Dispatch</a></li>
    </ul>
  </div><!-- /.navbar-collapse -->
  {/if}
</nav>