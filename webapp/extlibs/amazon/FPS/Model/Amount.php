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
 * Amazon_FPS_Model_Amount
 *
 * Properties:
 * <ul>
 *
 * <li>CurrencyCode: CurrencyCode</li>
 * <li>Value: string</li>
 *
 * </ul>
 */
class Amazon_FPS_Model_Amount extends Amazon_FPS_Model
{


    /**
     * Construct new Amazon_FPS_Model_Amount
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>CurrencyCode: CurrencyCode</li>
     * <li>Value: string</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'CurrencyCode' => array('FieldValue' => null, 'FieldType' => 'CurrencyCode'),
        'Value' => array('FieldValue' => null, 'FieldType' => 'string'),
        );
        //print_r($data);
        parent::__construct($data);
    }

        /**
     * Gets the value of the CurrencyCode property.
     *
     * @return CurrencyCode CurrencyCode
     */
    public function getCurrencyCode()
    {
        return $this->_fields['CurrencyCode']['FieldValue'];
    }

    /**
     * Sets the value of the CurrencyCode property.
     *
     * @param CurrencyCode CurrencyCode
     * @return this instance
     */
    public function setCurrencyCode($value)
    {
        $this->_fields['CurrencyCode']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the CurrencyCode and returns this instance
     *
     * @param CurrencyCode $value CurrencyCode
     * @return Amazon_FPS_Model_Amount instance
     */
    public function withCurrencyCode($value)
    {
        $this->setCurrencyCode($value);
        return $this;
    }


    /**
     * Checks if CurrencyCode is set
     *
     * @return bool true if CurrencyCode  is set
     */
    public function isSetCurrencyCode()
    {
        return !is_null($this->_fields['CurrencyCode']['FieldValue']);
    }

    /**
     * Gets the value of the Value property.
     *
     * @return string Value
     */
    public function getValue()
    {
        return $this->_fields['Value']['FieldValue'];
    }

    /**
     * Sets the value of the Value property.
     *
     * @param string Value
     * @return this instance
     */
    public function setValue($value)
    {
        $this->_fields['Value']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the Value and returns this instance
     *
     * @param string $value Value
     * @return Amazon_FPS_Model_Amount instance
     */
    public function withValue($value)
    {
        $this->setValue($value);
        return $this;
    }


    /**
     * Checks if Value is set
     *
     * @return bool true if Value  is set
     */
    public function isSetValue()
    {
        return !is_null($this->_fields['Value']['FieldValue']);
    }




}
