<?php
/** 
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     Amazon_FPS
 *  @copyright   Copyright 2008-2011 Amazon Technologies, Inc.
 *  @link        http://aws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2010-08-28
 */
/******************************************************************************* 
 *    __  _    _  ___ 
 *   (  )( \/\/ )/ __)
 *   /__\ \    / \__ \
 *  (_)(_) \/\/  (___/
 * 
 *  Amazon FPS PHP5 Library
 *  Generated: Wed Sep 23 03:35:04 PDT 2009
 * 
 */

require_once '.config.inc.php';
require_once 'Amazon/IpnReturnUrlValidation/SignatureUtilsForOutbound.php';
  
class Amazon_FPS_ReturnUrlVerificationSampleCode {

	public static function test() {
        $utils = new Amazon_FPS_SignatureUtilsForOutbound();
        
        //Parameters present in return url.
        	$params["signature"] = "dhvus38yMLjAdOkTLHtabDfvqc0StK+3PidoGpEIijbmfnooh48JvHiP9ljMevvndZkN2qY7hTCte/Uj/Vfh1jYb6HyhjARoqeyXbeIPJe70tlN/FH4BuwdILi1bWjbjGiVBLh6aVrs8YVBPBFnj6iy9GX3KvFNkUpY+oXORKDA=";
	$params["expiry"] = "10/2016";
	$params["signatureVersion"] = "2";
	$params["signatureMethod"] = "RSA-SHA1";
	$params["certificateUrl"] = "https://fps.sandbox.amazonaws.com/certs/090910/PKICert.pem?requestId=bjynhas7glasv69dqyfwcj0499uh2ujcvsnh8r0v0bzoc0mc309";
	$params["tokenID"] = "77H84MAUCME17HP5VVIC61KGHXSAX6KS7DJ6PXI5MC5C3LZ8X8RPRKQIAAE3TRP8";
	$params["status"] = "SC";
	$params["callerReference"] = "callerReferenceMultiUse";
 
        $urlEndPoint = "http://www.mysite.com/call_back.jsp"; //Your return url end point. 
        print "Verifying return url signed using signature v2 ....\n";
        //return url is sent as a http GET request and hence we specify GET as the http method.
        //Signature verification does not require your secret key
        print "Is signature correct: " . $utils->validateRequest($params, $urlEndPoint, "GET") . "\n";
	}
}

Amazon_FPS_ReturnUrlVerificationSampleCode::test(); 
?>
