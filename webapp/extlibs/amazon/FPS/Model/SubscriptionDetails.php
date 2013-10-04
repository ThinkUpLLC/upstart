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
 * Amazon_FPS_Model_SubscriptionDetails
 * 
 * Properties:
 * <ul>
 * 
 * <li>SubscriptionId: string</li>
 * <li>Description: string</li>
 * <li>SubscriptionAmount: Amazon_FPS_Model_Amount</li>
 * <li>NextTransactionAmount: Amazon_FPS_Model_Amount</li>
 * <li>PromotionalAmount: Amazon_FPS_Model_Amount</li>
 * <li>NumberOfPromotionalTransactions: int</li>
 * <li>StartDate: string</li>
 * <li>EndDate: string</li>
 * <li>SubscriptionPeriod: Amazon_FPS_Model_Duration</li>
 * <li>SubscriptionFrequency: Amazon_FPS_Model_Duration</li>
 * <li>OverrideIPNUrl: string</li>
 * <li>SubscriptionStatus: SubscriptionStatus</li>
 * <li>NumberOfTransactionsProcessed: int</li>
 * <li>RecipientEmail: string</li>
 * <li>RecipientName: string</li>
 * <li>SenderEmail: string</li>
 * <li>SenderName: string</li>
 * <li>NextTransactionDate: string</li>
 *
 * </ul>
 */ 
class Amazon_FPS_Model_SubscriptionDetails extends Amazon_FPS_Model
{


    /**
     * Construct new Amazon_FPS_Model_SubscriptionDetails
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>SubscriptionId: string</li>
     * <li>Description: string</li>
     * <li>SubscriptionAmount: Amazon_FPS_Model_Amount</li>
     * <li>NextTransactionAmount: Amazon_FPS_Model_Amount</li>
     * <li>PromotionalAmount: Amazon_FPS_Model_Amount</li>
     * <li>NumberOfPromotionalTransactions: int</li>
     * <li>StartDate: string</li>
     * <li>EndDate: string</li>
     * <li>SubscriptionPeriod: Amazon_FPS_Model_Duration</li>
     * <li>SubscriptionFrequency: Amazon_FPS_Model_Duration</li>
     * <li>OverrideIPNUrl: string</li>
     * <li>SubscriptionStatus: SubscriptionStatus</li>
     * <li>NumberOfTransactionsProcessed: int</li>
     * <li>RecipientEmail: string</li>
     * <li>RecipientName: string</li>
     * <li>SenderEmail: string</li>
     * <li>SenderName: string</li>
     * <li>NextTransactionDate: string</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'SubscriptionId' => array('FieldValue' => null, 'FieldType' => 'string'),
        'Description' => array('FieldValue' => null, 'FieldType' => 'string'),
        'SubscriptionAmount' => array('FieldValue' => null, 'FieldType' => 'Amazon_FPS_Model_Amount'),
        'NextTransactionAmount' => array('FieldValue' => null, 'FieldType' => 'Amazon_FPS_Model_Amount'),
        'PromotionalAmount' => array('FieldValue' => null, 'FieldType' => 'Amazon_FPS_Model_Amount'),
        'NumberOfPromotionalTransactions' => array('FieldValue' => null, 'FieldType' => 'int'),
        'StartDate' => array('FieldValue' => null, 'FieldType' => 'string'),
        'EndDate' => array('FieldValue' => null, 'FieldType' => 'string'),
        'SubscriptionPeriod' => array('FieldValue' => null, 'FieldType' => 'Amazon_FPS_Model_Duration'),
        'SubscriptionFrequency' => array('FieldValue' => null, 'FieldType' => 'Amazon_FPS_Model_Duration'),
        'OverrideIPNUrl' => array('FieldValue' => null, 'FieldType' => 'string'),
        'SubscriptionStatus' => array('FieldValue' => null, 'FieldType' => 'SubscriptionStatus'),
        'NumberOfTransactionsProcessed' => array('FieldValue' => null, 'FieldType' => 'int'),
        'RecipientEmail' => array('FieldValue' => null, 'FieldType' => 'string'),
        'RecipientName' => array('FieldValue' => null, 'FieldType' => 'string'),
        'SenderEmail' => array('FieldValue' => null, 'FieldType' => 'string'),
        'SenderName' => array('FieldValue' => null, 'FieldType' => 'string'),
        'NextTransactionDate' => array('FieldValue' => null, 'FieldType' => 'string'),
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
     * @return Amazon_FPS_Model_SubscriptionDetails instance
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
     * @return Amazon_FPS_Model_SubscriptionDetails instance
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
     * Gets the value of the SubscriptionAmount.
     * 
     * @return Amount SubscriptionAmount
     */
    public function getSubscriptionAmount() 
    {
        return $this->_fields['SubscriptionAmount']['FieldValue'];
    }

