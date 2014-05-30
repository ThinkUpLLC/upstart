{include file="_appheader.tpl" marketing_page=true
body_classes="marketing marketing-page" body_id="marketing-contact"}

  <div class="container">
    {include file="_marketing-navigation.tpl"}

    <section class="section col-md-2 col-lg-3 row-1 col-lg-right" id="section-page-header">
      <h2 class="section-header">Contact</h2>
      {include file="_about.nav.tpl" active="contact"}
    </section>

    <section class="section col-md-2 col-lg-3 col-lg-right section-marketing-text">
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
              <option value="level">Change membership level</option>
              <option value="billing">Billing issue</option>
              <option value="executive">Executive Memberships</option>
              <option value="press">Press Inquiry</option>
              <option value="other">Something else</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label" for="control-body">More info</label>
          <textarea class="form-control" id="control-body"></textarea>
        </div>

        <button type="submit" class="btn btn-circle btn-submit">Send it</button>
      </form>
    </section>

{include file="_footer.marketing.tpl"}