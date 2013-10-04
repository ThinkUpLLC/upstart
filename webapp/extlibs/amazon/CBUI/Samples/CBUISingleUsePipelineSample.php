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
 * 
 */

require_once('.config.inc.php');

require_once('Amazon/CBUI/CBUISingleUsePipeline.php');

class CBUISingleUsePipelineSample {

    function test() {
        $pipeline = new Amazon_FPS_CBUISingleUsePipeline(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY);

        $pipeline->setMandatoryParameters("callerReferenceSingleUse",  
                "http://www.mysite.com/call_back.jsp", "5");
        
        //optional parameters
        $pipeline->addParameter("currencyCode", "USD");
        $pipeline->addParameter("paymentReason", "HarryPotter 1-5 DVD set");
        
        //SingleUse url
        print "Sample CBUI url for SingleUse pipeline : " . $pipeline->getUrl() . "\n";
    }
}

CBUISingleUsePipelineSample::test();
