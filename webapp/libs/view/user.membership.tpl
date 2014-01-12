{assign var=thinkup_username value="capndesign"}
{include file="_appheader.v2.tpl" include_menu=true
body_classes="settings menu-open" body_id="settings-subscription"}

  <nav class="navbar navbar-default" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button class="btn menu-trigger">
        <i class="fa fa-bars"></i>
      </button>
      <a class="navbar-brand" href="#"><strong>Think</strong>Up</span></a>
    </div>
  </nav>    

  <div class="container">
      <header>
        <h1>Subscription</h1>
        <h2>This is what our database knows.</h2>
      </header>

      <ul class="list-group">
        <li class="list-group-item">
          <div class="list-group-item-label">Username:</div>
          <div class="list-group-item-value"><a href="//{$thinkup_username}.thinkup.com">{$thinkup_username}</a></div>
        </li>
        <li class="list-group-item">
          <div class="list-group-item-label">Level:</div>
          <div class="list-group-item-value">{$subscriber->membership_level}</div>
        </li>
        <li class="list-group-item">
          <div class="list-group-item-label">Paid through:</div>
          <div class="list-group-item-value">{$subscription_end_date|date_format:"F jS, Y"}</div>
        </li>
      </ul>

      <p class="form-note">Issues with your membership?<br>
      <a href="#help" class="show-section btn btn-default" data-section-selector="#form-membership-contact">Contact us</a></p>

      <form role="form" class="form-horizontal" id="form-membership-contact">
        <fieldset>
          <header>
            <h2>What’s your trouble?</h2>
          </header>
          <div class="form-group">
            <label class="control-label" for="control-type">Type of issue</label>
            <div class="form-control picker">
              <i class="fa fa-chevron-down icon"></i>
              <select id="control-type">
                <option value="">Choose&hellip;</option>
                <option value="level">Change membership level</option>
                <option value="billing">Billing issue</option>
                <option value="cancel">Cancel my membership</option>
                <option value="other">Something else</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label" for="control-body">More info</label>
            <textarea class="form-control" id="control-body"></textarea>
          </div>
        </fieldset>

        <input type="submit" value="Send it" class="btn btn-circle btn-submit">
      </form>

{include file="_appfooter.v2.tpl"}