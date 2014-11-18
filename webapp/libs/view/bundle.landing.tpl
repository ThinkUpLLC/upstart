{assign var="tagline" value="The best of the web, half of the price."}
{assign var="tagline_logo" value="The best of the web, for half the price."}
{assign var="description" value="Get memberships to 5 of the web's best apps and communities, for 50% off."}
<!DOCTYPE html>
<html lang="en" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <title>{if isset($controller_title)}{$controller_title} | {/if}{$tagline}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{$site_root_path}bundle/assets/img/favicon.png">
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="{$site_root_path}bundle/assets/ico/apple-touch-icon-152-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{$site_root_path}bundle/assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{$site_root_path}bundle/assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{$site_root_path}bundle/assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="{$site_root_path}bundle/assets/ico/apple-touch-icon-57-precomposed.png">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="The Good Web Bundle">

    <meta property="og:site_name" content="The Good Web Bundle" />
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@goodwebbundle">
    <meta name="twitter:domain" content="goodwebbundle.com">

    <meta property="og:url" content="{$site_root_path}" />

    <meta property="name" content="The Good Web Bundle">
    <meta name="twitter:title" content="The Good Web Bundle: The best of the web, half of the price.">
    <meta property="og:title" content="The Good Web Bundle: The best of the web, half of the price." />

    <meta name="description" content="Get memberships to 5 of the web's best apps and communities, for 50% off.">
    <meta name="description" content="Get memberships to 5 of the web's best apps and communities, for 50% off.">
    <meta name="twitter:description" content="Get memberships to 5 of the web's best apps and communities, for 50% off.">

    <meta property="image" content="{$site_root_path}bundle/assets/img/bundle-black.png" />
    <meta property="og:image" content="{$site_root_path}bundle/assets/img/bundle-black.png" />
    <meta property="og:image:secure" content="{$site_root_path}bundle/assets/img/bundle-black.png" />
    <meta name="twitter:image:src" content="{$site_root_path}bundle/assets/img/bundle-black.png" />

    <meta property="og:image:type" content="image/png">
    <meta name="twitter:image:width" content="270">
    <meta name="twitter:image:height" content="304">
    <meta property="og:image:width" content="270">
    <meta property="og:image:height" content="304">
    <meta name="twitter:creator" content="@goodwebbundle">

    <!-- styles -->

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">

    <style>
        @font-face {
            font-family: 'Alte Haas Grotesk';
            src: url('{$site_root_path}bundle/assets/fonts/altehaasgroteskregular-webfont.eot');
            src: url('{$site_root_path}bundle/assets/fonts/altehaasgroteskregular-webfont.eot?#iefix') format('embedded-opentype'),
                 url('{$site_root_path}bundle/assets/fonts/altehaasgroteskregular-webfont.woff2') format('woff2'),
                 url('{$site_root_path}bundle/assets/fonts/altehaasgroteskregular-webfont.woff') format('woff'),
                 url('{$site_root_path}bundle/assets/fonts/altehaasgroteskregular-webfont.ttf') format('truetype'),
                 url('{$site_root_path}bundle/assets/fonts/altehaasgroteskregular-webfont.svg#alte_haas_groteskregular') format('svg');
            font-weight: normal;
            font-style: normal;

        }

        @font-face {
            font-family: 'Alte Haas Grotesk Bold';
            src: url('{$site_root_path}bundle/assets/fonts/altehaasgroteskbold-webfont.eot');
            src: url('{$site_root_path}bundle/assets/fonts/altehaasgroteskbold-webfont.eot?#iefix') format('embedded-opentype'),
                 url('{$site_root_path}bundle/assets/fonts/altehaasgroteskbold-webfont.woff2') format('woff2'),
                 url('{$site_root_path}bundle/assets/fonts/altehaasgroteskbold-webfont.woff') format('woff'),
                 url('{$site_root_path}bundle/assets/fonts/altehaasgroteskbold-webfont.ttf') format('truetype'),
                 url('{$site_root_path}bundle/assets/fonts/altehaasgroteskbold-webfont.svg#alte_haas_groteskbold') format('svg');
            font-weight: normal;
            font-style: normal;

        }

        @font-face {
            font-family: 'Peaches and Cream';
            src: url('{$site_root_path}bundle/assets/fonts/fenotype_-_peachesandcreamregular-webfont.eot');
            src: url('{$site_root_path}bundle/assets/fonts/fenotype_-_peachesandcreamregular-webfont.eot?#iefix') format('embedded-opentype'),
                 url('{$site_root_path}bundle/assets/fonts/fenotype_-_peachesandcreamregular-webfont.woff2') format('woff2'),
                 url('{$site_root_path}bundle/assets/fonts/fenotype_-_peachesandcreamregular-webfont.woff') format('woff'),
                 url('{$site_root_path}bundle/assets/fonts/fenotype_-_peachesandcreamregular-webfont.ttf') format('truetype'),
                 url('{$site_root_path}bundle/assets/fonts/fenotype_-_peachesandcreamregular-webfont.svg#peaches_and_cream_regularRg') format('svg');
            font-weight: normal;
            font-style: normal;

        }

        @font-face {
            font-family: 'DIN Bold';
            src: url('{$site_root_path}bundle/assets/fonts/din-bold-webfont-webfont.eot');
            src: url('{$site_root_path}bundle/assets/fonts/din-bold-webfont-webfont.eot?#iefix') format('embedded-opentype'),
                 url('{$site_root_path}bundle/assets/fonts/din-bold-webfont-webfont.woff2') format('woff2'),
                 url('{$site_root_path}bundle/assets/fonts/din-bold-webfont-webfont.woff') format('woff'),
                 url('{$site_root_path}bundle/assets/fonts/din-bold-webfont-webfont.ttf') format('truetype'),
                 url('{$site_root_path}bundle/assets/fonts/din-bold-webfont-webfont.svg#dinbold') format('svg');
            font-weight: normal;
            font-style: normal;

        }

        @font-face {
            font-family: 'DIN Black';
            src: url('{$site_root_path}bundle/assets/fonts/din-black-webfont-webfont.eot');
            src: url('{$site_root_path}bundle/assets/fonts/din-black-webfont-webfont.eot?#iefix') format('embedded-opentype'),
                 url('{$site_root_path}bundle/assets/fonts/din-black-webfont-webfont.woff2') format('woff2'),
                 url('{$site_root_path}bundle/assets/fonts/din-black-webfont-webfont.woff') format('woff'),
                 url('{$site_root_path}bundle/assets/fonts/din-black-webfont-webfont.ttf') format('truetype'),
                 url('{$site_root_path}bundle/assets/fonts/din-black-webfont-webfont.svg#din_blackregular') format('svg');
            font-weight: normal;
            font-style: normal;

        }

        @font-face {
            font-family: 'DIN';
            src: url('{$site_root_path}bundle/assets/fonts/din-regular-webfont-webfont.eot');
            src: url('{$site_root_path}bundle/assets/fonts/din-regular-webfont-webfont.eot?#iefix') format('embedded-opentype'),
                 url('{$site_root_path}bundle/assets/fonts/din-regular-webfont-webfont.woff2') format('woff2'),
                 url('{$site_root_path}bundle/assets/fonts/din-regular-webfont-webfont.woff') format('woff'),
                 url('{$site_root_path}bundle/assets/fonts/din-regular-webfont-webfont.ttf') format('truetype'),
                 url('{$site_root_path}bundle/assets/fonts/din-regular-webfont-webfont.svg#dinregular') format('svg');
            font-weight: normal;
            font-style: normal;

        }

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

        table {
          overflow: scroll;
        }

        td {
          padding: 8px;
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
          min-height: 40px;
          text-transform: uppercase;
          font-size: large;
          background-color: transparent;
        }

        .navbar-header {
          background-color: transparent;
          height: 40px;
        }

        .navbar .navbar-nav {
          display: inline-block;
          float: none;
          vertical-align: top;
        }

        .navbar .navbar-collapse { text-align: center; }

        button.navbar-toggle {
          /*
          margin-left: 40%;
          margin-right: 40%;
          width: 20%;
          */
          margin-top: 0;
          margin-bottom: 0;
          border-radius: 0;
          padding: 0;
          width: 40px;
          height: 40px;
          float: left;
          background-color: #ff0042;
        }

        button.navbar-toggle.collapsed {  }

        #navbar {
          background-color: white;
          font-family: "DIN";
        }

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

        h3#price sup {
          color: #ff0042;
          font-size: 60%;
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
          font: 1.2em "DIN Bold";
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
          top: 110px;
          left: 155px;
          font-weight: lighter;
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
          background: black; /* CHANGE COLOR TO SUIT */
          margin: 0;
          -ms-transform: rotate(-15deg); /* IE 9 */
          -webkit-transform: rotate(-15deg); /* Chrome, Safari, Opera */
          transform: rotate(-15deg);
        }

        #the-sites .col-centered { text-align: center; }

        #the-sites ul {
          height: 200px;
          text-align: left;
          padding-left: 6px;
        }

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

        #features table {
          display: table;
          color: white;
        }

        #features th {
          font-weight: bolder;
          color: black;
        }

        #features a {
          color: white;
          font-weight: 700;
        }

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

        #who-we-are { }

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
          height: 30px;
          width: auto;
          vertical-align: middle;
        }

        #faq #you-get td a {
          color: #999;
        }
        #faq #you-get td a:hover {
          color: #ff0042;
        }

        #faq #you-get .col-md-11 { padding-top: 6px; }

        .redeem-explain {
          text-align: center;
        }

        .redeem-explain strong {
          color: #ff0042;
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
          text-align: center;
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

        #share #twitter-widget-0, #share .fb-share-button { vertical-align: middle; }

        #share a.email {
          color: #ff0042;
        }

    </style>

    <!-- TU JS namespace -->
    <script>window.tu = {};</script>

    <script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="{$site_root_path}bundle/assets/js/jquery.scrollTo.js"></script>
    <script type="text/javascript" src="{$site_root_path}bundle/assets/js/noconflict.js"></script>
    {literal}
    <script type="text/javascript">
      jQuery(document).ready(function() {
        $('#navbar').onePageNav();
      });
    </script>
    {/literal}

    {literal}
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-76614-5', 'auto');

    </script>
    {/literal}


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
          appId      : '133199273523556',
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

  {if isset($success_msg)}
      <div id="home" class="jumbotron">
        <div class="container">
          <h1 class="center-block">{$title}</h1>
          <h2>Thank you!</h2>
          <p>{$success_msg}</p>
          <div class="row">
              <div class="col-md-4">
                  &nbsp;
              </div>
              <div class="col-md-4">
                  {if isset($claim_code)}
                  <h4 id="fiftypercent">Your Coupon Code:<br />
                  <strong style="color: white;">{$claim_code_readable}</strong></h4>
                  {/if}
              </div>
              <div class="col-md-4">
                  &nbsp;
              </div>
          </div>
        </div>
      </div>

      <div id="faq">
        <div class="container">

            <h5 class="redeem-explain">We've sent your coupon code and these links to <strong>{$buyer_email}</strong>. Or print this page for your records.</h5>

            <h2><span>Redeem Your Code</span></h2>

            <div id="you-get">

              <table class="col-md-offset-2">
                <tr>
                  <td><img src="{$site_root_path}bundle/assets/img/logos/metafilter.svg" alt="MetaFilter" /></td>
                  <td><a href="https://login.metafilter.com/join.mefi?code={$claim_code}">https://metafilter.com/join.mefi?code={$claim_code}</a></td>
                </tr>
                <tr>
                  <td><img src="{$site_root_path}bundle/assets/img/logos/mlkshk.svg" alt="MLKSHK" /></td>
                  <td><a href="https://mlkshk.com/account/redeem?key={$claim_code}">https://mlkshk.com/account/redeem?key={$claim_code}</a></td>
                </tr>
                <tr>
                  <td><img src="{$site_root_path}bundle/assets/img/logos/newsblur.png" alt="NewsBlur" /></td>
                  <td><a href="http://www.newsblur.com/account/redeem_code?code={$claim_code}">http://newsblur.com/account/redeem_code?code={$claim_code}</a></td>
                </tr>
                <tr>
                  <td><img src="{$site_root_path}bundle/assets/img/logos/thetoast.png" alt="The Toast" /></td>
                  <td><a href="http://the-toast.net/donate/?code={$claim_code}">http://the-toast.net/donate/?code={$claim_code}</a></td>
                </tr>
                <tr>
                  <td><img src="{$site_root_path}bundle/assets/img/logos/thinkup.svg" alt="ThinkUp" /></td>
                  <td><a href="https://www.thinkup.com/join/bundle-redeem.php?code={$claim_code}">https://thinkup.com/join/bundle-redeem.php?code={$claim_code}</a></td>
                </tr>
              </table>
            </div>
        </div>
      </div>

      <div id="features">
        <div class="container">

            <h2><span>At Your Service</span></h2>

              <p class="redeem-explain">Need help? Have questions? Contact us at <a href="mailto:help@thinkup.com">help@thinkup.com</a>, and include these details:</p>


              <div class="row">
                  <div class="col-md-2">
                      &nbsp;
                  </div>
                  <div class="col-md-8">
                    <table class="center-block">
                      <tr>
                        <th>Transaction</th>
                        <td>{$transaction_id}</td>
                      </tr>
                      <tr>
                        <th>Reference</th>
                        <td>{$reference_id}</td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-2">
                      &nbsp;
                  </div>
              </div>
        </div>
      </div>

  {elseif isset($error_msg)}
      <div id="home" class="jumbotron">
        <div class="container">
          <h1 class="center-block">{$title}</h1>
          <h2>We're Sorry.</h2>
          <p>{$error_msg}</p>
          <div class="row">
              <div class="col-md-4">
                  &nbsp;
              </div>
              <div class="col-md-4">
                <h4 id="fiftypercent">Contact <a href="mailto:help@thinkup.com" style="color: white; font-weight: bolder">help@thinkup.com</a> for help.</h4>
              </div>
              <div class="col-md-4">
                  &nbsp;
              </div>
          </div>
        </div>
      </div>

    {if isset($transaction_id) && isset($reference_id)}
      <div id="features">
        <div class="container">

            <h2><span>We Can Fix It!</span></h2>

              <p class="redeem-explain">When you email us at <a href="mailto:help@thinkup.com">help@thinkup.com</a>, include these details:</p>


              <div class="row">
                  <div class="col-md-2">
                      &nbsp;
                  </div>
                  <div class="col-md-8">
                    <table class="center-block">
                      <tr>
                        <th>Transaction</th>
                        <td>{$transaction_id}</td>
                      </tr>
                      <tr>
                        <th>Reference</th>
                        <td>{$reference_id}</td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-2">
                      &nbsp;
                  </div>
              </div>
        </div>
      </div>
    {/if}
  {/if}

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
        <p>Memberships to <strong>five</strong> of the <strong>best apps and communities</strong> on the web!</p>
        <div class="row">
            <div class="col-md-4">
                <h3 id="price"><sup>$</sup>96</h3>
                <h2 class="button">
                  {$pay_with_amazon_form}
                </h2>
                <p class="fineprint">Pay with Amazon</p>
            </div>
            <div class="col-md-4">
                <h4 id="fiftypercent">That's <strong style="color: white;">50% less</strong> than you'd pay
                for each site individually.</h4>
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
            <a href="http://metafilter.com"><img src="{$site_root_path}bundle/assets/img/logos/metafilter.svg" alt="MetaFilter" /></a>
            <h3><a href="http://metafilter.com">MetaFilter</a></h3>
            <p>This venerable community blog has an answer for everything.</p>
            <h3 class="strike">$60</h3>
          </div>
          <div class="col-md-2 col-centered">
            <a href="http://mlkshk.com"><img src="{$site_root_path}bundle/assets/img/logos/mlkshk.svg" alt="MLKSHK" /></a>
            <h3><a href="http://mlkshk.com">MLKSHK</a></h3>
            <p>The most fun and delightful image sharing community on the web.</p>
            <h3 class="strike">$24</h3>
         </div>
          <div class="col-md-2 col-centered">
            <a href="http://newsblur.com/"><img src="{$site_root_path}bundle/assets/img/logos/newsblur.png" alt="NewsBlur" width="85" height="85" /></a>
            <h3><a href="http://newsblur.com/">NewsBlur</a></h3>
            <p>A newsreader that makes it fun to keep up with your favorite sites.</p>
            <h3 class="strike">$24</h3>
          </div>
          <div class="col-md-2 col-centered">
            <a href="http://the-toast.net/"><img src="{$site_root_path}bundle/assets/img/logos/thetoast.png" alt="The Toast" width="135" height="85" /></a>
            <h3><a href="http://the-toast.net/">The Toast</a></h3>
            <p>A smart daily blog you will sincerely love and someday resent.</p>
            <h3 class="strike">$24</h3>
          </div>
          <div class="col-md-2 col-centered">
            <a href="https://thinkup.com/"><img src="{$site_root_path}bundle/assets/img/logos/thinkup.svg" alt="ThinkUp" /></a>
            <h3><a href="https://thinkup.com/">ThinkUp</a></h3>
            <p>Daily insights about you and your friends on Twitter and Facebook.</p>
            <h3 class="strike">$60</h3>
          </div>
        </div>

      </div>
    </div>

    <div id="features">
      <div class="container">
          <h2><span>Bundle Features</span></h2>
          <p class="center-block">The Good Web Bundle includes a full year's membership to five of the best apps and communities on the web for half the price.<br>These sites are a ton of fun&mdash;you're gonna be so glad you did this.</p>
        <div class="row row-centered">
          <div class="col-md-3 col-centered">
              <h4 id="daysleft" class="center-block">Only {$days_to_go} days left</h4>
              <p class="center-block">This is a one-time-only offer! Don't miss out.</p>
          </div>
          <div class="col-md-3 col-centered">
              <h4 id="bigsavings" class="center-block">50% savings</h4>
              <p class="center-block">Get a $192 value for about as much as you pay for Netflix.</p>
          </div>
          <div class="col-md-3 col-centered">
              <h4 id="goodcommunity" class="center-block">DO read the comments</h4>
              <p class="center-block">The best communities: On our sites, we don't tolerate abuse.</p>
          </div>

        </div>

        <div class="row">
          <div class="col-md-3">
            &nbsp;
          </div>
          <div class="col-md-3">
              <h4 id="notcreepy" class="center-block">We're not creepy</h4>
              <p class="center-block">We're building user-supported apps and communities that treat you with respect.</p>
          </div>
          <div class="col-md-3">
              <h4 id="honest" class="center-block">Join the movement</h4>
              <p class="center-block">Support these sites with your dollars and make the web a better place.</p>
          </div>
          <div class="col-md-3">
            &nbsp;
          </div>
        </div>
      </div>
    </div>

    <div id="who-we-are">
      <div class="container">
        <h2><span>Who We Are</span></h2>
        <p>The nicest proprietors on the web built and run the sites in the Good Web Bundle.<br>We're not billionaires. We're regular folks with families and bills who love the web as much as you do.</p>

        <div class="row">
          <div class="col-md-3">&nbsp;</div>
          <div class="col-md-2">
              <img src="{$site_root_path}bundle/assets/img/people/matt-haughey.jpg" alt="Matt Haughey" />
              <h4><a href="https://twitter.com/intent/user?screen_name=mathowie">Matt Haughey</a></h4>
              <p>MetaFilter</p>
          </div>
          <div class="col-md-2">
              <img src="{$site_root_path}bundle/assets/img/people/amber-costley.jpg" alt="Amber Costley" />
              <h4><a href="https://twitter.com/intent/user?screen_name=amberdawn">Amber Costley</a></h4>
              <p>MLKSHK</p>
          </div>
          <div class="col-md-2">
              <img src="{$site_root_path}bundle/assets/img/people/andre-torrez.jpg" alt="Andre Torrez" />
              <h4><a href="https://twitter.com/intent/user?screen_name=torrez">Andre Torrez</a></h4>
              <p>MLKSHK</p>
          </div>
          <div class="col-md-3">&nbsp;</div>
        </div>

        <div class="row">
          <div class="col-md-3">&nbsp;</div>
          <div class="col-md-2">
              <img src="{$site_root_path}bundle/assets/img/people/samuel-clay.jpg" alt="Samuel Clay" />
              <h4><a href="https://twitter.com/intent/user?screen_name=samuelclay">Samuel Clay</a></h4>
              <p>NewsBlur</p>
          </div>
          <div class="col-md-2">
              <img src="{$site_root_path}bundle/assets/img/people/nicole-cliffe.jpg" alt="Nicole Cliffe" />
              <h4><a href="https://twitter.com/intent/user?screen_name=nicole_cliffe">Nicole Cliffe</a></h4>
              <p>The Toast</p>
          </div>
          <div class="col-md-2">
              <img src="{$site_root_path}bundle/assets/img/people/mallory-ortberg.jpg" alt="Mallory Ortberg" />
              <h4><a href="https://twitter.com/intent/user?screen_name=mallelis">Mallory Ortberg</a></h4>
              <p>The Toast</p>
          </div>
          <div class="col-md-3">&nbsp;</div>
        </div>

        <div class="row">
          <div class="col-md-3">&nbsp;</div>
          <div class="col-md-2">
            <img src="{$site_root_path}bundle/assets/img/people/nick-pavich.jpg" alt="Nick Pavich" />
            <h4><a href="https://twitter.com/intent/user?screen_name=nick_pavich">Nick Pavich</a></h4>
            <p>The Toast</p>
          </div>
          <div class="col-md-2">
              <img src="{$site_root_path}bundle/assets/img/people/anil-dash.jpg" alt="Anil Dash" />
              <h4><a href="https://twitter.com/intent/user?screen_name=anildash">Anil Dash</a></h4>
              <p>ThinkUp</p>
          </div>
          <div class="col-md-2">
              <img src="{$site_root_path}bundle/assets/img/people/gina-trapani.jpg" alt="Gina Trapani" />
              <h4><a href="https://twitter.com/intent/user?screen_name=ginatrapani">Gina Trapani</a></h4>
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
              <p>Lots of great sites are free! But if you care about keeping quality, independent web sites around for years to come, a good way to support them is to become a paid member. Because in order to provide you #content, every site needs to find a way to make money, and this bundle is one fair, simple way to do it.</p>
            </div>
            <div class="question">
              <h4>Can I split up the perks between me and a friend?</h4>
              <p>Yep! When you buy the Good Web Bundle, you'll get a coupon code you can use to unlock your benefits on each of the sites. If you share that code with a friend as a gift, they can use it instead. (Each code can only be used once per site.)</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="question">
              <h4>Where does the money go when I buy a bundle?</h4>
              <p>We're splitting it between us, with each of the sites getting 50% of their usual subscription or membership fee in order to make this a good deal for you. (There's a little cut off the top for Amazon, which handles the payment processing.)</p>
            </div>
            <div class="question">
              <h4>How do I pay?</h4>
              <p>When you click the <strong>GET IT NOW</strong> button, you'll be sent to Amazon Payments to complete
              your purchase. This is as easy as any other purchase you might make on Amazon. We don't yet support PayPal or any other purchase methods, sorry.</p>
            </div>
          </div>
        </div>

        <h3>Let's break it down. You get:</h3>

        <div id="you-get">

          <table>
            <tr>
              <td><img src="{$site_root_path}bundle/assets/img/logos/metafilter.svg" alt="MetaFilter" /></td>
              <td>One year paid membership, with lifetime right to post and ask questions and no ads.</td>
            </tr>
            <tr>
              <td><img src="{$site_root_path}bundle/assets/img/logos/mlkshk.svg" alt="MLKSHK" /></td>
              <td>One year paid membership, with the ability to create multiple image shakes and browse with no ads.</td>
            </tr>
            <tr>
              <td><img src="{$site_root_path}bundle/assets/img/logos/newsblur.png" alt="NewsBlur" /></td>
              <td>One year premium account, with unlimited sites, faster updates and search.</td>
            </tr>
            <tr>
              <td><img src="{$site_root_path}bundle/assets/img/logos/thetoast.png" alt="The Toast" /></td>
              <td>Recognition as a sponsoring member and access to chat when it launches.</td>
            </tr>
            <tr>
              <td><img src="{$site_root_path}bundle/assets/img/logos/thinkup.svg" alt="ThinkUp" /></td>
              <td>One year paid membership, with daily insights about you and your friends on Twitter and Facebook.</td>
            </tr>
          </table>

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
          <h4><sup style="font-size: 60%;">$</sup>96</h4>
        </div>

        <div class="col-md-4">
          <h3>50% less than you'd pay to sign up individually.<br />
            For a limited time.</h3>
            {$pay_with_amazon_form}
        </div>
      </div>
    </div>

{/if}

  <div id="share">
    <div class="container">

      <h2>Share</h2>

      <div class="col-md-6">
        <h3>Keep up with us:</h3>
          <ul>
            <li id="follow-twitter">
        <span class="followus"><img src="{$site_root_path}bundle/assets/img/twitter.png" alt="Twitter" /><a href="http://twitter.com/goodwebbundle">@goodwebbundle</a></span>

              <a href="https://twitter.com/goodwebbundle" class="twitter-follow-button" data-show-count="false" data-size="large">Follow @goodwebbundle</a>
              {literal}
              <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
              {/literal}
            </li>
          </ul>
      </div>

      <div class="col-md-6">
          <h3>Tell your friends!</h3>
          <a href="https://twitter.com/share" class="twitter-share-button" data-via="goodwebbundle">Tweet</a>
          {literal}
          <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
          {/literal}

          <div class="fb-share-button" data-href="http://goodwebbundle.com" data-layout="button_count"></div>

          <h3>Need help? Got a question? Get in touch!</h3>
          <p><a class="email" href="mailto:help@thinkup.com">help@thinkup.com</a></p>

      </div>

    </div>
  </div>


  <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
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
