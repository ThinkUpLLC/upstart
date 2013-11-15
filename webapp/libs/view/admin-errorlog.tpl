{include file="_adminheader.tpl" page="Error Log"}

    <div class="container">

<div class="row-fluid">
  <div class="span1"></div>
  <div class="span10">
    <h2>Error Log</h2>
    <table class="table table-condensed table-hover">
      <tr>
          <th>Location</th>
          <th>Debug</th>
      </tr>
      {foreach $errors as $error}
      <tr>
        <td><a href="{$error.github_link}">{$error.method}#L{$error.line_number}</a><br />{$error.timestamp|relative_datetime} ago</td>
        <td><pre>{$error.debug}</pre></td>
      </tr>
      {/foreach}
    </table>

<div class="span1"></div>

{if $prev_page}<a href="?p={$prev_page}">&larr; previous</a>{/if} {if $next_page and $prev_page}|{/if} {if $next_page}<a href="?p={$next_page}">next &rarr;</a>{/if}
</div>
</div>
    </div> <!-- /container -->


  </body>
</html>
