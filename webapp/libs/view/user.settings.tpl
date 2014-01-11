{include file="_userheader.tpl"}
{include file="_usernavigation.tpl"}

{include file="_appusermessage.tpl"}


{include file="_appusermessage.tpl"}

      
      <div class="container">
        <header>
          <h1>Settings</h1>
        </header>

{if isset($thinkup_username)}
ThinkUp username: {$thinkup_username} (<a href="{$thinkup_url}">go to ThinkUp</a>)
{else}
Choose your ThinkUp username: <input type="text">
{/if}

        <form role="form" class="form-horizontal" id="form-settings">
          <fieldset class="fieldset-personal">
            <header>
              <h2>Personal</h2>
            </header>
            <div class="form-group">
              <label class="control-label" for="control-email">Email</label>
              <input type="email" class="form-control" id="control-email">
            </div>
            <div class="form-group">
              <label class="control-label" for="control-first-name">First Name</label>
              <input type="text" class="form-control" id="control-first-name">
            </div>
            <div class="form-group">
              <label class="control-label" for="control-last-name">Last Name</label>
              <input type="text" class="form-control" id="control-last-name">
            </div>
            <div class="form-group">
              <label class="control-label" for="control-time-zone">Time Zone</label>
              <div class="form-control picker">
              <i class="fa fa-chevron-down icon"></i>
              <select id="control-time-zone">
                <option value="-12.0">GMT -12:00 Eniwetok, Kwajalein</option>
                <option value="-11.0">GMT -11:00 Midway Island, Samoa</option>
                <option value="-10.0">GMT -10:00 Hawaii</option>
                <option value="-9.0">GMT -9:00 Alaska</option>
                <option value="-8.0">GMT -8:00 Pacific Time</option>
                <option value="-7.0">GMT -7:00 Mountain Time</option>
                <option value="-6.0">GMT -6:00 Central Time</option>
                <option value="-5.0">GMT -5:00 Eastern Time</option>
                <option value="-4.0" selected>GMT -4:00 Atlantic Time</option>
                <option value="-3.5">GMT -3:30 Newfoundland</option>
                <option value="-3.0">GMT -3:00 Brazil, Buenos Aires</option>
                <option value="-2.0">GMT -2:00 Mid-Atlantic</option>
                <option value="-1.0">GMT -1:00 Azores, Cape Verde Islands</option>
                <option value="0.0">GMT Western Europe Time, London</option>
                <option value="1.0">GMT +1:00 Brussels, Copenhagen, Madrid, Paris</option>
                <option value="2.0">GMT +2:00 Kaliningrad, South Africa</option>
                <option value="3.0">GMT +3:00 Baghdad, Riyadh, Moscow, St. Petersburg</option>
                <option value="3.5">GMT +3:30 Tehran</option>
                <option value="4.0">GMT +4:00 Abu Dhabi, Muscat, Baku, Tbilisi</option>
                <option value="4.5">GMT +4:30 Kabul</option>
                <option value="5.0">GMT +5:00 Ekaterinburg, Islamabad, Karachi, Tashkent</option>
                <option value="5.5">GMT +5:30 Bombay, Calcutta, Madras, New Delhi</option>
                <option value="5.75">GMT +5:45 Kathmandu</option>
                <option value="6.0">GMT +6:00 Almaty, Dhaka, Colombo</option>
                <option value="7.0">GMT +7:00 Bangkok, Hanoi, Jakarta</option>
                <option value="8.0">GMT +8:00 Beijing, Perth, Singapore, Hong Kong</option>
                <option value="9.0">GMT +9:00 Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
                <option value="9.5">GMT +9:30 Adelaide, Darwin</option>
                <option value="10.0">GMT +10:00 Eastern Australia, Guam, Vladivostok</option>
                <option value="11.0">GMT +11:00 Magadan, Solomon Islands, New Caledonia</option>
                <option value="12.0">GMT +12:00 Auckland, Wellington, Fiji, Kamchatka</option>
              </select>
            </div>
          </fieldset>

          <fieldset class="fieldset-password">
            <header>
              <h2>Change Password</h2>
            </header>
            <div class="form-group">
              <label class="control-label" for="control-password-current">Current</label>
              <input type="password" class="form-control" id="control-password-current">
            </div>
            <div class="form-group">
              <label class="control-label" for="control-password-new">New</label>
              <input type="password" class="form-control" id="control-password-new">
            </div>
            <div class="form-group">
              <label class="control-label" for="control-password-verify">Veryify New</label>
              <input type="password" class="form-control" id="control-password-verify">
            </div>
          </fieldset>

          <fieldset class="fieldset-privacy">
            <header>
              <h2>Privacy</h2>
              <div class="help-text">Who can view your stream and shared insights?</div>
            </header>
            <div class="form-group form-group-toggle">
              <input type="checkbox" id="control-privacy" checked>
              <label class="control-label" for="control-privacy">Anyone with a link can view</label>
            </div>
          </fieldset>

          <input type="submit" value="Done" class="btn btn-circle btn-submit">
        </form>
      </div>
    </div>


{include file="_userfooter.tpl"}
