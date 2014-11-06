<div class="container">
  <section class="section section-marketing-text section-press-materials">
    <h2 class="section-header">{$title}</h2>
    <br><br><br>
    {if isset($pay_with_amazon_form)}{$pay_with_amazon_form}{/if}


    {if isset($success_msg)}[SUCCESS MSG] {$success_msg} {/if}
    {if isset($error_msg)}[ERROR MSG] {$error_msg} {/if}

    {if isset($claim_code)}
    <p>Here's your claim code:</p>
    <h3>{$claim_code_readable}</h3>
    <p>Transaction: {$transaction_id}<br>
    Reference: {$reference_id}</p>
    {/if}
  </section>
</div>