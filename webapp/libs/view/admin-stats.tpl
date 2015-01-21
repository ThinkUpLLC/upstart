{include file="_adminheader.tpl" page="Stats"}

    <div class="container">

      <h3>Monthly subscription payments by day</h3>
        <img src="{$daily_payments_chart_url}" class="img-responsive" />

      <h3>Trial signups by day</h3>
        <img src="{$daily_signups_chart_url}" class="img-responsive" />

      <h3>Monthly subscription conversions by week</h3>
        <p>{$weekly_conversions_message}<br>
        {$weekly_refunds_message}</p>
        <img src="{$weekly_conversions_chart_url}" class="img-responsive" />

      <h3>Monthly conversions</h3>
        <p>{$monthly_conversions_message}</p>
        <img src="{$monthly_conversions_chart_url}" class="img-responsive" />

    </div> <!-- /container -->
    <br><br><br>
  </body>
</html>
