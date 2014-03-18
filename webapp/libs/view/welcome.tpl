{include file="_appheader.v2.tpl" include_menu=true body_id="welcome"}
{include file="_appusermessage.tpl"}
  <div class="container">
    <div class="stream">
      <div class="date-group today">
          <div class="date-marker">
              <div class="absolute">Today</div>
          </div>

          <div class="panel panel-default insight insight-default insight-purple insight-hero">
            <div class="panel-heading">
              <h2 class="panel-title insight-color-text">Thanks for joining ThinkUp!</h2>
            </div>
            <div class="panel-desktop-right">
              <div class="panel-body">
                <div class="panel-body-inner">
                  <p>Your account is all set up: <a href="{$new_subscriber->installation_url}">{$new_subscriber->installation_url}</a></p>

                  <p>Keep your eyes peeled for an email from us with your first set of ThinkUp insights.</p>
                </div>
              </div>
            </div>
          </div>

          {if $new_subscriber->membership_level eq "Pro"}
          <div class="panel panel-default insight insight-default insight-mint">
            <div class="panel-heading">
              <h2 class="panel-title insight-color-text">Got more social networking accounts?</h2>
            </div>
            <div class="panel-desktop-right">
              <div class="panel-body">
                <div class="panel-body-inner">
                  <p>As a Pro member, you can add up to ten accounts. Connect another <a href="{$new_subscriber->installation_url}account/?p=facebook">Facebook</a> or <a href="{$new_subscriber->installation_url}account/?p=twitter">Twitter</a> account with a click.</p>
                </div>
              </div>
            </div>
          </div>
          {else}
          <div class="panel panel-default insight insight-default insight-mint">
            <div class="panel-heading">
              <h2 class="panel-title insight-color-text">Got a {if $new_subscriber->network eq 'facebook'}Twitter{else}Facebook{/if} account?</h2>
            </div>
            <div class="panel-desktop-right">
              <div class="panel-body">
                <div class="panel-body-inner">
                  <p>You can <a href="{$new_subscriber->installation_url}account/?p={if $new_subscriber->network eq 'facebook'}twitter{else}facebook{/if}">connect your account</a> to ThinkUp and get insights from both Facebook and Twitter in one place.</p>
                </div>
              </div>
            </div>
          </div>
          {/if}

          <div class="panel panel-default insight insight-default insight-bubblegum">
            <div class="panel-heading">
              <h2 class="panel-title insight-color-text">Need any help? Let us know.</h2>
            </div>
            <div class="panel-desktop-right">
              <div class="panel-body">
                <div class="panel-body-inner">
                  <p>Email us any time at <a href="mailto:help@thinkup.com">help@thinkup.com</a> if you get stuck, and we’ll help you out.</p>

                  <p>You might want to follow us at <a href="https://twitter.com/intent/user?screen_name=thinkup">@thinkup</a> on Twitter, too.</p>
                </div>
              </div>
            </div>
          </div>

      </div>
    </div>

{include file="_appfooter.v2.tpl"}