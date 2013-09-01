
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
  <div class="span4"></div>
  <div class="span4">
    <div id="logo"><h1>Think<span>Up</span>grader</h1> </div>

{if $smarty.get.upgrade eq 'true'}
<p><span style="color:green;font-weight:bold">{$successful_upgrades} successful</span> {if $failed_upgrades > 0}and <span style="color:red">{$failed_upgrades} failed</span> {/if}upgrades complete.</p>
<p>Don't forget to restart Dispatch's workers!</p>
{else}
<p>Steps to run upgrader:</p>
<ol>
<li{if $total_installs_to_upgrade eq 0} class="alert alert-danger"{/if}><span style="font-weight:bold">{$total_installs_to_upgrade} installations</span> need an upgrade.</li>
<li {if $workers_ok} class="alert alert-danger"{/if}>Shut down Dispatch workers. Current worker status:<br /> <b>{$worker_status}</b></li>
<li>Update master and chameleon installations via git pull. 
<br/>
{if $chameleon_commit_hash}
<p>Master: <a href="https://github.com/ginatrapani/ThinkUp/commit/{$commit_hash}">{$commit_hash}</a></p>
<p>Chameleon: <a href="https://github.com/ginatrapani/ThinkUp/commit/{$chameleon_commit_hash}">{$chameleon_commit_hash}</a></p>
{if $commit_hash neq $chameleon_commit_hash}
<p class="alert alert-danger">Chameleon and Master installations are out of sync.</p>
{/if}
{/if}
</li>

{if $show_go_button neq false}
<a href="?upgrade=true" class="btn btn-success">Run the upgrade</a>
{else}
<a href="javascript:alert('Not ready to run upgrade. Be sure to complete all steps.');" class="btn btn-danger">Hold up</a>
{/if}

{/if}
<div class="span4"></div>

</div>
</div>
    </div> <!-- /container -->


  </body>
</html>
