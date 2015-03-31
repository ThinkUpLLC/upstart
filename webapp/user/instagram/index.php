<?php
//@TODO: Turn this into a proper controller
//http://dev.upstart.com/user/instagram/?u=ginatrapani&code=c6f70ab7479d4d15b6958d68010ab10c

if (isset($_GET['u']) && isset($_GET['code'])) {
    $destination = 'https://'.$_GET['u'].'.thinkup.com/account/?p=instagram&code='.$_GET['code'];
    header('Location: '.$destination);
}
