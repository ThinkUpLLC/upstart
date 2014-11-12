{include file="_adminheader.tpl" page="Claim Codes" hide_admin_nav_links="true"}

    <div class="container">

<div class="row-fluid">
  <div class="span1"></div>
  <div class="span10">

    <table class="table table-hover" style="background-color:white">
      <tr>
          <th>Code</th>
          <th>Email</th>
          <th>Type</th>
          <th>Good for</th>
          <th>Purchased</th>
      </tr>
      {foreach $claim_codes as $claim_code}
      <tr>
        <td><a href="https://www.thinkup.com/join/api/bundle/?code={$claim_code->code}">{$claim_code->readable_code}</a></td>
        <td>{$claim_code->email}</td>
        <td>{$claim_code->type}</td>
        <td>{$claim_code->number_days} days</th>
        <td>{$claim_code->timestamp}</th>
      </tr>
      {/foreach}
    </table>

<div class="span1"></div>

<ul class="pager">
  {if $prev_page}<li class="previous"><a href="?p={$prev_page}{if $search_term}&q={$search_term|urlencode}{/if}">&larr; Previous</a></li>{/if}
  {if $next_page}<li class="next"><a href="?p={$next_page}{if $search_term}&q={$search_term|urlencode}{/if}">Next &rarr;</a></li>{/if}
</ul>

</div>
</div>
    </div> <!-- /container -->

  </body>
</html>
