{include file="_appheader.tpl" marketing_page=true
body_classes="marketing marketing-page" body_id="marketing-email-confirmed"}

  <div class="container">
    {include file="_marketing-navigation.tpl"}

    <section class="section col-md-2 col-lg-3 row-1 col-lg-right" id="section-page-header">
      <h2 class="section-header">{if $error_msg}Oops!{/if}{if $success_msg}Confirmed!{/if}</h2>
    </section>

    <section class="section col-md-2 col-lg-3 col-lg-right section-legal-text" id="section-legal-text">
      {if $error_msg}<div class="message warning"><i class="icon icon-warning-sign"></i>{$error_msg}</div>{/if}
      {if $success_msg}
      <div class="message success"><i class="icon icon-ok-sign"></i> {$success_msg}</div>
        <p>You're going to get in first, on January 15th. And we'll update you along the way with some cool surprises.</p>
      {/if}

    </section>


{include file="_footer.marketing.tpl"}