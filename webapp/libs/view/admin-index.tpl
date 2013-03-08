
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
    <div id="logo"><h1>Think<span>Up</span></h1></div>

    <table class="table table-condensed table-hover">
      <tr>
          <th></th>
          <th>Username</th>
          <th>Followers</th>
          <th></th>
      </tr>
      {foreach $users as $user}
      <tr>
        <td> {if $user.is_verified}<img src="../assets/img/twitter_verified_icon.png" />{/if}</td>
        <td><a href="https://twitter.com/intent/user?user_id={$user.twitter_user_id}" title="{$user.email}">@{$user.twitter_username}</a></td>
        <td>{$user.follower_count|number_format}</td>
        <td><a href="javascript:alert('Coming soon');" class="btn btn-success btn-mini">Install app</a></td>
      </tr>
      {/foreach}
    </table>

<div class="span4"></div>
</div>
</div>

    </div> <!-- /container -->


  </body>
</html>
