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
 * Amazon_FPS_Model_GetRecipientVerificationStatusRequest
 * 
 * Properties:
 * <ul>
 * 
 * <li>RecipientTokenId: string</li>
 *
 * </ul>
 */ 
class Amazon_FPS_Model_GetRecipientVerificationStatusRequest extends Amazon_FPS_Model
{


    /**
     * Construct new Amazon_FPS_Model_GetRecipientVerificationStatusRequest
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>RecipientTokenId: string</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'RecipientTokenId' => array('FieldValue' => null, 'FieldType' => 'string'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the RecipientTokenId property.
     * 
     * @return string RecipientTokenId
     */
    public function getRecipientTokenId() 
    {
        return $this->_fields['RecipientTokenId']['FieldValue'];
    }

    /**
     * Sets the value of the RecipientTokenId property.
     * 
     * @param string RecipientTokenId
     * @return this instance
     */
    public function setRecipientTokenId($value) 
    {
        $this->_fields['RecipientTokenId']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the RecipientTokenId and returns this instance
     * 
     * @param string $value RecipientTokenId
     * @return Amazon_FPS_Model_GetRecipientVerificationStatusRequest instance
     */
    public function withRecipientTokenId($value)
    {
        $this->setRecipientTokenId($value);
        return $this;
    }


    /**
     * Checks if RecipientTokenId is set
     * 
     * @return bool true if RecipientTokenId  is set
     */
    public function isSetRecipientTokenId()
    {
        return !is_null($this->_fields['RecipientTokenId']['FieldValue']);
    }




}