    /**
     * Sets the value of the SubscriptionAmount.
     * 
     * @param Amount SubscriptionAmount
     * @return void
     */
    public function setSubscriptionAmount($value) 
    {
        $this->_fields['SubscriptionAmount']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the SubscriptionAmount  and returns this instance
     * 
     * @param Amount $value SubscriptionAmount
     * @return Amazon_FPS_Model_SubscriptionDetails instance
     */
    public function withSubscriptionAmount($value)
    {
        $this->setSubscriptionAmount($value);
        return $this;
    }


    /**
     * Checks if SubscriptionAmount  is set
     * 
     * @return bool true if SubscriptionAmount property is set
     */
    public function isSetSubscriptionAmount()
    {
        return !is_null($this->_fields['SubscriptionAmount']['FieldValue']);

    }

    /**
     * Gets the value of the NextTransactionAmount.
     * 
     * @return Amount NextTransactionAmount
     */
    public function getNextTransactionAmount() 
    {
        return $this->_fields['NextTransactionAmount']['FieldValue'];
    }

    /**
     * Sets the value of the NextTransactionAmount.
     * 
     * @param Amount NextTransactionAmount
     * @return void
     */
    public function setNextTransactionAmount($value) 
    {
        $this->_fields['NextTransactionAmount']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the NextTransactionAmount  and returns this instance
     * 
     * @param Amount $value NextTransactionAmount
     * @return Amazon_FPS_Model_SubscriptionDetails instance
     */
    public function withNextTransactionAmount($value)
    {
        $this->setNextTransactionAmount($value);
        return $this;
    }


    /**
     * Checks if NextTransactionAmount  is set
     * 
     * @return bool true if NextTransactionAmount property is set
     */
    public function isSetNextTransactionAmount()
    {
        return !is_null($this->_fields['NextTransactionAmount']['FieldValue']);

    }

    /**
     * Gets the value of the PromotionalAmount.
     * 
     * @return Amount PromotionalAmount
     */
    public function getPromotionalAmount() 
    {
        return $this->_fields['PromotionalAmount']['FieldValue'];
    }

    /**
     * Sets the value of the PromotionalAmount.
     * 
     * @param Amount PromotionalAmount
     * @return void
     */
    public function setPromotionalAmount($value) 
    {
        $this->_fields['PromotionalAmount']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the PromotionalAmount  and returns this instance
     * 
     * @param Amount $value PromotionalAmount
     * @return Amazon_FPS_Model_SubscriptionDetails instance
     */
    public function withPromotionalAmount($value)
    {
        $this->setPromotionalAmount($value);
        return $this;
    }


    /**
     * Checks if PromotionalAmount  is set
     * 
     * @return bool true if PromotionalAmount property is set
     */
    public function isSetPromotionalAmount()
    {
        return !is_null($this->_fields['PromotionalAmount']['FieldValue']);

    }

    /**
     * Gets the value of the NumberOfPromotionalTransactions property.
     * 
     * @return int NumberOfPromotionalTransactions
     */
    public function getNumberOfPromotionalTransactions() 
    {
        return $this->_fields['NumberOfPromotionalTransactions']['FieldValue'];
    }

    /**
     * Sets the value of the NumberOfPromotionalTransactions property.
     * 
     * @param int NumberOfPromotionalTransactions
     * @return this instance
     */
    public function setNumberOfPromotionalTransactions($value) 
    {
        $this->_fields['NumberOfPromotionalTransactions']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the NumberOfPromotionalTransactions and returns this instance
     * 
     * @param int $value NumberOfPromotionalTransactions
     * @return Amazon_FPS_Model_SubscriptionDetails instance
     */
    public function withNumberOfPromotionalTransactions($value)
    {
        $this->setNumberOfPromotionalTransactions($value);
        return $this;
    }


    /**
     * Checks if NumberOfPromotionalTransactions is set
     * 
     * @return bool true if NumberOfPromotionalTransactions  is set
     */
    public function isSetNumberOfPromotionalTransactions()
    {
        return !is_null($this->_fields['NumberOfPromotionalTransactions']['FieldValue']);
    }

    /**
     * Gets the value of the StartDate property.
     * 
     * @return string StartDate
     */
    public function getStartDate() 
    {
        return $this->_fields['StartDate']['FieldValue'];
    }

    /**
     * Sets the value of the StartDate property.
     * 
     * @param string StartDate
     * @return this instance
     */
    public function setStartDate($value) 
    {
        $this->_fields['StartDate']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the StartDate and returns this instance
     * 
     * @param string $value StartDate
     * @return Amazon_FPS_Model_SubscriptionDetails instance
     */
    public function withStartDate($value)
    {
        $this->setStartDate($value);
        return $this;
    }


    /**
     * Checks if StartDate is set
     * 
     * @return bool true if StartDate  is set
     */
    public function isSetStartDate()
    {
        return !is_null($this->_fields['StartDate']['FieldValue']);
    }

    /**
     * Gets the value of the EndDate property.
     * 
     * @return string EndDate
     */
    public function getEndDate() 
    {
        return $this->_fields['EndDate']['FieldValue'];
    }

    /**
     * Sets the value of the EndDate property.
     * 
     * @param string EndDate
     * @return this instance
     */
    public function setEndDate($value) 
    {
        $this->_fields['EndDate']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the EndDate and returns this instance
     * 
     * @param string $value EndDate
     * @return Amazon_FPS_Model_SubscriptionDetails instance
     */
    public function withEndDate($value)
    {
        $this->setEndDate($value);
        return $this;
    }


    /**
     * Checks if EndDate is set
     * 
     * @return bool true if EndDate  is set
     */
    public function isSetEndDate()
    {
        return !is_null($this->_fields['EndDate']['FieldValue']);
    }

    /**
     * Gets the value of the SubscriptionPeriod.
     * 
     * @return Duration SubscriptionPeriod
     */
    public function getSubscriptionPeriod() 
    {
        return $this->_fields['SubscriptionPeriod']['FieldValue'];
    }

    /**
     * Sets the value of the SubscriptionPeriod.
     * 
     * @param Duration SubscriptionPeriod
     * @return void
     */
    public function setSubscriptionPeriod($value) 
    {
        $this->_fields['SubscriptionPeriod']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the SubscriptionPeriod  and returns this instance
     * 
     * @param Duration $value SubscriptionPeriod
     * @return Amazon_FPS_Model_SubscriptionDetails instance
     */
    public function withSubscriptionPeriod($value)
    {
        $this->setSubscriptionPeriod($value);
        return $this;
    }


    /**
     * Checks if SubscriptionPeriod  is set
     * 
     * @return bool true if SubscriptionPeriod property is set
     */
    public function isSetSubscriptionPeriod()
    {
        return !is_null($this->_fields['SubscriptionPeriod']['FieldValue']);

    }

    /**
     * Gets the value of the SubscriptionFrequency.
     * 
     * @return Duration SubscriptionFrequency
     */
    public function getSubscriptionFrequency() 
    {
        return $this->_fields['SubscriptionFrequency']['FieldValue'];
    }

    /**
     * Sets the value of the SubscriptionFrequency.
     * 
     * @param Duration SubscriptionFrequency
     * @return void
     */
    public function setSubscriptionFrequency($value) 
    {
        $this->_fields['SubscriptionFrequency']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the SubscriptionFrequency  and returns this instance
     * 
     * @param Duration $value SubscriptionFrequency
     * @return Amazon_FPS_Model_SubscriptionDetails instance
     */
    public function withSubscriptionFrequency($value)
    {
        $this->setSubscriptionFrequency($value);
        return $this;
    }


    /**
     * Checks if SubscriptionFrequency  is set
     * 
     * @return bool true if SubscriptionFrequency property is set
     */
    public function isSetSubscriptionFrequency()
    {
        return !is_null($this->_fields['SubscriptionFrequency']['FieldValue']);

    }

    /**
     * Gets the value of the OverrideIPNUrl property.
     * 
     * @return string OverrideIPNUrl
     */
    public function getOverrideIPNUrl() 
    {
        return $this->_fields['OverrideIPNUrl']['FieldValue'];
    }

    /**
     * Sets the value of the OverrideIPNUrl property.
     * 
     * @param string OverrideIPNUrl
     * @return this instance
     */
    public function setOverrideIPNUrl($value) 
    {
        $this->_fields['OverrideIPNUrl']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the OverrideIPNUrl and returns this instance
     * 
     * @param string $value OverrideIPNUrl
     * @return Amazon_FPS_Model_SubscriptionDetails instance
     */
    public function withOverrideIPNUrl($value)
    {
        $this->setOverrideIPNUrl($value);
        return $this;
    }


    /**
     * Checks if OverrideIPNUrl is set
     * 
     * @return bool true if OverrideIPNUrl  is set
     */
    public function isSetOverrideIPNUrl()
    {
        return !is_null($this->_fields['OverrideIPNUrl']['FieldValue']);
    }

    /**
     * Gets the value of the SubscriptionStatus property.
     * 
     * @return SubscriptionStatus SubscriptionStatus
     */
    public function getSubscriptionStatus() 
    {
        return $this->_fields['SubscriptionStatus']['FieldValue'];
    }

    /**
     * Sets the value of the SubscriptionStatus property.
     * 
     * @param SubscriptionStatus SubscriptionStatus
     * @return this instance
     */
    public function setSubscriptionStatus($value) 
    {
        $this->_fields['SubscriptionStatus']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the SubscriptionStatus and returns this instance
     * 
     * @param SubscriptionStatus $value SubscriptionStatus
     * @return Amazon_FPS_Model_SubscriptionDetails instance
     */
    public function withSubscriptionStatus($value)
    {
        $this->setSubscriptionStatus($value);
        return $this;
    }


    /**
     * Checks if SubscriptionStatus is set
     * 
     * @return bool true if SubscriptionStatus  is set
     */
    public function isSetSubscriptionStatus()
    {
        return !is_null($this->_fields['SubscriptionStatus']['FieldValue']);
    }

    /**
     * Gets the value of the NumberOfTransactionsProcessed property.
     * 
     * @return int NumberOfTransactionsProcessed
     */
    public function getNumberOfTransactionsProcessed() 
    {
        return $this->_fields['NumberOfTransactionsProcessed']['FieldValue'];
    }

    /**
     * Sets the value of the NumberOfTransactionsProcessed property.
     * 
     * @param int NumberOfTransactionsProcessed
     * @return this instance
     */
    public function setNumberOfTransactionsProcessed($value) 
    {
        $this->_fields['NumberOfTransactionsProcessed']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the NumberOfTransactionsProcessed and returns this instance
     * 
     * @param int $value NumberOfTransactionsProcessed
     * @return Amazon_FPS_Model_SubscriptionDetails instance
     */
    public function withNumberOfTransactionsProcessed($value)
    {
        $this->setNumberOfTransactionsProcessed($value);
        return $this;
    }


    /**
     * Checks if NumberOfTransactionsProcessed is set
     * 
     * @return bool true if NumberOfTransactionsProcessed  is set
     */
    public function isSetNumberOfTransactionsProcessed()
    {
        return !is_null($this->_fields['NumberOfTransactionsProcessed']['FieldValue']);
    }

    /**
     * Gets the value of the RecipientEmail property.
     * 
     * @return string RecipientEmail
     */
    public function getRecipientEmail() 
    {
        return $this->_fields['RecipientEmail']['FieldValue'];
    }

    /**
     * Sets the value of the RecipientEmail property.
     * 
     * @param string RecipientEmail
     * @return this instance
     */
    public function setRecipientEmail($value) 
    {
        $this->_fields['RecipientEmail']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the RecipientEmail and returns this instance
     * 
     * @param string $value RecipientEmail
     * @return Amazon_FPS_Model_SubscriptionDetails instance
     */
    public function withRecipientEmail($value)
    {
        $this->setRecipientEmail($value);
        return $this;
    }


    /**
     * Checks if RecipientEmail is set
     * 
     * @return bool true if RecipientEmail  is set
     */
    public function isSetRecipientEmail()
    {
        return !is_null($this->_fields['RecipientEmail']['FieldValue']);
    }

    /**
     * Gets the value of the RecipientName property.
     * 
     * @return string RecipientName
     */
    public function getRecipientName() 
    {
        return $this->_fields['RecipientName']['FieldValue'];
    }

    /**
     * Sets the value of the RecipientName property.
     * 
     * @param string RecipientName
     * @return this instance
     */
    public function setRecipientName($value) 
    {
        $this->_fields['RecipientName']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the RecipientName and returns this instance
     * 
     * @param string $value RecipientName
     * @return Amazon_FPS_Model_SubscriptionDetails instance
     */
    public function withRecipientName($value)
    {
        $this->setRecipientName($value);
        return $this;
    }


    /**
     * Checks if RecipientName is set
     * 
     * @return bool true if RecipientName  is set
     */
    public function isSetRecipientName()
    {
        return !is_null($this->_fields['RecipientName']['FieldValue']);
    }

    /**
     * Gets the value of the SenderEmail property.
     * 
     * @return string SenderEmail
     */
    public function getSenderEmail() 
    {
        return $this->_fields['SenderEmail']['FieldValue'];
    }

    /**
     * Sets the value of the SenderEmail property.
     * 
     * @param string SenderEmail
     * @return this instance
     */
    public function setSenderEmail($value) 
    {
        $this->_fields['SenderEmail']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the SenderEmail and returns this instance
     * 
     * @param string $value SenderEmail
     * @return Amazon_FPS_Model_SubscriptionDetails instance
     */
    public function withSenderEmail($value)
    {
        $this->setSenderEmail($value);
        return $this;
    }


    /**
     * Checks if SenderEmail is set
     * 
     * @return bool true if SenderEmail  is set
     */
    public function isSetSenderEmail()
    {
        return !is_null($this->_fields['SenderEmail']['FieldValue']);
    }

    /**
     * Gets the value of the SenderName property.
     * 
     * @return string SenderName
     */
    public function getSenderName() 
    {
        return $this->_fields['SenderName']['FieldValue'];
    }

    /**
     * Sets the value of the SenderName property.
     * 
     * @param string SenderName
     * @return this instance
     */
    public function setSenderName($value) 
    {
        $this->_fields['SenderName']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the SenderName and returns this instance
     * 
     * @param string $value SenderName
     * @return Amazon_FPS_Model_SubscriptionDetails instance
     */
    public function withSenderName($value)
    {
        $this->setSenderName($value);
        return $this;
    }


    /**
     * Checks if SenderName is set
     * 
     * @return bool true if SenderName  is set
     */
    public function isSetSenderName()
    {
        return !is_null($this->_fields['SenderName']['FieldValue']);
    }

    /**
     * Gets the value of the NextTransactionDate property.
     * 
     * @return string NextTransactionDate
     */
    public function getNextTransactionDate() 
    {
        return $this->_fields['NextTransactionDate']['FieldValue'];
    }

    /**
     * Sets the value of the NextTransactionDate property.
     * 
     * @param string NextTransactionDate
     * @return this instance
     */
    public function setNextTransactionDate($value) 
    {
        $this->_fields['NextTransactionDate']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the NextTransactionDate and returns this instance
     * 
     * @param string $value NextTransactionDate
     * @return Amazon_FPS_Model_SubscriptionDetails instance
     */
    public function withNextTransactionDate($value)
    {
        $this->setNextTransactionDate($value);
        return $this;
    }


    /**
     * Checks if NextTransactionDate is set
     * 
     * @return bool true if NextTransactionDate  is set
     */
    public function isSetNextTransactionDate()
    {
        return !is_null($this->_fields['NextTransactionDate']['FieldValue']);
    }




}