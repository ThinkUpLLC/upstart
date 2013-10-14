<?php

include "extlibs/mailchimp/MailChimp.class.php";
$MailChimp = new MailChimp('457ce78a601662b1d532943b08623feb-us6');

$email = htmlspecialchars($_GET['email']);

$result = $MailChimp->call('lists/subscribe', array(
                'id'                => 'dffdb8d09e',
                'email'             => array('email'=>$email),
                'merge_vars'        => array('FNAME'=>'Davy', 'LNAME'=>'Jones'),
                'update_existing'   => true,
                'replace_interests' => false,
            ));

header('Content-type: application/json');
print_r(json_encode($result));

?>