
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>ThinkUp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

       .upstart-form {
            max-width: 300px;
            padding: 19px 29px 29px;
            margin: 0 auto 20px;
            background-color: #fff;
            border: 1px solid #e5e5e5;
            -webkit-border-radius: 5px;
               -moz-border-radius: 5px;
                    border-radius: 5px;
            -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
               -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                    box-shadow: 0 1px 2px rgba(0,0,0,.05);
       }
      .form-signin .form-signin-heading,
      .form-signin  {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
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
        .big-btn {
            display: inline-block;
            font: 400 24px/39px;    
            padding: 12px 36px;
            margin: 8px 0px;
            
            -webkit-border-radius: 8px;
            -moz-border-radius: 8px;
            border-radius: 8px;
           
            color:#fff; 
            text-shadow: #3799ca 0 -1px 0;
            box-shadow: 0 1px 1px rgba(0,0,0,0.2) ;
            
            background-color: #00AEEF;
            background-image: -moz-linear-gradient(top, #00AEEF, #0072EF);
            background-image: -ms-linear-gradient(top, #00AEEF, #0072EF);
            background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#00AEEF), to(#0072EF));
            background-image: -webkit-linear-gradient(top, #00AEEF, #0072EF);
            background-image: -o-linear-gradient(top, #00AEEF, #0072EF);
            background-image: linear-gradient(top, #00AEEF, #0072EF);
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#00AEEF', endColorstr='#0072EF', GradientType=0);
        }
        
        .big-btn:link, 
        .big-btn:visited {
            color:#ffffff;
        }
        
        .big-btn:hover {    
            color: #ffffff; 
            text-decoration: none;  
            
            background-color: #4d90fe;  
            background-image: -moz-linear-gradient(top, #4d90fe, #4787ed);
            background-image: -ms-linear-gradient(top, #4d90fe, #4787ed);
            background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#4d90fe), to(#4787ed));
            background-image: -webkit-linear-gradient(top, #4d90fe, #4787ed);
            background-image: -o-linear-gradient(top, #4d90fe, #4787ed);
            background-image: linear-gradient(top, #4d90fe, #4787ed);
            
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#4d90fe', endColorstr='#4787ed', GradientType=0);
            
            
        }
    </style>
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">
    <link rel="shortcut icon" href="assets/ico/favicon.png">
  </head>

  <body>

    <div class="container">

      <div class="upstart-form">
          <form class="form-signin" method="post" action="">
            <div id="logo"><h1>Think<span>Up</span></h1></div>
            {if $error_msg}<div class="alert alert-error">{$error_msg}</div>{/if}
            {if $success_msg}
            <div class="alert alert-success">{$success_msg}</div>
            <a href="https://twitter.com/thinkup" class="twitter-follow-button" data-show-count="true" data-size="large" data-lang="en">Follow @thinkup</a>
            {literal}
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
{/literal}
            {else}
            <input type="text" class="input-block-level" placeholder="Email address" name="email" value="{$prefill_email}">
            <button class="btn btn-large btn-primary btn-info big-btn" type="submit">Get on the waiting list</button>
            {/if}
          </form>
        </div>
    </div> <!-- /container -->

  </body>
</html>
