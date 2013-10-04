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
 * Amazon_FPS_Model_CancelTokenRequest
 * 
 * Properties:
 * <ul>
 * 
 * <li>TokenId: string</li>
 * <li>ReasonText: string</li>
 * <li>OverrideIPNURL: string</li>
 *
 * </ul>
 */ 
class Amazon_FPS_Model_CancelTokenRequest extends Amazon_FPS_Model
{


    /**
     * Construct new Amazon_FPS_Model_CancelTokenRequest
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>TokenId: string</li>
     * <li>ReasonText: string</li>
     * <li>OverrideIPNURL: string</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'TokenId' => array('FieldValue' => null, 'FieldType' => 'string'),
        'ReasonText' => array('FieldValue' => null, 'FieldType' => 'string'),
        'OverrideIPNURL' => array('FieldValue' => null, 'FieldType' => 'string'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the TokenId property.
     * 
     * @return string TokenId
     */
    public function getTokenId() 
    {
        return $this->_fields['TokenId']['FieldValue'];
    }

    /**
     * Sets the value of the TokenId property.
     * 
     * @param string TokenId
     * @return this instance
     */
    public function setTokenId($value) 
    {
        $this->_fields['TokenId']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the TokenId and returns this instance
     * 
     * @param string $value TokenId
     * @return Amazon_FPS_Model_CancelTokenRequest instance
     */
    public function withTokenId($value)
    {
        $this->setTokenId($value);
        return $this;
    }


    /**
     * Checks if TokenId is set
     * 
     * @return bool true if TokenId  is set
     */
    public function isSetTokenId()
    {
        return !is_null($this->_fields['TokenId']['FieldValue']);
    }

    /**
     * Gets the value of the ReasonText property.
     * 
     * @return string ReasonText
     */
    public function getReasonText() 
    {
        return $this->_fields['ReasonText']['FieldValue'];
    }

    /**
     * Sets the value of the ReasonText property.
     * 
     * @param string ReasonText
     * @return this instance
     */
    public function setReasonText($value) 
    {
        $this->_fields['ReasonText']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the ReasonText and returns this instance
     * 
     * @param string $value ReasonText
     * @return Amazon_FPS_Model_CancelTokenRequest instance
     */
    public function withReasonText($value)
    {
        $this->setReasonText($value);
        return $this;
    }


    /**
     * Checks if ReasonText is set
     * 
     * @return bool true if ReasonText  is set
     */
    public function isSetReasonText()
    {
        return !is_null($this->_fields['ReasonText']['FieldValue']);
    }

    /**
     * Gets the value of the OverrideIPNURL property.
     * 
     * @return string OverrideIPNURL
     */
    public function getOverrideIPNURL() 
    {
        return $this->_fields['OverrideIPNURL']['FieldValue'];
    }

    /**
     * Sets the value of the OverrideIPNURL property.
     * 
     * @param string OverrideIPNURL
     * @return this instance
     */
    public function setOverrideIPNURL($value) 
    {
        $this->_fields['OverrideIPNURL']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the OverrideIPNURL and returns this instance
     * 
     * @param string $value OverrideIPNURL
     * @return Amazon_FPS_Model_CancelTokenRequest instance
     */
    public function withOverrideIPNURL($value)
    {
        $this->setOverrideIPNURL($value);
        return $this;
    }


    /**
     * Checks if OverrideIPNURL is set
     * 
     * @return bool true if OverrideIPNURL  is set
     */
    public function isSetOverrideIPNURL()
    {
        return !is_null($this->_fields['OverrideIPNURL']['FieldValue']);
    }




}