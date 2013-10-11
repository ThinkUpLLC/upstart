
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>ThinkUp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      /* Logo */
        div#logo {
        }
        
        div#logo h1 {
            margin: 18px 0 18px 0;
            padding: 0;
            color: #00aeef;
            font-size: 72px;
            line-height: 72px;
            letter-spacing: -2px;
        }
        
        div#logo h1 span {
            color: #404040;
            font-weight: normal;
        }
    </style>
    <link href="../assets/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="../assets/ico/favicon.png">
     <script type="text/javascript" src="../assets/js/widgets.js"></script>
  </head>

  <body>

    <div class="container">

<div class="row-fluid">
  <div class="span4"></div>
  <div class="span4">
    <div id="logo"><a href="?p=1"><h1>Think<span>Up</span></h1></a></div>

<h2>{$total|number_format} waitlisted</h2>
<h2>{$total_active_routes|number_format} installed</h2>
<p>Stalest dispatched:<br>
10k+ followers: {$stalest_dispatch_time_10k_up|relative_datetime} ago<br>
1k to 10k followers: {$stalest_dispatch_time_1k_to_10k|relative_datetime} ago<br>
< 1k: {$stalest_dispatch_time|relative_datetime} ago</p>
<p {if !$workers_ok} class="alert alert-danger"{/if}>Dispatch status: <b>{$worker_status}</b></p>
    <table class="table table-condensed table-hover">
      <tr>
          <th></th>
          <th>Username</th>
          <th>Waitlisted</th>
          <th>Followers</th>
          <th>Dispatched</th>
      </tr>
      {foreach $users as $user}
      <tr>
        <td> {if $user.is_verified}<img src="../assets/img/twitter_verified_icon.png" />{/if}</td>
        <td><a href="https://twitter.com/intent/user?user_id={$user.twitter_user_id}" title="{$user.email} waitlisted {$user.date_waitlisted}">@{$user.twitter_username}</a></td>
        <td>{$user.date_waitlisted|relative_datetime}&nbsp;ago</td>
        <td style="text-align:right">{$user.follower_count|number_format}</td>
        <td style="text-align:right">{if $user.route}{if $user.is_active eq 0}<a href="install.php?id={$user.id}" class="btn btn-success btn-mini">Install app</a> <cite style="color:red" title="{$user.id} is inactive"}>x</cite>{else}<a href="{$user.route}" target="_new">{$user.last_dispatched|relative_datetime}&nbsp;ago</a>{/if}{else}<a href="install.php?id={$user.id}" class="btn btn-success btn-mini">Install app</a>{/if}</td>
      </tr>
      {/foreach}
    </table>

<div class="span4"></div>

{if $prev_page}<a href="?p={$prev_page}">&larr; previous</a>{/if} {if $next_page and $prev_page}|{/if} {if $next_page}<a href="?p={$next_page}">next &rarr;</a>{/if}
</div>
</div>
    </div> <!-- /container -->


  </body>
</html>