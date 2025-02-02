<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{if isset($controller_title)}{$controller_title} | {/if}ThinkUp</title>
    <link rel="shortcut icon" type="image/x-icon" href="{$site_root_path}assets/img/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{$site_root_path}assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{$site_root_path}assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{$site_root_path}assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="{$site_root_path}assets/ico/apple-touch-icon-57-precomposed.png">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- TU JS namespace -->
    <script>window.tu = {};</script>

    <!-- styles -->
    {literal}<script type="text/javascript" src="//use.typekit.net/xzh8ady.js"></script>
    <script type="text/javascript">try{Typekit.load();}catch(e){}</script>{/literal}
    <link href="{$site_root_path}assets/css/vendor/font-awesome.min.css" rel="stylesheet">
    <link href='//fonts.googleapis.com/css?family=Libre+Baskerville:400,700,400italic|' rel='stylesheet' type='text/css'>
{if isset($marketing_page) and $marketing_page}
    <link href="{$site_root_path}assets/css/marketing.v2.css" rel="stylesheet">
{else}
    <link href="{$site_root_path}assets/css/vendor/bootstrap.min.css" rel="stylesheet">
    <link href="{$site_root_path}assets/css/thinkup.css" rel="stylesheet">
{/if}
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
{if !isset($marketing_page) or !$marketing_page}
    {if isset($include_menu) and $include_menu}
    <div id="menu">
      <ul class="list-unstyled menu-options">{if isset($subscriber)}
        <li><a href="{if isset($thinkup_url)}{$thinkup_url}{else}{$site_root_path}{/if}">Home</a></li>
        {if isset($thinkup_url)}
        <li class="service {$facebook_connection_status}"><a href="{$thinkup_url}account/?p=facebook" class="{if isset($smarty.get.p) && $smarty.get.p eq 'facebook'} active{/if}">Facebook<i class="fa fa-{if $facebook_connection_status eq 'active'}check-circle{elseif $facebook_connection_status eq 'error'}exclamation-triangle{else}facebook-square{/if} icon"></i></a></li>
        <li class="service {$twitter_connection_status}"><a href="{$thinkup_url}account/?p=twitter" class="service error{if isset($smarty.get.p) && $smarty.get.p eq 'twitter'} active{/if}">Twitter<i class="fa fa-{if $twitter_connection_status eq 'active'}check-circle{elseif $twitter_connection_status eq 'error'}exclamation-triangle{else}twitter{/if} icon"></i></a></li>
        <li class="service {$instagram_connection_status}"><a href="{$thinkup_url}account/?p=instagram" class="service error{if isset($smarty.get.p) && $smarty.get.p eq 'instagram'} active{/if}">Instagram<i class="fa fa-{if $instagram_connection_status eq 'active'}check-circle{elseif $instagram_connection_status eq 'error'}exclamation-triangle{else}instagram{/if} icon"></i></a></li>
        {/if}
        <li><a href="{$site_root_path}user/settings.php"{if $controller_title eq "Settings"} class="active"{/if}>Settings</a></li>
        <li><a href="{$site_root_path}user/membership.php"{if $controller_title eq "Membership Info"} class="active"{/if}>Membership</a></li>
        <li class="user-info logged-in">
          <img src="http://www.gravatar.com/avatar/{$subscriber->email|strtolower|md5}" class="user-photo img-circle">
          <div class="current-user">
            <div class="label">Logged in as</div>
            {$subscriber->email}
          </div>
        </li>
        <li><a href="{$site_root_path}user/logout.php">Log out</a></li>
    {else}
        <li><a href="{$site_root_path}session/login.php">Log in</a></li>
    {/if}</ul>
    </div>
    {/if}
    <div id="page-content">
{include file="_appusermessage.tpl"}

<nav class="navbar navbar-default" role="navigation">
  <div class="navbar-header">
    <button class="btn menu-trigger">
      <i class="fa fa-bars"></i>
    </button>
    <a class="navbar-brand" href="{$site_root_path}"><strong>Think</strong>Up</span></a>
  </div>
</nav>
{/if}
