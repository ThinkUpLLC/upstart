{if isset($success_msg)}[SUCCESS MSG] {$success_msg} {/if}
{if isset($error_msg)}[ERROR MSG] {$error_msg} {/if}

{if isset($claim_code)}
<p>Here's your claim code:</p>
<h3>{$claim_code_readable}</h3>
<p>Transaction: {$transaction_id}<br>
Reference: {$reference_id}</p>

{else}


{assign var="tagline" value="The best of the web, half of the price."}
{assign var="tagline_logo" value="The best of the web, for half the price."}
{assign var="description" value="Get memberships to 5 of the web's most fun apps and communities, for 50% off."}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{if isset($controller_title)}{$controller_title} | {/if}The Good Web Bundle - {$tagline}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{$site_root_path}assets/img/favicon.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{$site_root_path}assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{$site_root_path}assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{$site_root_path}assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="{$site_root_path}assets/ico/apple-touch-icon-57-precomposed.png">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="The Good Web Bundle">

    <meta property="og:site_name" content="The Good Web Bundle" />
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@goodwebbundle">
    <meta name="twitter:domain" content="goodwebbundle.com">

    <meta property="og:url" content="https://www.goodwebbundle.com/" />

    <meta itemprop="name" content="The Good Web Bundle">
    <meta name="twitter:title" content="The Good Web Bundle: {$tagline}">
    <meta property="og:title" content="The Good Web Bundle: {$tagline}" />

    <meta itemprop="description" content="{$description}">
    <meta name="description" content="{$description}">
    <meta name="twitter:description" content="{$description}">

    <meta itemprop="image" content="https://www.goodwebbundle.com/join/assets/img/landing/crowd.png">
    <meta property="og:image" content="https://www.goodwebbundle.com/join/assets/img/landing/crowd.png" />
    <meta property="og:image:secure" content="https://www.goodwebbundle.com/join/assets/img/landing/crowd.png" />
    <meta name="twitter:image:src" content="https://www.goodwebbundle.com/join/assets/img/landing/crowd.png">

    <meta name="og:image:type" content="image/png">
    <meta name="twitter:image:width" content="160">
    <meta name="twitter:image:height" content="154">
    <meta name="og:image:width" content="160">
    <meta name="og:image:height" content="154">
    <meta name="twitter:creator" content="@goodwebbundle">

    <!-- TU JS namespace -->
    <script>window.tu = {};</script>

    <!-- styles -->
    {literal}<script type="text/javascript" src="//use.typekit.net/xzh8ady.js"></script>
    <script type="text/javascript">try{Typekit.load();}catch(e){}</script>{/literal}
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">

    {literal}<script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-76614-5', 'auto');

    </script>{/literal}

    <style>
        /* Move down content because we have a fixed navbar that is 50px tall */
        body {
            /*
        padding-top: 50px;
        padding-bottom: 20px;
            */
        }

        .jumbotron {
            text-align: center;
            background-color: black;
            color: white;
        }
    </style>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand active" href="https://www.goodwebbundle.com/">Home</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class=""><a href="#the-sites">The Sites</a></li>
            <li><a href="#features">Bundle Features</a></li>
            <li><a href="#who-we-are">Who We Are</a></li>
            <li><a href="#faq">FAQ</a></li>
            <li><a href="#get-it">Get It!</a></li>
            <li><a href="#share">Share</a></li>
          </ul>

        </div><!--/.navbar-collapse -->
      </div>
    </nav>

    <div class="jumbotron">
      <div class="container">
        <h2>{$title}</h2>
        <h1>{$tagline}</h1>
        <p>Memberships to <strong>five</strong> of the most <strong>fun and entertaining</strong> apps and communities on the web!</p>
        <div class="row">
            <div class="col-md-4">
                <h1>$96</h1>
                <h2>{if isset($pay_with_amazon_form)}{$pay_with_amazon_form}{/if}</h2>
                <p>Pay with Amazon</p>
            </div>
            <div class="col-md-4">
                <h4><strong>50% less than you'd pay to sign up individually</strong></h4>
            </div>
            <div class="col-md-4">
                <h5>Give ANY part of the bundle to a friend.</h5>
                <h5>Or ALL of it to your best friend!</h5>
            </div>
        </div>
      </div>
    </div>


    <div class="container">
      <div class="row">
        <div class="col-md-2">
          <h2>MetaFilter</h2>
          <p>The web's most venerable community blog, with an answer for everything.</p>
          <h3><strike>$60</strike></h3>
        </div>
        <div class="col-md-2">
          <h2>MLKSHK</h2>
          <p>The most fun and delightful image sharing community on the web.</p>
          <h3><strike>$24</strike></h3>
       </div>
        <div class="col-md-2">
          <h2>NewsBlur</h2>
          <p>A newsreader that actually makes it fun to catch up on the web.</p>
          <h3><strike>$24</strike></h3>
        </div>
        <div class="col-md-2">
          <h2>The Toast</h2>
          <p>A delightful daily blog you will sincerely love and someday resent.</p>
          <h3><strike>$24</strike></h3>
        </div>
        <div class="col-md-2">
          <h2>ThinkUp</h2>
          <p>Daily insights that help you get more out of Twitter and Facebook.</p>
          <h3><strike>$60</strike></h3>
        </div>
      </div>
    </div>


    <div class="container">
        <h2>Bundle Features</h2>
        <p>The Good Web Bundle gives you a full membership to five of the best sites on the web for half the price. These are sites that are a ton of fun&mdash;sites people love so much they pay to be members. You're gonna be so glad you did this.</p>
      <div class="row">
        <div class="col-md-4">
            <h4>Only 45 days left</h4>
            <p>This is a one-time-only offer! Don't miss out.</p>
        </div>
        <div class="col-md-4">
            <h4>50% Savings</h4>
            <p>Get a $192 value for about as much as you pay for Netflix.</p>
        </div>
        <div class="col-md-4">
            <h4>We're not creepy</h4>
            <p>These are sites that don't track you or have too many ads.</p>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
            <h4>The best communities</h4>
            <p>On our sites, we don't tolerate abuse. Let's all be cool!</p>
        </div>
        <div class="col-md-6">
            <h4>Honesty &amp; transparency</h4>
            <p>You won't feel vaguely unsettled about giving your money to us.</p>
        </div>
      </div>
    </div>


    <div class="container">
        <h2>Who We Are</h2>
        <p>Our sites are made by nice, regular, normal people like you, with families and kids and stuff!</p>
      <div class="row">
        <div class="col-md-2">
            <h4>Matt Haughey</h4>
            <p>MetaFilter</p>
        </div>
        <div class="col-md-2">
            <h4>Amber Costley</h4>
            <p>MLKSHK</p>
        </div>
        <div class="col-md-2">
            <h4>Andre Torrez</h4>
            <p>MLKSHK</p>
        </div>
        <div class="col-md-2">
            <h4>Samuel Clay</h4>
            <p>NewsBlur</p>
        </div>
        <div class="col-md-2">
            <h4>Mallory Ortberg</h4>
            <p>The Toast</p>
        </div>
        <div class="col-md-2">
            <h4>Nicole Cliffe</h4>
            <p>The Toast</p>
        </div>
      </div>

      <div class="row">
        <div class="col-md-2">
            <h4>Roxane Gay</h4>
            <p>The Toast</p>
        </div>
        <div class="col-md-2">
            <h4>Anil Dash</h4>
            <p>ThinkUp</p>
        </div>
        <div class="col-md-2">
            <h4>Gina Trapani</h4>
            <p>ThinkUp</p>
        </div>
      </div>

    </div>

    <div class="container">
        <h2>FAQ</h2>


      <div class="row">
        <div class="col-md-6">
            <h4>Why should I pay for this stuff? The web is supposed to be free!</h4>
            <p>In the past decade, it's become the norm for web sites</p>
        </div>
        <div class="col-md-6">
            <h4>Lorem Ipsum?</h4>
            <p>Because I said so!</p>
        </div>
      </div>

        <h3>Let's break it down. You get:</h3>

        <ul>
            <li><strong>MetaFilter:</strong> One year paid member account, with lifetime right to post and ask questions.</li>

            <li><strong>MLKSHK:</strong> One year paid member account, with lifetime right to post and ask questions.</li>

            <li><strong>NewsBlur:</strong> One year paid member account, with extra cheese.</li>

            <li><strong>The Toast:</strong> Become a sponsoring member and be recognized on the site.</li>

            <li><strong>MetaFilter:</strong> One year paid member account with insights about Twitter and Facebook.</li>
        </ul>
    </div>


<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>

{literal}<script type="text/javascript">
ga('send', 'pageview');

var _sf_async_config={uid:2383,domain:"thinkup.com"};
(function(){
  function loadChartbeat() {
    window._sf_endpt=(new Date()).getTime();
    var e = document.createElement('script');
    e.setAttribute('language', 'javascript');
    e.setAttribute('type', 'text/javascript');
    e.setAttribute('src',
       (("https:" == document.location.protocol) ? "https://a248.e.akamai.net/chartbeat.download.akamai.com/102508/" : "http://static.chartbeat.com/") +
       "js/chartbeat.js");
    document.body.appendChild(e);
  }
  var oldonload = window.onload;
  window.onload = (typeof window.onload != 'function') ?
     loadChartbeat : function() { oldonload(); loadChartbeat(); };
})();

</script>{/literal}


</body>

</html>

{/if}
