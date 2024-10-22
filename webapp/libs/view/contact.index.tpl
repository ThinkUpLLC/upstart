{include file="_header.marketing.tpl" marketing_page=true
body_classes="marketing marketing-page" body_id="marketing-contact"}

{if isset($smarty.get.type)}
  {assign var="contact_type" value=$smarty.get.type}
{else}
  {assign var="contact_type" value=null}
{/if}

  <div class="container">
    <section class="section section-marketing-text">
      <h2 class="section-header">Contact</h2>
      {include file="_about.nav.tpl" active="contact"}

      <form role="form" class="form" id="form-contact">
        <header>
          <h3 class="text-header">We’d love to hear from you.</h3>
        </header>
        <div class="form-group">
          <label class="control-label" for="control-subject">Subject</label>
          <div class="form-control picker">
            <i class="fa fa-chevron-down icon"></i>
            <select id="control-subject">
              <option value="">Choose&hellip;</option>
              <option value="level" {if $contact_type eq 'level'}selected="true"{/if}>Change membership level</option>
              <option value="billing" {if $contact_type eq 'billing'}selected="true"{/if}>Billing issue</option>
              <option value="executive" {if $contact_type eq 'executive'}selected="true"{/if}>Executive Memberships</option>
              <option value="press" {if $contact_type eq 'press'}selected="true"{/if}>Press Inquiry</option>
              <option value="other" {if $contact_type eq 'other'}selected="true"{/if}>Something else</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label" for="control-body">More info</label>
          <textarea class="form-control" id="control-body"></textarea>
        </div>

        <button type="submit" class="btn btn-pill btn-submit">Send it</button>
      </form>
    </section>

{include file="_footer.marketing.tpl"}

</body>

</html>
