<?php
/**
 * This document is a rudimentary testbed for the avatar change API endpoint.
 * If avatar changes are showing up that shouldn't be, add them to the list below to see what the API returns and
 * make changes to it locally to fix the problem.
 */
?>
<!DOCTYPE html>
<html>
<head>
  <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.6.2.min.js"></script>
</head>
<body>
<?php
$test_images = array(
    array(
        'image1'=>'https://igcdn-photos-g-a.akamaihd.net/hphotos-ak-xft1/t51.2885-19/10576078_831540340191782_1437048786_a.jpg',
        'image2'=>'https://igcdn-photos-e-a.akamaihd.net/hphotos-ak-xaf1/t51.2885-19/11377431_1411883439139548_1204304515_a.jpg',
        'result'=>'false' //one is blank
    ),
    array(
        'image1'=>'https://igcdn-photos-b-a.akamaihd.net/hphotos-ak-xpa1/t51.2885-19/1596979_781965558558193_1373667247_a.jpg',
        'image2'=>'https://igcdn-photos-b-a.akamaihd.net/hphotos-ak-xfp1/t51.2885-19/1596979_781965558558193_1373667247_a.jpg',
        'result'=>'false' //same photos
    ),
    array(
        'image1'=>'https://igcdn-photos-g-a.akamaihd.net/hphotos-ak-xfp1/t51.2885-19/1689630_778173505538238_1101876775_a.jpg',
        'image2'=>'https://igcdn-photos-d-a.akamaihd.net/hphotos-ak-xfa1/t51.2885-19/11377775_849794601773979_1675234520_a.jpg',
        'result'=>'true' //different!
    ),
    array(
        'image1'=>'https://instagramimages-a.akamaihd.net/profiles/anonymousUser.jpg',
        'image2'=>'https://igcdn-photos-a-a.akamaihd.net/hphotos-ak-xfa1/t51.2885-19/11380022_946985428676272_1086845313_a.jpg',
        'result'=>'true' //different!
    ),
    array(
        'image1'=>'https://instagramimages-a.akamaihd.net/profiles/profile_14083114_75sq_1322415694.jpg',
        'image2'=>'https://igcdn-photos-h-a.akamaihd.net/hphotos-ak-xaf1/t51.2885-19/11335521_1604667283150199_486934309_a.jpg',
        'result'=>'false' //one is blank
    ),
    array(
        'image1'=>'https://igcdn-photos-b-a.akamaihd.net/hphotos-ak-xap1/t51.2885-19/1538409_330831203772817_962804141_a.jpg',
        'image2'=>'https://igcdn-photos-b-a.akamaihd.net/hphotos-ak-xfp1/t51.2885-19/1538409_330831203772817_962804141_a.jpg',
        'result'=>'true' //same photos
    ),
    array(
        'image1'=>'https://igcdn-photos-b-a.akamaihd.net/hphotos-ak-xfa1/t51.2885-19/10895423_729248160524889_1870729078_a.jpg',
        'image2'=>'https://igcdn-photos-b-a.akamaihd.net/hphotos-ak-xpf1/t51.2885-19/10895423_729248160524889_1870729078_a.jpg',
        'result'=>'true' //same photos
    )
/* //These images have changed and disappeared since we used them to test
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/584359090698199040/y3a_k3-2.jpg',
        'image2'=>'https://pbs.twimg.com/profile_images/584934935686991872/xoSr8izN.jpg',
        'result'=>'false' //same-same
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/580497125865365504/raZ3Ahzs.jpg',
        'image2'=>'https://pbs.twimg.com/profile_images/583766272116011008/2HyIWIQx.jpg',
        'result'=>'true' //different photos
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/421040013460000769/yB3JojGw.jpeg',
        'image2'=>'https://pbs.twimg.com/profile_images/584799120323387392/WSc9qdke.jpg',
        'result'=>'false' //one is blank
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/582973538807631872/FUe-YfYM.png',
        'image2'=>'https://pbs.twimg.com/profile_images/584071968162357248/J-GBgXYa.png',
        'result'=>'true'
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/583178217097310208/RF9TzXaH.jpg',
        'image2'=>'https://pbs.twimg.com/profile_images/584594436669079553/eQGQiHkq.jpg',
        'result'=>'true'
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/580610210634588162/COD46OZ5.jpg',
        'image2'=>'https://pbs.twimg.com/profile_images/584104465340506113/lftNLcqm.jpg',
        'result'=>'false'
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/579904742442340352/qWje9OOG.jpg',
        'image2'=>'https://pbs.twimg.com/profile_images/581700037652193280/M-TVtsKA.jpg',
        'result'=>'false'
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/582247262073425920/N_E803NQ.jpg',
        'image2'=>'https://pbs.twimg.com/profile_images/583663572003102721/qAV045OD.jpg',
        'result'=>'true'
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/579777092780363777/bf-f2ybA.jpg',
        'image2'=>'https://pbs.twimg.com/profile_images/583288559550869504/4GPFuJie.jpg',
        'result'=>'true'
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/580569675207180288/VMQ8mDd9.jpg',
        'image2'=>'https://pbs.twimg.com/profile_images/583521456350339074/TIkMvtiJ.jpg',
        'result'=>'false'
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/572233072629968897/lINgO3W9.jpeg',
        'image2'=>'https://pbs.twimg.com/profile_images/583103569433387008/YuvPwNDD.jpg',
        'result'=>'false'
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/572957953888501760/zdvqnc0P.png',
        'image2'=>'https://pbs.twimg.com/profile_images/583009742345342976/Iys9XcLk.jpg',
        'result'=>'true'
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/580022931884912640/RtcMPF0X.png',
        'image2'=>'https://pbs.twimg.com/profile_images/582944543479304192/JhVATQvJ.png',
        'result'=>'false'
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/585698686186954752/_QaCXbyP.png',
        'image2'=>'https://pbs.twimg.com/profile_images/585336306340732930/UHsOXbS_.png',
        'result'=>'true'
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/578007449115856896/rBr1-z0P.png',
        'image2'=>'https://pbs.twimg.com/profile_images/585397149325598720/Veuv8Cgz.jpg',
        'result'=>'true'
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/582028682274836480/8j354Hbu.jpg',
        'image2'=>'https://pbs.twimg.com/profile_images/585667045343289344/wSqFqYui.jpg',
        'result'=>'false'
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/583836985812475904/GwdgMhCN.jpg',
        'image2'=>'https://pbs.twimg.com/profile_images/585671963160551424/9w27Xom2.jpg',
        'result'=>'false'
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/583371085376319489/1ga51FM9.jpg',
        'image2'=>'https://pbs.twimg.com/profile_images/585083967264563200/PAqPuKEz.png',
        'result'=>'false'
    ),
    array(
        'image1'=>'https://pbs.twimg.com/profile_images/579328659103813633/nAXdisxL.jpg',
        'image2'=>'https://pbs.twimg.com/profile_images/584603424337108992/xbJAz57j.jpg',
        'result'=>'false'
    )
    */
);

