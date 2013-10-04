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
 * Amazon_FPS_Model_SubscriptionTransaction
 * 
 * Properties:
 * <ul>
 * 
 * <li>TransactionId: string</li>
 * <li>TransactionDate: string</li>
 * <li>TransactionSerialNumber: int</li>
 * <li>TransactionAmount: Amazon_FPS_Model_Amount</li>
 * <li>Description: string</li>
 * <li>TransactionStatus: TransactionStatus</li>
 *
 * </ul>
 */ 
class Amazon_FPS_Model_SubscriptionTransaction extends Amazon_FPS_Model
{


    /**
     * Construct new Amazon_FPS_Model_SubscriptionTransaction
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>TransactionId: string</li>
     * <li>TransactionDate: string</li>
     * <li>TransactionSerialNumber: int</li>
     * <li>TransactionAmount: Amazon_FPS_Model_Amount</li>
     * <li>Description: string</li>
     * <li>TransactionStatus: TransactionStatus</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'TransactionId' => array('FieldValue' => null, 'FieldType' => 'string'),
        'TransactionDate' => array('FieldValue' => null, 'FieldType' => 'string'),
        'TransactionSerialNumber' => array('FieldValue' => null, 'FieldType' => 'int'),
        'TransactionAmount' => array('FieldValue' => null, 'FieldType' => 'Amazon_FPS_Model_Amount'),
        'Description' => array('FieldValue' => null, 'FieldType' => 'string'),
        'TransactionStatus' => array('FieldValue' => null, 'FieldType' => 'TransactionStatus'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the TransactionId property.
     * 
     * @return string TransactionId
     */
    public function getTransactionId() 
    {
        return $this->_fields['TransactionId']['FieldValue'];
    }

    /**
     * Sets the value of the TransactionId property.
     * 
     * @param string TransactionId
     * @return this instance
     */
    public function setTransactionId($value) 
    {
        $this->_fields['TransactionId']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the TransactionId and returns this instance
     * 
     * @param string $value TransactionId
     * @return Amazon_FPS_Model_SubscriptionTransaction instance
     */
    public function withTransactionId($value)
    {
        $this->setTransactionId($value);
        return $this;
    }


    /**
     * Checks if TransactionId is set
     * 
     * @return bool true if TransactionId  is set
     */
    public function isSetTransactionId()
    {
        return !is_null($this->_fields['TransactionId']['FieldValue']);
    }

    /**
     * Gets the value of the TransactionDate property.
     * 
     * @return string TransactionDate
     */
    public function getTransactionDate() 
    {
        return $this->_fields['TransactionDate']['FieldValue'];
    }

    /**
     * Sets the value of the TransactionDate property.
     * 
     * @param string TransactionDate
     * @return this instance
     */
    public function setTransactionDate($value) 
    {
        $this->_fields['TransactionDate']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the TransactionDate and returns this instance
     * 
     * @param string $value TransactionDate
     * @return Amazon_FPS_Model_SubscriptionTransaction instance
     */
    public function withTransactionDate($value)
    {
        $this->setTransactionDate($value);
        return $this;
    }


    /**
     * Checks if TransactionDate is set
     * 
     * @return bool true if TransactionDate  is set
     */
    public function isSetTransactionDate()
    {
        return !is_null($this->_fields['TransactionDate']['FieldValue']);
    }

    /**
     * Gets the value of the TransactionSerialNumber property.
     * 
     * @return int TransactionSerialNumber
     */
    public function getTransactionSerialNumber() 
    {
        return $this->_fields['TransactionSerialNumber']['FieldValue'];
    }

    /**
     * Sets the value of the TransactionSerialNumber property.
     * 
     * @param int TransactionSerialNumber
     * @return this instance
     */
    public function setTransactionSerialNumber($value) 
    {
        $this->_fields['TransactionSerialNumber']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the TransactionSerialNumber and returns this instance
     * 
     * @param int $value TransactionSerialNumber
     * @return Amazon_FPS_Model_SubscriptionTransaction instance
     */
    public function withTransactionSerialNumber($value)
    {
        $this->setTransactionSerialNumber($value);
        return $this;
    }


    /**
     * Checks if TransactionSerialNumber is set
     * 
     * @return bool true if TransactionSerialNumber  is set
     */
    public function isSetTransactionSerialNumber()
    {
        return !is_null($this->_fields['TransactionSerialNumber']['FieldValue']);
    }

    /**
     * Gets the value of the TransactionAmount.
     * 
     * @return Amount TransactionAmount
     */
    public function getTransactionAmount() 
    {
        return $this->_fields['TransactionAmount']['FieldValue'];
    }

    /**
     * Sets the value of the TransactionAmount.
     * 
     * @param Amount TransactionAmount
     * @return void
     */
    public function setTransactionAmount($value) 
    {
        $this->_fields['TransactionAmount']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the TransactionAmount  and returns this instance
     * 
     * @param Amount $value TransactionAmount
     * @return Amazon_FPS_Model_SubscriptionTransaction instance
     */
    public function withTransactionAmount($value)
    {
        $this->setTransactionAmount($value);
        return $this;
    }


    /**
     * Checks if TransactionAmount  is set
     * 
     * @return bool true if TransactionAmount property is set
     */
    public function isSetTransactionAmount()
    {
        return !is_null($this->_fields['TransactionAmount']['FieldValue']);

    }

    /**
     * Gets the value of the Description property.
     * 
     * @return string Description
     */
    public function getDescription() 
    {
        return $this->_fields['Description']['FieldValue'];
    }

    /**
     * Sets the value of the Description property.
     * 
     * @param string Description
     * @return this instance
     */
    public function setDescription($value) 
    {
        $this->_fields['Description']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the Description and returns this instance
     * 
     * @param string $value Description
     * @return Amazon_FPS_Model_SubscriptionTransaction instance
     */
    public function withDescription($value)
    {
        $this->setDescription($value);
        return $this;
    }


    /**
     * Checks if Description is set
     * 
     * @return bool true if Description  is set
     */
    public function isSetDescription()
    {
        return !is_null($this->_fields['Description']['FieldValue']);
    }

    /**
     * Gets the value of the TransactionStatus property.
     * 
     * @return TransactionStatus TransactionStatus
     */
    public function getTransactionStatus() 
    {
        return $this->_fields['TransactionStatus']['FieldValue'];
    }

    /**
     * Sets the value of the TransactionStatus property.
     * 
     * @param TransactionStatus TransactionStatus
     * @return this instance
     */
    public function setTransactionStatus($value) 
    {
        $this->_fields['TransactionStatus']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the TransactionStatus and returns this instance
     * 
     * @param TransactionStatus $value TransactionStatus
     * @return Amazon_FPS_Model_SubscriptionTransaction instance
     */
    public function withTransactionStatus($value)
    {
        $this->setTransactionStatus($value);
        return $this;
    }


    /**
     * Checks if TransactionStatus is set
     * 
     * @return bool true if TransactionStatus  is set
     */
    public function isSetTransactionStatus()
    {
        return !is_null($this->_fields['TransactionStatus']['FieldValue']);
    }




}