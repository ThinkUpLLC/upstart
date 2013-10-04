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
 * Amazon_FPS_Model_Duration
 * 
 * Properties:
 * <ul>
 * 
 * <li>Value: int</li>
 * <li>TimeUnit: TimeUnit</li>
 *
 * </ul>
 */ 
class Amazon_FPS_Model_Duration extends Amazon_FPS_Model
{


    /**
     * Construct new Amazon_FPS_Model_Duration
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>Value: int</li>
     * <li>TimeUnit: TimeUnit</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'Value' => array('FieldValue' => null, 'FieldType' => 'int'),
        'TimeUnit' => array('FieldValue' => null, 'FieldType' => 'TimeUnit'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the Value property.
     * 
     * @return int Value
     */
    public function getValue() 
    {
        return $this->_fields['Value']['FieldValue'];
    }

    /**
     * Sets the value of the Value property.
     * 
     * @param int Value
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
     * @param int $value Value
     * @return Amazon_FPS_Model_Duration instance
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

    /**
     * Gets the value of the TimeUnit property.
     * 
     * @return TimeUnit TimeUnit
     */
    public function getTimeUnit() 
    {
        return $this->_fields['TimeUnit']['FieldValue'];
    }

    /**
     * Sets the value of the TimeUnit property.
     * 
     * @param TimeUnit TimeUnit
     * @return this instance
     */
    public function setTimeUnit($value) 
    {
        $this->_fields['TimeUnit']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the TimeUnit and returns this instance
     * 
     * @param TimeUnit $value TimeUnit
     * @return Amazon_FPS_Model_Duration instance
     */
    public function withTimeUnit($value)
    {
        $this->setTimeUnit($value);
        return $this;
    }


    /**
     * Checks if TimeUnit is set
     * 
     * @return bool true if TimeUnit  is set
     */
    public function isSetTimeUnit()
    {
        return !is_null($this->_fields['TimeUnit']['FieldValue']);
    }




}