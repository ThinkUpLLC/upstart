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
 * Get Transactions For Subscription  Sample
 */

include_once ('.config.inc.php'); 

/************************************************************************
 * Instantiate Implementation of Amazon FPS
 * 
 * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants 
 * are defined in the .config.inc.php located in the same 
 * directory as this sample
 ***********************************************************************/
 $service = new Amazon_FPS_Client(AWS_ACCESS_KEY_ID, 
                                       AWS_SECRET_ACCESS_KEY);
 
/************************************************************************
 * Uncomment to try out Mock Service that simulates Amazon_FPS
 * responses without calling Amazon_FPS service.
 *
 * Responses are loaded from local XML files. You can tweak XML files to
 * experiment with various outputs during development
 *
 * XML files available under Amazon/FPS/Mock tree
 *
 ***********************************************************************/
 // $service = new Amazon_FPS_Mock();

/************************************************************************
 * Setup request parameters and uncomment invoke to try out 
 * sample for Get Transactions For Subscription Action
 ***********************************************************************/
 // @TODO: set request. Action can be passed as Amazon_FPS_Model_GetTransactionsForSubscriptionRequest
 // object or array of parameters
 // invokeGetTransactionsForSubscription($service, $request);

                                            
/**
  * Get Transactions For Subscription Action Sample
  * Returns the transactions for a given subscriptionID.
  *   
  * @param Amazon_FPS_Interface $service instance of Amazon_FPS_Interface
  * @param mixed $request Amazon_FPS_Model_GetTransactionsForSubscription or array of parameters
  */
  function invokeGetTransactionsForSubscription(Amazon_FPS_Interface $service, $request) 
  {
      try {
              $response = $service->getTransactionsForSubscription($request);
              
                echo ("Service Response\n");
                echo ("=============================================================================\n");

                echo("        GetTransactionsForSubscriptionResponse\n");
                if ($response->isSetGetTransactionsForSubscriptionResult()) { 
                    echo("            GetTransactionsForSubscriptionResult\n");
                    $getTransactionsForSubscriptionResult = $response->getGetTransactionsForSubscriptionResult();
                    $subscriptionTransactionList = $getTransactionsForSubscriptionResult->getSubscriptionTransaction();
                    foreach ($subscriptionTransactionList as $subscriptionTransaction) {
                        echo("                SubscriptionTransaction\n");
                        if ($subscriptionTransaction->isSetTransactionId()) 
                        {
                            echo("                    TransactionId\n");
                            echo("                        " . $subscriptionTransaction->getTransactionId() . "\n");
                        }
                        if ($subscriptionTransaction->isSetTransactionDate()) 
                        {
                            echo("                    TransactionDate\n");
                            echo("                        " . $subscriptionTransaction->getTransactionDate() . "\n");
                        }
                        if ($subscriptionTransaction->isSetTransactionSerialNumber()) 
                        {
                            echo("                    TransactionSerialNumber\n");
                            echo("                        " . $subscriptionTransaction->getTransactionSerialNumber() . "\n");
                        }
                        if ($subscriptionTransaction->isSetTransactionAmount()) { 
                            echo("                    TransactionAmount\n");
                            $transactionAmount = $subscriptionTransaction->getTransactionAmount();
                            if ($transactionAmount->isSetCurrencyCode()) 
                            {
                                echo("                        CurrencyCode\n");
                                echo("                            " . $transactionAmount->getCurrencyCode() . "\n");
                            }
                            if ($transactionAmount->isSetValue()) 
                            {
                                echo("                        Value\n");
                                echo("                            " . $transactionAmount->getValue() . "\n");
                            }
                        } 
                        if ($subscriptionTransaction->isSetDescription()) 
                        {
                            echo("                    Description\n");
                            echo("                        " . $subscriptionTransaction->getDescription() . "\n");
                        }
                        if ($subscriptionTransaction->isSetTransactionStatus()) 
                        {
                            echo("                    TransactionStatus\n");
                            echo("                        " . $subscriptionTransaction->getTransactionStatus() . "\n");
                        }
                    }
                } 
                if ($response->isSetResponseMetadata()) { 
                    echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId()) 
                    {
                        echo("                RequestId\n");
                        echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                } 

     } catch (Amazon_FPS_Exception $ex) {
         echo("Caught Exception: " . $ex->getMessage() . "\n");
         echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         echo("Error Code: " . $ex->getErrorCode() . "\n");
         echo("Error Type: " . $ex->getErrorType() . "\n");
         echo("Request ID: " . $ex->getRequestId() . "\n");
         echo("XML: " . $ex->getXML() . "\n");
     }
 }
                                                                                        