<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Join ThinkUp: Confirm your email</title>
    <meta name="description" content="">
    {include file="_appheader.tpl"}
  </head>
  <body class="email-confirmation transactional">

    <div class="content-wrapper">
      <div class="left-column">
        <header class="page-header">
          <h1><span class="site-name"><strong>Think</strong>Up</span></h1>
          <h2>One last step</h2>
        </header>

        <div class="content">

        {if $error_msg}<div class="message warning"><i class="icon icon-warning-sign"></i>{$error_msg}</div>{/if}
        {if $success_msg}
        <div class="message success"><i class="icon icon-ok-sign"></i> {$success_msg}</div>
          <p>You're going to get in first, on January 15th. And we'll update you along the way with some cool surprises.</p>

          <div class="spread-the-word">
            <header>With that out of the way, please spread the word!</header>
            {include file="_appsharebuttons.tpl"}
          </div>
        {/if}

        </div>

        </div>
      </div><!-- end left column -->
    </div>

    {include file="_appfooter.tpl"}
  </body>
</html>