echo "<table>";
$i = 0;
foreach ($test_images as $test_image) {
    $i++;
    //Can't hit live endpoint b/c "No 'Access-Control-Allow-Origin' header is present on the requested resource"
    //$api_endpoint = "https://images.thinkup.com/";
    $api_endpoint = "http://dev.upstart.com/img.php";
    $link = $api_endpoint."?image1=".$test_image['image1']."&image2=".
        $test_image['image2']."&s=a93c67c8de750fe13aee546caa4e8a04";
    $img_link1 = $api_endpoint."?url=".$test_image['image1'].
        "&s=a93c67c8de750fe13aee546caa4e8a04&t=avatar";
    $img_link2 = $api_endpoint."?url=".$test_image['image2'].
        "&s=a93c67c8de750fe13aee546caa4e8a04&t=avatar";
    echo '<tr><td><img src="'.
        $img_link1.'" style="border:1px solid black" width="100"></td><td><img src="'.$img_link2.
        '" width="100" style="border:1px solid black"></td><td>';
    echo '<div id="reason'.$i.'"></div><script>
        $(document).ready(function(){
            $.getJSON("'.$link.'", function(json) {
                if (json.show_diff == false) {
                    $(\'#reason'.$i.'\').html(\'No diff: \' + json.reason);
                } else {
                    $(\'#reason'.$i.'\').html(\'Show diff: \' + json.reason);
                }
            });
        });
        </script></td></tr>';
}
echo "</table>";
?>
</body>
</html>

