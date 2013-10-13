
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
  <div class="span1"></div>
  <div class="span10">
    <div id="logo"><a href="?p=1"><h1>Think<span>Up</span></h1></a></div>
<h2>${$total_authorizations|number_format} pledged by {$total_subscribers|number_format} subscribers</h2>
    <table class="table table-condensed table-hover">
      <tr>
          <th></th>
          <th>Name</th>
          <th>Email</th>
          <th>Network</th>
          <th>Subscribed</th>
          <th style="text-align:right">Amount</th>
          <th>Notes</th>
          <th>Actions</th>
      </tr>
      {foreach $subscribers as $subscriber}
      <tr>
        <td> {if $subscriber->is_verified}<img src="../assets/img/twitter_verified_icon.png" />{/if}</td>
        <td>{$subscriber->full_name}</td>
        <td>{$subscriber->email}</th>
        <td>{$subscriber->network}</td>
        <td>{$subscriber->creation_time}</td>
        <td style="text-align:right">${$subscriber->amount}</td>
        <td><a title="{$subscriber->description}">{$subscriber->status_code}</a>{if $subscriber->is_email_verified eq 0}, Email not verified{/if}{if $subscriber->error_message}, Amazon error: {$subscriber->error_message}{/if}</td>
        <td>{if $smarty.now > $subscriber->token_validity_start_date_ts}
        <a href="charge.php?token_id={$subscriber->token_id}&amount={$subscriber->amount|urlencode}" class="btn btn-success btn-mini">Charge</a>{else}
        Charge after {$subscriber->token_validity_start_date}{/if}  
         </td>
      </tr>
      {/foreach}
    </table>

<div class="span1"></div>

{if $prev_page}<a href="?p={$prev_page}">&larr; previous</a>{/if} {if $next_page and $prev_page}|{/if} {if $next_page}<a href="?p={$next_page}">next &rarr;</a>{/if}
</div>
</div>
    </div> <!-- /container -->


  </body>
</html>
