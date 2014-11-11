{assign var="tagline" value="The best of the web, half of the price."}
{assign var="tagline_logo" value="The best of the web, for half the price."}
{assign var="description" value="Get memberships to 5 of the web's most fun apps and communities, for 50% off."}
<!DOCTYPE html>
<html lang="en" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <title>{if isset($controller_title)}{$controller_title} | {/if}{$tagline}</title>
    <link rel="shortcut icon" type="image/x-icon" href="/join/bundle/assets/img/favicon.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/join/bundle/assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/join/bundle/assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/join/bundle/assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="/join/bundle/assets/ico/apple-touch-icon-57-precomposed.png">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="The Good Web Bundle">

    <meta property="og:site_name" content="The Good Web Bundle" />
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@goodwebbundle">
    <meta name="twitter:domain" content="goodwebbundle.com">

    <meta property="og:url" content="http://goodwebbundle.com/" />

    <meta itemprop="name" content="The Good Web Bundle">
    <meta name="twitter:title" content="The Good Web Bundle: The best of the web, half of the price.">
    <meta property="og:title" content="The Good Web Bundle: The best of the web, half of the price." />

    <meta itemprop="description" content="Get memberships to 5 of the web's most fun apps and communities, for 50% off.">
    <meta name="description" content="Get memberships to 5 of the web's most fun apps and communities, for 50% off.">
    <meta name="twitter:description" content="Get memberships to 5 of the web's most fun apps and communities, for 50% off.">

    <meta itemprop="image" content="http://goodwebbundle.com/join/bundle}/assets/img/bundle-black.png">
    <meta property="og:image" content="http://goodwebbundle.com/join/bundle}/assets/img/bundle-black.png" />
    <meta property="og:image:secure" content="http://goodwebbundle.com/join/bundle}/assets/img/bundle-black.png" />
    <meta name="twitter:image:src" content="http://goodwebbundle.com/join/bundle}/assets/img/bundle-black.png">

    <meta property="og:image:type" content="image/png">
    <meta name="twitter:image:width" content="270">
    <meta name="twitter:image:height" content="304">
    <meta name="og:image:width" content="270">
    <meta name="og:image:height" content="304">
    <meta name="twitter:creator" content="@goodwebbundle">

    <!-- TU JS namespace -->
    <script>window.tu = {};</script>

    <!-- styles -->

    <script type="text/javascript" src="//use.typekit.net/xzh8ady.js"></script>
    <script type="text/javascript">{literal}try{Typekit.load();}catch(e){}{/literal}</script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script type="text/javascript" src="{$site_root_path}bundle/assets/js/jquery.scrollTo.js"></script>
    <script type="text/javascript" src="{$site_root_path}bundle/assets/js/noconflict.js"></script>
  {literal}
    <script type="text/javascript">
      jQuery(document).ready(function() {
      $('#navbar').onePageNav();
      });
    </script>
  {/literal}

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="{$site_root_path}bundle/assets/css/fonts.css">

    {literal}
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-76614-5', 'auto');

    </script>
    {/literal}

    <style>
        /* Move down content because we have a fixed navbar that is 50px tall */
        body {
            /*
        padding-top: 50px;
        padding-bottom: 20px;
            */
            font-family: 'Alte Haas Grotesk', sans-serif;
        }

        a { color: black; }

        a:hover { color: black; }

    b, strong { font-family: "DIN Bold"; }

        h2 {
          font-family: 'Peaches and Cream', cursive;
          text-align: center;
          font-size: 4em;
        }

    h2 span {
      display: inline-block;
      position: relative;
      padding: 0 90px;
      z-index: 2;
    }

    #the-sites h2 span, #who-we-are h2 span { text-shadow: -2px -2px 0 white, 2px -2px 0 white, -2px 2px 0 white, 2px 2px 0 white; }

    #features h2 span { text-shadow: -2px -2px 0 #ff0042, 2px -2px 0 #ff0042, -2px 2px 0 #ff0042, 2px 2px 0 #ff0042; }

    #faq h2 span { text-shadow: -2px -2px 0 #ededed, 2px -2px 0 #ededed, -2px 2px 0 #ededed, 2px 2px 0 #ededed; }

    h2 span:before {
      content: " ";
      position: absolute;
      z-index: -1;
      top: 0;
      left: 0;
      right: 0;
      bottom: 16px;
      border-bottom: 2px solid goldenrod;
    }

    #features h2 span:before { border-bottom: 2px solid white; }

        .container {
          padding-top: 40px;
          padding-bottom: 40px;
        }
        /* centered columns styles */
        .row-centered {
            text-align:center;
        }
        .col-centered {
            display:inline-block;
            float:none;
            /* reset the text-align */
            text-align:left;
            /* inline-block space fix */
            margin-right:-4px;
        }
        .navbar-fixed-top {
          text-transform: uppercase;
          font-size: large;
          background-color: white;
        }

        .navbar .navbar-nav {
          display: inline-block;
          float: none;
          vertical-align: top;
        }

        .navbar .navbar-collapse { text-align: center; }

        button.navbar-toggle { border: 1px solid #ff0042; }

        button.navbar-toggle.collapsed { color: #ff0042; }

    #navbar li { font-family: "DIN"; }

    #navbar li.current span { border-bottom: 2px solid goldenrod; }

        .jumbotron {
          text-align: center;
          background-color: black;
          color: white;
      margin-bottom: 0;
      font-family: "DIN";
        }

    .jumbotron .container { padding-bottom: 0; }

        .jumbotron h1 {
          width: 170px;
          height: 225px;
          background: url('{$site_root_path}bundle/assets/img/good-web-bundle-logo.svg') 50% 0 no-repeat;
          font-weight: 300;
          text-transform: uppercase;
          text-indent: 100%;
          font-size: 0;
        }

        .jumbotron h2 {
          font-size: 8em;
      line-height: 0.6em;
      margin: 40px 0 10px;
      border-bottom: 0;
        }

    .jumbotron h2.button { margin-top: 0; }

    .jumbotron p {
      font-size: 24px;
      margin-bottom: 0;
    }

        h3#price {
          font: 105px "DIN Bold";
          font-weight: 700;
          color: #ff0042;

        }

        #bigpaybutton {
          font-family: 'DIN Black', sans-serif;
          font-size: xx-large;
          text-transform: uppercase;
          font-weight: 700;
          background-color: #ff0042;
          border-radius: 0;
          border: none;
        }

        .jumbotron p.fineprint {
          font-size: small;
          font-weight: 300;
          color: #ff0042;
      margin-top: -15px;
        }

        .jumbotron #fiftypercent {
          color: #ff0042;
          border-radius: 24px;
          border: 6px solid #ff0042;
          margin-top: 77px;
          padding: 26px;
          border-top: 4px solid black;
          border-bottom: 4px solid black;
          font-size: x-large;
        }

        .jumbotron #giveafriend {
          width: 263px;
          height: 250px;
      margin: 20px auto 0;
          background: url('{$site_root_path}bundle/assets/img/bundle-gift.png') 50% 20px no-repeat;
      font: 1.2em "DIN Black";
          position: relative;
}

    #giveafriend .friend {
      display: block;
      -ms-transform: rotate(-15deg);
      -webkit-transform: rotate(-15deg);
      transform: rotate(-15deg);
      position: absolute;
      width: 130px;
      top: 68px;
      left: 20px;
    }

    #giveafriend .bestie {
      color: #ff0042;
      width: 90px;
      font-size: 0.9em;
      display: block;
      -ms-transform: rotate(15deg);
      -webkit-transform: rotate(15deg);
      transform: rotate(15deg);
      position: absolute;
      top: 105px;
      left: 155px;
    }

    #the-sites {

    }

    #the-sites .row { padding-top: 30px; }

    #the-sites img {
      display: block;
      margin: 0 auto 20px;
    }

    #the-sites h3 {
      font: 1.2em "Alte Haas Grotesk Bold";
      position: relative;
      display: inline-block;
    }

    #the-sites h3.strike {
      font-size: 1.5em;
      color: #ff0042;
      padding: 0 5px;
    }

    #the-sites h3.strike:before {
      content: "";
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 3px; /* ADJUST HEIGHT TO ADD WEIGHT */
      background: #ff0042; /* CHANGE COLOR TO SUIT */
      margin: 0;
    }

    #the-sites .col-centered { text-align: center; }

        #features { background-color: #ff0042; }

        #features h2, #features h4, #features p {
          color: white;
      text-align: center;
        }

    #features h4 {
      font: 1.2em "Alte Haas Grotesk Bold";
      margin-bottom: 5px;
    }

    #features p {
          text-align: center;
      font-size: 1.1em;
      line-height: 1.7em;
        }

    #features .row p { font-size: 1em; }

    #features .row { padding-top: 30px; }

        #daysleft, #bigsavings, #goodcommunity, #notcreepy, #honest {
          padding-top: 145px;
        }

        #daysleft {
          background: url('{$site_root_path}bundle/assets/img/hourglass.svg') 50% 0 no-repeat;
        }

        #bigsavings {
          background: url('{$site_root_path}bundle/assets/img/piggybank.svg') 50% 0 no-repeat;
        }

        #goodcommunity {
          background: url('{$site_root_path}bundle/assets/img/handshake.svg') 50% 0 no-repeat;
        }

        #notcreepy {
          background: url('{$site_root_path}bundle/assets/img/spy.svg') 50% 0 no-repeat;
        }

        #honest {
          background: url('{$site_root_path}bundle/assets/img/ribbon.svg') 50% 0 no-repeat;
        }

    #who-we-are {

    }

    #who-we-are h4 {
      font: 1.2em "Alte Haas Grotesk Bold";
      margin-bottom: 5px;
      text-align: center;
    }

    #who-we-are p {
          text-align: center;
      font-size: 1.1em;
      line-height: 1.7em;
        }

    #who-we-are .row {
      text-align: center;
      padding-top: 30px;
    }

    #who-we-are .row p { font-size: 1em; }

    #faq { background-color: #ededed; }

    #faq h4 { font: 1.3em "Alte Haas Grotesk Bold"; }

    #faq .question { margin-bottom: 30px; }

    #faq h3 {
      font: 1.4em "Alte Haas Grotesk Bold";
      text-align: center;
      padding-bottom: 20px;
    }

    #faq #you-get {
      margin: 0 auto;
      width: 70%;
    }

    #faq #you-get .row { height: 50px; }

    #faq #you-get .col-md-2 strong { padding-left: 20px; }

    #faq #you-get img {
      height: 20px;
      width: auto;
      vertical-align: middle;
    }

    #get-it {
      color: #fff;
      background-color: #ff0042;
    text-align: center;
    }

    #get-it h2 { display: none; }

    #get-it h3 {
      font: 1.25em "DIN";
      text-align: center;
      margin-top: 8px;
    }

    #get-it h4 {
      font: 105px "DIN Bold";
      font-weight: 700;
      color: #000;
    }

    #get-it h5 { text-align: center; }

    #get-it ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    #get-it li { text-align: center; }

    #get-it li a {
      color: #fff;
      font-family: "Alte Haas Grotesk Bold";
    }

    #get-it form { text-align: center; }

    #get-it button {
      font-family: 'DIN Black', sans-serif;
      font-size: large;
      text-transform: uppercase;
      font-weight: 700;
      background-color: #fff;
      border-radius: 0;
      border: none;
      color: #ff0042;
      margin-top: 10px;
    }

    #share {
      background-color: #000;
      color: #fff;
      text-align: center;
    }

    #share h2 { display: none; }

    #share h3 { font: 1.4em "DIN Bold"; }

    #share ul {
      list-style: none;
      padding: 0;
      margin: 35px 0 0;
    }

    #share li {
      width: 50%;
      float: left;
    }

    #share .followus {
      height: 32px;
      display: block;
      font: 1.3em "DIN Bold";
      margin-bottom: 10px;
    }

    #share .followus img { margin-right: 10px }

    #share #follow-twitter a { color: #01b7ee; }

    #share #follow-tumblr a { color: #4b6282; }

    #share a.email {
      color: #ff0042;
    }

    </style>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>

  {literal}
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId      : 'your-app-id',
          xfbml      : true,
          version    : 'v2.1'
        });
      };

      (function(d, s, id){
         var js, fjs = d.getElementsByTagName(s)[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement(s); js.id = id;
         js.src = "//connect.facebook.net/en_US/sdk.js";
         fjs.parentNode.insertBefore(js, fjs);
       }(document, 'script', 'facebook-jssdk'));
    </script>
  {/literal}


{if isset($success_msg) || isset($error_msg)}
<div class="container">
    {if isset($success_msg)}[SUCCESS MSG] {$success_msg} {/if}
    {if isset($error_msg)}[ERROR MSG] {$error_msg} {/if}

    {if isset($claim_code)}
    <p>Here's your claim code:</p>
    <h3>{$claim_code_readable}</h3>
    <p>Transaction: {$transaction_id}<br>
    Reference: {$reference_id}</p>
    {/if}
</div>
{else}

    <nav class="navbar navbar-fixed-top" role="navigation">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="current"><a class="active" href="#home"><span>Home</span></a></li>
            <li><a href="#the-sites"><span>The Sites</span></a></li>
            <li><a href="#features"><span>Bundle Features</span></a></li>
            <li><a href="#who-we-are"><span>Who We Are</span></a></li>
            <li><a href="#faq"><span>FAQ</span></a></li>
            <li><a href="#get-it"><span>Get It!</span></a></li>
            <li><a href="#share"><span>Share</span></a></li>
          </ul>

        </div><!--/.navbar-collapse -->
    </nav>

    <div id="home" class="jumbotron">
      <div class="container">
        <h1 class="center-block">{$title}</h1>
        <h2>{$tagline}</h2>
        <p>Memberships to <strong>five</strong> of the most <strong>fun and entertaining</strong> apps and communities on the web!</p>
        <div class="row">
            <div class="col-md-4">
                <h3 id="price">$96</h3>
                <h2 class="button">
                  {$pay_with_amazon_form}
                </h2>
                <p class="fineprint">Pay with Amazon</p>
            </div>
            <div class="col-md-4">
                <h4 id="fiftypercent"><strong>50% less</strong> than you'd pay to sign up individually.</h4>
            </div>
            <div class="col-md-4">
                <h5 id="giveafriend">
          <span class="friend">Give ANY part of the bundle to a friend.</span>
          <span class="bestie">Or ALL of it to your best friend!</span>
        </h5>
            </div>
        </div>
      </div>
    </div>

    <div id="the-sites">
      <div class="container">
          <h2 class="center-block"><span>The Sites</span></h2>
        <div class="row row-centered">
          <div class="col-md-2 col-centered">
      <img src="{$site_root_path}bundle/assets/img/logos/metafilter.png" />
            <h3>MetaFilter</h3>
            <p>The most venerable community blog has an answer for everything.</p>
            <h3 class="strike">$60</h3>
          </div>
          <div class="col-md-2 col-centered">
      <img src="{$site_root_path}bundle/assets/img/logos/mlkshk.svg" />
            <h3>MLKSHK</h3>
            <p>The most fun and delightful image sharing community on the web.</p>
            <h3 class="strike">$24</h3>
         </div>
          <div class="col-md-2 col-centered">
      <img src="{$site_root_path}bundle/assets/img/logos/newsblur.png" />
            <h3>NewsBlur</h3>
            <p>A newsreader that actually makes it fun to catch up on the web.</p>
            <h3 class="strike">$24</h3>
          </div>
          <div class="col-md-2 col-centered">
      <img src="{$site_root_path}bundle/assets/img/logos/thetoast.png" />
            <h3>The Toast</h3>
            <p>A delightful daily blog you will sincerely love and someday resent.</p>
            <h3 class="strike">$24</h3>
          </div>
          <div class="col-md-2 col-centered">
      <img src="{$site_root_path}bundle/assets/img/logos/thinkup.svg" />
            <h3>ThinkUp</h3>
            <p>Daily insights that help you get more out of Twitter and Facebook.</p>
            <h3 class="strike">$60</h3>
          </div>
        </div>
      </div>
    </div>

    <div id="features">
      <div class="container">
          <h2><span>Bundle Features</span></h2>
          <p class="center-block">The Good Web Bundle gives you a full membership to five of the best sites on the web for half the price. These sites are a ton of fun&mdash;people love us so much they pay to be members. You're gonna be so glad you did this.</p>
        <div class="row">
          <div class="col-md-4">
              <h4 id="daysleft" class="center-block">Only 45 days left</h4>
              <p class="center-block">This is a one-time-only offer! Don't miss out.</p>
          </div>
          <div class="col-md-4">
              <h4 id="bigsavings" class="center-block">50% Savings</h4>
              <p class="center-block">Get a $192 value for about as much as you pay for Netflix.</p>
          </div>
          <div class="col-md-4">
              <h4 id="goodcommunity" class="center-block">YES! Read the Comments.</h4>
              <p class="center-block">The best communities: On our sites, we don't tolerate abuse.</p>
          </div>

        </div>

        <div class="row">
          <div class="col-md-2">
            &nbsp;
          </div>
          <div class="col-md-4">
              <h4 id="notcreepy" class="center-block">We're not creepy</h4>
              <p class="center-block">These are sites that don't track you or have too many ads.</p>
          </div>
          <div class="col-md-4">
              <h4 id="honest" class="center-block">Honesty</h4>
              <p class="center-block">You won't feel vaguely unsettled about giving your money to us.</p>
          </div>
          <div class="col-md-2">
            &nbsp;
          </div>
        </div>
      </div>
    </div>

    <div id="who-we-are">
      <div class="container">
          <h2><span>Who We Are</span></h2>
          <p>Our sites are made by nice, regular, normal people like you, with families and kids and stuff!</p>
        <div class="row">
          <div class="col-md-2">
        <img src="{$site_root_path}bundle/assets/img/people/matt-haughey.jpg" />
              <h4>Matt Haughey</h4>
              <p>MetaFilter</p>
          </div>
          <div class="col-md-2">
        <img src="{$site_root_path}bundle/assets/img/people/amber-costley.jpg" />
              <h4>Amber Costley</h4>
              <p>MLKSHK</p>
          </div>
          <div class="col-md-2">
        <img src="{$site_root_path}bundle/assets/img/people/andre-torrez.jpg" />
              <h4>Andre Torrez</h4>
              <p>MLKSHK</p>
          </div>
          <div class="col-md-2">
        <img src="{$site_root_path}bundle/assets/img/people/samuel-clay.jpg" />
              <h4>Samuel Clay</h4>
              <p>NewsBlur</p>
          </div>
          <div class="col-md-2">
        <img src="{$site_root_path}bundle/assets/img/people/mallory-ortberg.jpg" />
              <h4>Mallory Ortberg</h4>
              <p>The Toast</p>
          </div>
          <div class="col-md-2">
        <img src="{$site_root_path}bundle/assets/img/people/nicole-cliffe.jpg" />
              <h4>Nicole Cliffe</h4>
              <p>The Toast</p>
          </div>
        </div>

        <div class="row">
      <div class="col-md-3">&nbsp;</div>
      <div class="col-md-2">
        <img src="{$site_root_path}bundle/assets/img/people/nick-pavich.jpg" />
        <h4>Nick Pavich</h4>
        <p>The Toast</p>
      </div>
      <div class="col-md-2">
        <img src="{$site_root_path}bundle/assets/img/people/anil-dash.jpg" />
          <h4>Anil Dash</h4>
          <p>ThinkUp</p>
      </div>
      <div class="col-md-2">
        <img src="{$site_root_path}bundle/assets/img/people/gina-trapani.jpg" />
          <h4>Gina Trapani</h4>
          <p>ThinkUp</p>
      </div>
      <div class="col-md-3">&nbsp;</div>
        </div>

      </div>
    </div>

    <div id="faq">
      <div class="container">
          <h2><span>FAQ</span></h2>

        <div class="row">
          <div class="col-md-6">
        <div class="question">
                  <h4>Why should I pay for this stuff? The web is supposed to be free!</h4>
                  <p>In the past decade, it’s become the norm for web sites, services, and communities to provide free access to everyone, which is great — unless you care about your privacy, your data, and the longevity of the services you’re using. Because in order to provide you #content, those sites need to find a way to make money. Most often, they do that by selling you, the user, to someone else — like advertisers, data-mining companies, email harvesters, and so on.</p>
        </div>
        <div class="question">
          <h4>Curabitur pulvinar mi et malesuada tincidunt?</h4>
          <p>Vivamus ac bibendum metus. Fusce sit amet justo leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Quisque tristique dolor sem.</p>
        </div>
          </div>
          <div class="col-md-6">
        <div class="question">
          <h4>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer varius enim nec sem tincidunt?</h4>
          <p>Donec cursus vel tortor et aliquet. Curabitur rhoncus lorem elit, eget tincidunt velit ultrices auctor. Nam mattis a mauris a lacinia. Vivamus in quam velit. Donec luctus accumsan sapien in venenatis. Nunc convallis lacinia erat quis auctor.</p>
        </div>
        <div class="question">
                  <h4>Aenean hendrerit justo ac nulla dapibus, sed euismod enim malesuada. Nam in magna?</h4>
                  <p>Duis imperdiet, metus in vestibulum vehicula, odio dolor feugiat elit, eu semper orci arcu ut arcu. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis molestie volutpat risus eu luctus. Sed semper tristique risus, sed condimentum nisi placerat vel. Sed id porttitor nisl.</p>
        </div>
          </div>
        </div>

          <h3>Let's break it down. You get:</h3>

      <div id="you-get">
        <div class="row">
          <div class="col-md-1"><img src="{$site_root_path}bundle/assets/img/logos/metafilter.png" /></div>
          <div class="col-md-2"><strong>MetaFilter:</strong></div>
          <div class="col-md-9">One year paid member account, with lifetime right to post and ask questions.</div>
        </div>
        <div class="row">
          <div class="col-md-1"><img src="{$site_root_path}bundle/assets/img/logos/mlkshk.svg" /></div>
          <div class="col-md-2"><strong>MLKSHK:</strong></div>
          <div class="col-md-9">One year paid member account, with lifetime right to post and ask questions.</div>
              </div>
        <div class="row">
          <div class="col-md-1"><img src="{$site_root_path}bundle/assets/img/logos/newsblur.png" /></div>
          <div class="col-md-2"><strong>NewsBlur:</strong></div>
          <div class="col-md-9">One year paid member account, with extra cheese.</div>
              </div>
        <div class="row">
          <div class="col-md-1"><img src="{$site_root_path}bundle/assets/img/logos/thetoast.png" /></div>
          <div class="col-md-2"><strong>The Toast:</strong></div>
          <div class="col-md-9">Become a sponsoring member and be recognized on the site.</div>
              </div>
        <div class="row">
          <div class="col-md-1"><img src="{$site_root_path}bundle/assets/img/logos/thinkup.svg" /></div>
          <div class="col-md-2"><strong>ThinkUp:</strong></div>
          <div class="col-md-9">One year paid member account with insights about Twitter and Facebook.</div>
              </div>
      </div>

      </div>
    </div>

  <div id="get-it">
    <div class="container">

      <h2>Get It!</h2>

      <div class="col-md-2">
        <img src="{$site_root_path}bundle/assets/img/good-web-bundle-logo-blk.svg" alt="Good Web Bundle" />
      </div>

      <div class="col-md-3">
        <h5>Membership access to:</h5>
        <ul>
          <li><a href="http://metafilter.com/" target="_blank">MetaFilter</a></li>
          <li><a href="http://mlkshk.com/" target="_blank">MLKSHK</a></li>
          <li><a href="http://newsblur.com/" target="_blank">NewsBlur</a></li>
          <li><a href="http://the-toast.net/" target="_blank">The Toast</a></li>
          <li><a href="https://thinkup.com/" target="_blank">ThinkUp</a></li>
        </ul>
      </div>

      <div class="col-md-3">
        <h4>$96</h4>
      </div>

      <div class="col-md-4">
        <h3>50% less than you'd pay to sign up individually.<br />
          For a limited time.</h3>
          {$pay_with_amazon_form}
      </div>
  </div>
{/if}


  <div id="share">
    <div class="container">

      <h2>Share</h2>

      <div class="col-md-6">
        <h3>Keep up with us on social media:</h3>
          <ul>
            <li id="follow-twitter">
        <span class="followus"><img src="{$site_root_path}bundle/assets/img/twitter.png" /><a href="http://twitter.com/goodwebbundle">@goodwebbundle</a></span>

              <a href="https://twitter.com/goodwebbundle" class="twitter-follow-button" data-show-count="false" data-size="large">Follow @goodwebbundle</a>
              {literal}
              <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
              {/literal}
            </li>
            <li id="follow-tumblr">
          <span class="followus"><img src="{$site_root_path}bundle/assets/img/tumblr.png" /><a href="http://goodwebbundle.tumblr.com/">goodwebbundle</a></span>

          <a href="https://www.tumblr.com/follow/goodwebbundle"><img src="{$site_root_path}bundle/assets/img/tumblr-follow.png" /></a>

              <!--<iframe class="btn" frameborder="0" border="0" scrolling="no" allowtransparency="true" height="25" width="200" src="http://platform.tumblr.com/v1/follow_button.html?button_type=1&tumblelog=goodwebbundle&color_scheme=dark"></iframe>-->
            </li>
          </ul>
      </div>

      <div class="col-md-6">
        <p>
          <h3>Tell your friends!</h3>
          <a href="https://twitter.com/share" class="twitter-share-button" data-via="goodwebbundle">Tweet</a>
          {literal}
          <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
          {/literal}

          <div class="fb-share-button" data-href="http://goodwebbundle.com" data-layout="button_count"></div>
        </p>
        <p>
          <h3>Need help? Got a question? Get in touch!</h3>
          <a class="email" href="mailto:info@goodwebbundle.com">info@goodwebbundle.com</a>
        </p>
      </div>

    </div>
  </div>

  <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>

  {literal}
  <script type="text/javascript">
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

  </script>
  {/literal}

</body>

</html>
