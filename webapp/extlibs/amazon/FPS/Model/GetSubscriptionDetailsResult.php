<?php
/** 
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     Amazon_FPS
 *  @copyright   Copyright 2008-2009 Amazon.com, Inc. or its affiliates. All Rights Reserved.
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
 *  Generated: Wed Jun 15 05:50:14 GMT+00:00 2011
 * 
 */

/**
 *  @see Amazon_FPS_Model
 */
require_once ('Amazon/FPS/Model.php');  

    

/**
 * Amazon_FPS_Model_GetSubscriptionDetailsResult
 * 
 * Properties:
 * <ul>
 * 
 * <li>SubscriptionDetails: Amazon_FPS_Model_SubscriptionDetails</li>
 *
 * </ul>
 */ 
class Amazon_FPS_Model_GetSubscriptionDetailsResult extends Amazon_FPS_Model
{


    /**
     * Construct new Amazon_FPS_Model_GetSubscriptionDetailsResult
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>SubscriptionDetails: Amazon_FPS_Model_SubscriptionDetails</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'SubscriptionDetails' => array('FieldValue' => null, 'FieldType' => 'Amazon_FPS_Model_SubscriptionDetails'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the SubscriptionDetails.
     * 
     * @return SubscriptionDetails SubscriptionDetails
     */
    public function getSubscriptionDetails() 
    {
        return $this->_fields['SubscriptionDetails']['FieldValue'];
    }

    /**
     * Sets the value of the SubscriptionDetails.
     * 
     * @param SubscriptionDetails SubscriptionDetails
     * @return void
     */
    public function setSubscriptionDetails($value) 
    {
        $this->_fields['SubscriptionDetails']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the SubscriptionDetails  and returns this instance
     * 
     * @param SubscriptionDetails $value SubscriptionDetails
     * @return Amazon_FPS_Model_GetSubscriptionDetailsResult instance
     */
    public function withSubscriptionDetails($value)
    {
        $this->setSubscriptionDetails($value);
        return $this;
    }


    /**
     * Checks if SubscriptionDetails  is set
     * 
     * @return bool true if SubscriptionDetails property is set
     */
    public function isSetSubscriptionDetails()
    {
        return !is_null($this->_fields['SubscriptionDetails']['FieldValue']);

    }




}