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
//require_once ('Amazon/FPS/Model.php');



/**
 * Amazon_FPS_Model_VerifySignatureResult
 *
 * Properties:
 * <ul>
 *
 * <li>VerificationStatus: VerificationStatus</li>
 *
 * </ul>
 */
class Amazon_FPS_Model_VerifySignatureResult extends Amazon_FPS_Model
{


    /**
     * Construct new Amazon_FPS_Model_VerifySignatureResult
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>VerificationStatus: VerificationStatus</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'VerificationStatus' => array('FieldValue' => null, 'FieldType' => 'VerificationStatus'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the VerificationStatus property.
     *
     * @return VerificationStatus VerificationStatus
     */
    public function getVerificationStatus()
    {
        return $this->_fields['VerificationStatus']['FieldValue'];
    }

    /**
     * Sets the value of the VerificationStatus property.
     *
     * @param VerificationStatus VerificationStatus
     * @return this instance
     */
    public function setVerificationStatus($value)
    {
        $this->_fields['VerificationStatus']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the VerificationStatus and returns this instance
     *
     * @param VerificationStatus $value VerificationStatus
     * @return Amazon_FPS_Model_VerifySignatureResult instance
     */
    public function withVerificationStatus($value)
    {
        $this->setVerificationStatus($value);
        return $this;
    }


    /**
     * Checks if VerificationStatus is set
     *
     * @return bool true if VerificationStatus  is set
     */
    public function isSetVerificationStatus()
    {
        return !is_null($this->_fields['VerificationStatus']['FieldValue']);
    }




}