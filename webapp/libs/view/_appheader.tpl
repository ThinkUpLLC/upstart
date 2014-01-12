  <meta name="viewport" content="width=device-width, user-scalable=no">
  <link rel="stylesheet" href="{$site_root_path}assets/css/vendor/normalize.min.css">
  <link href="//fonts.googleapis.com/css?family=Libre+Baskerville:400,700,400italic" rel='stylesheet' type='text/css'>
  <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
  <link rel="stylesheet" href="{$site_root_path}assets/css/main.css">

<!--[if lt IE 9]>
  <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <script>window.html5 || document.write('<script src="js/vendor/html5shiv.js"><\/script>')</script>
<![endif]-->

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
