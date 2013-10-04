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
 * Amazon_FPS_Model_GetRecipientVerificationStatusResult
 * 
 * Properties:
 * <ul>
 * 
 * <li>RecipientVerificationStatus: RecipientVerificationStatus</li>
 *
 * </ul>
 */ 
class Amazon_FPS_Model_GetRecipientVerificationStatusResult extends Amazon_FPS_Model
{


    /**
     * Construct new Amazon_FPS_Model_GetRecipientVerificationStatusResult
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>RecipientVerificationStatus: RecipientVerificationStatus</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'RecipientVerificationStatus' => array('FieldValue' => null, 'FieldType' => 'RecipientVerificationStatus'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the RecipientVerificationStatus property.
     * 
     * @return RecipientVerificationStatus RecipientVerificationStatus
     */
    public function getRecipientVerificationStatus() 
    {
        return $this->_fields['RecipientVerificationStatus']['FieldValue'];
    }

    /**
     * Sets the value of the RecipientVerificationStatus property.
     * 
     * @param RecipientVerificationStatus RecipientVerificationStatus
     * @return this instance
     */
    public function setRecipientVerificationStatus($value) 
    {
        $this->_fields['RecipientVerificationStatus']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the RecipientVerificationStatus and returns this instance
     * 
     * @param RecipientVerificationStatus $value RecipientVerificationStatus
     * @return Amazon_FPS_Model_GetRecipientVerificationStatusResult instance
     */
    public function withRecipientVerificationStatus($value)
    {
        $this->setRecipientVerificationStatus($value);
        return $this;
    }


    /**
     * Checks if RecipientVerificationStatus is set
     * 
     * @return bool true if RecipientVerificationStatus  is set
     */
    public function isSetRecipientVerificationStatus()
    {
        return !is_null($this->_fields['RecipientVerificationStatus']['FieldValue']);
    }




}