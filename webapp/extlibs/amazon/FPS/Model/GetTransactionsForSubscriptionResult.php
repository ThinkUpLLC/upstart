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
 * Amazon_FPS_Model_GetTransactionsForSubscriptionResult
 * 
 * Properties:
 * <ul>
 * 
 * <li>SubscriptionTransaction: Amazon_FPS_Model_SubscriptionTransaction</li>
 *
 * </ul>
 */ 
class Amazon_FPS_Model_GetTransactionsForSubscriptionResult extends Amazon_FPS_Model
{


    /**
     * Construct new Amazon_FPS_Model_GetTransactionsForSubscriptionResult
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>SubscriptionTransaction: Amazon_FPS_Model_SubscriptionTransaction</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'SubscriptionTransaction' => array('FieldValue' => array(), 'FieldType' => array('Amazon_FPS_Model_SubscriptionTransaction')),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the SubscriptionTransaction.
     * 
     * @return array of SubscriptionTransaction SubscriptionTransaction
     */
    public function getSubscriptionTransaction() 
    {
        return $this->_fields['SubscriptionTransaction']['FieldValue'];
    }

    /**
     * Sets the value of the SubscriptionTransaction.
     * 
     * @param mixed SubscriptionTransaction or an array of SubscriptionTransaction SubscriptionTransaction
     * @return this instance
     */
    public function setSubscriptionTransaction($subscriptionTransaction) 
    {
        if (!$this->_isNumericArray($subscriptionTransaction)) {
            $subscriptionTransaction =  array ($subscriptionTransaction);    
        }
        $this->_fields['SubscriptionTransaction']['FieldValue'] = $subscriptionTransaction;
        return $this;
    }


    /**
     * Sets single or multiple values of SubscriptionTransaction list via variable number of arguments. 
     * For example, to set the list with two elements, simply pass two values as arguments to this function
     * <code>withSubscriptionTransaction($subscriptionTransaction1, $subscriptionTransaction2)</code>
     * 
     * @param SubscriptionTransaction  $subscriptionTransactionArgs one or more SubscriptionTransaction
     * @return Amazon_FPS_Model_GetTransactionsForSubscriptionResult  instance
     */
    public function withSubscriptionTransaction($subscriptionTransactionArgs)
    {
        foreach (func_get_args() as $subscriptionTransaction) {
            $this->_fields['SubscriptionTransaction']['FieldValue'][] = $subscriptionTransaction;
        }
        return $this;
    }   



    /**
     * Checks if SubscriptionTransaction list is non-empty
     * 
     * @return bool true if SubscriptionTransaction list is non-empty
     */
    public function isSetSubscriptionTransaction()
    {
        return count ($this->_fields['SubscriptionTransaction']['FieldValue']) > 0;
    }




}