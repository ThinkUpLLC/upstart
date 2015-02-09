<!DOCTYPE html>
<html>
<head>
    <title></title>
    <script src="../assets/js/vendor/pay-with-amazon.min.js"></script>
    <script type="text/javascript">
    var payWithAmazon = new PayWithAmazon({
        sellerId: 'A3MIF7Z0W3XC2S', //https://sellercentral.amazon.com/gp/pyop/seller/account/settings/user-settings-view.html/ref=ps_pyopiset_dnav_onconfig_
        clientId: 'amzn1.application-oa2-client.f402aa2a27df4dd0bfce7e297b971176', // https://sellercentral.amazon.com/gp/homepage.html
        button: { id: 'pay-with-amazon', type: 'large', color: 'DarkGray' },
        wallet: { id: 'wallet', width: 400, height: 260 },
        consent: { id: 'consent', width: 400, height: 140 }}
    );
</script>

</head>
<body>
    <div id="pay-with-amazon"></div>
    <div id="wallet"></div>
    <div id="consent"></div>
</body>
</html>
