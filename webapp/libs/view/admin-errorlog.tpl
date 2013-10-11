
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
    <table class="table table-condensed table-hover">
      <tr>
          <th>Location</th>
          <th>Debug</th>
      </tr>
      {foreach $errors as $error}
      <tr>
        <td><a href="{$error.github_link}">{$error.method}#L{$error.line_number}</a><br />{$error.timestamp|relative_datetime} ago</td>
        <td><pre>{$error.debug}</pre></td>
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
