{assign var="tagline" value="Daily insights about you and your friends"}
{assign var="tagline_logo" value="Daily insights about you"}
{assign var="description" value="ThinkUp uncovers fun and fascinating insights about you and your friends on Twitter and Facebook."}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{if isset($controller_title)}{$controller_title} | {/if}ThinkUp - {$tagline}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{$site_root_path}assets/img/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{$site_root_path}assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{$site_root_path}assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{$site_root_path}assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="{$site_root_path}assets/ico/apple-touch-icon-57-precomposed.png">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="ThinkUp">

    <meta property="og:site_name" content="ThinkUp" />
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@thinkup">
    <meta name="twitter:domain" content="thinkup.com">

    <meta property="og:url" content="https://www.thinkup.com/" />

    <meta itemprop="name" content="ThinkUp">
    <meta name="twitter:title" content="ThinkUp: {$tagline}">
    <meta property="og:title" content="ThinkUp: {$tagline}" />

    <meta itemprop="description" content="{$description}">
    <meta name="description" content="{$description}">
    <meta name="twitter:description" content="{$description}">

    <meta itemprop="image" content="https://www.thinkup.com/join/assets/img/landing/crowd.png">
    <meta property="og:image" content="https://www.thinkup.com/join/assets/img/landing/crowd.png" />
    <meta property="og:image:secure" content="https://www.thinkup.com/join/assets/img/landing/crowd.png" />
    <meta name="twitter:image:src" content="https://www.thinkup.com/join/assets/img/landing/crowd.png">

    <meta name="og:image:type" content="image/png">
    <meta name="twitter:image:width" content="160">
    <meta name="twitter:image:height" content="154">
    <meta name="og:image:width" content="160">
    <meta name="og:image:height" content="154">
    <meta name="twitter:creator" content="@thinkup">

    <!-- TU JS namespace -->
    <script>window.tu = {};</script>

    <!-- styles -->
    {literal}<script type="text/javascript" src="//use.typekit.net/xzh8ady.js"></script>
    <script type="text/javascript">try{Typekit.load();}catch(e){}</script>{/literal}
    <link href="{$site_root_path}assets/css/vendor/font-awesome.min.css" rel="stylesheet">
    <link href='//fonts.googleapis.com/css?family=Libre+Baskerville:400,700,400italic|' rel='stylesheet' type='text/css'>
    <link href="{$site_root_path}assets/css/marketing.v2.css" rel="stylesheet">

{include file="_header.common.tpl"}

{if isset($header_css)}
{foreach from=$header_css item=css}
    <link type="text/css" rel="stylesheet" href="{$site_root_path}{$css}" />
{/foreach}
{/if}
{if isset($header_scripts)}
{foreach from=$header_scripts item=script}
    <script type="text/javascript" src="{$site_root_path}{$script}"></script>
{/foreach}
{/if}

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
<body{if isset($body_classes)} class="{$body_classes}"{/if}{if isset($body_id)} id="{$body_id}"{/if}>

<div class="container" id="container-navbar">
    <header class="section navbar navbar-marketing" id="section-navbar">
        <div class="navbar-brand">
            <h1 class="logo"><a href="{$site_root_path}">ThinkUp</a></h1>
            <h2 class="tagline">{$tagline_logo}</h2>
            <div class="beta-tag">Beta</div>
        </div>


        <ul class="nav navbar-nav">
            {if isset($thinkup_url)}<li class="nav-link"><a href="{$thinkup_url}">Your Insights</a></li>
            {else}<li class="nav-link"><a href="{$site_root_path}user/">Login</a></li>{/if}
            <li class="nav-link"><a href="{$site_root_path}pricing.php">Pricing</a></li>
            <li class="nav-link"><a href="{$site_root_path}media.php">For Media</a></li>
            <li class="nav-link"><a href="{$site_root_path}about/">About</a></li>
            <li class="nav-button{if (isset($hide_join) and $hide_join)} is-hidden{/if}"><a class="btn btn-pill btn-signup" href="{$site_root_path}">Join now</a></li>
        </ul>
    </header>
</div>

{include file="_appusermessage.tpl"}