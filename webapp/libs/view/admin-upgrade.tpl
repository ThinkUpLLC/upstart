{include file="_adminheader.tpl" page="Upgrade"}
    <div class="container">

<div class="row-fluid">
  <div class="span4"></div>
  <div class="span4">
    <div id="logo"><h1>Think<span>Up</span>grader</h1> </div>

{if $smarty.get.upgrade eq 'true'}
<p><span style="color:green;font-weight:bold">{$successful_upgrades|number_format} successful</span> {if $failed_upgrades > 0}and <span style="color:red">{$failed_upgrades} failed</span> {/if}upgrades complete.</p>
<p>Don't forget to restart Dispatch's workers!</p>
{else}
<p>Steps to run upgrader:</p>
<ol>
<li{if $total_installs_to_upgrade eq 0} class="alert alert-danger"{/if}><span style="font-weight:bold">{$total_installs_to_upgrade|number_format} installations</span> need an upgrade.</li>
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
