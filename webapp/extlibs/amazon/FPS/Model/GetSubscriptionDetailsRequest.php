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
 * Amazon_FPS_Model_GetSubscriptionDetailsRequest
 * 
 * Properties:
 * <ul>
 * 
 * <li>SubscriptionId: string</li>
 *
 * </ul>
 */ 
class Amazon_FPS_Model_GetSubscriptionDetailsRequest extends Amazon_FPS_Model
{


    /**
     * Construct new Amazon_FPS_Model_GetSubscriptionDetailsRequest
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>SubscriptionId: string</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'SubscriptionId' => array('FieldValue' => null, 'FieldType' => 'string'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the SubscriptionId property.
     * 
     * @return string SubscriptionId
     */
    public function getSubscriptionId() 
    {
        return $this->_fields['SubscriptionId']['FieldValue'];
    }

    /**
     * Sets the value of the SubscriptionId property.
     * 
     * @param string SubscriptionId
     * @return this instance
     */
    public function setSubscriptionId($value) 
    {
        $this->_fields['SubscriptionId']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the SubscriptionId and returns this instance
     * 
     * @param string $value SubscriptionId
     * @return Amazon_FPS_Model_GetSubscriptionDetailsRequest instance
     */
    public function withSubscriptionId($value)
    {
        $this->setSubscriptionId($value);
        return $this;
    }


    /**
     * Checks if SubscriptionId is set
     * 
     * @return bool true if SubscriptionId  is set
     */
    public function isSetSubscriptionId()
    {
        return !is_null($this->_fields['SubscriptionId']['FieldValue']);
    }




}