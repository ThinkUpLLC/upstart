{include file="_adminheader.tpl" page="Stats"}

    <div class="container">

      <h2>Daily payments</h2>
        <img src="{$daily_payments_chart_url}" class="img-responsive" />

      <h2>Daily signups</h2>
        <img src="{$daily_signups_chart_url}" class="img-responsive" />

      <h2>Weekly conversions</h2>
        <p>{$weekly_conversions_message}</p>
        <img src="{$weekly_conversions_chart_url}" class="img-responsive" />

      <h2>Monthly conversions</h2>
        <p>{$monthly_conversions_message}</p>
        <img src="{$monthly_conversions_chart_url}" class="img-responsive" />

    </div> <!-- /container -->
    <br><br><br>
  </body>
</html>
