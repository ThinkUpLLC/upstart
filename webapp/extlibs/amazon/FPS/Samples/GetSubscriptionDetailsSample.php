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
 * Get Subscription Details  Sample
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
 * sample for Get Subscription Details Action
 ***********************************************************************/
 // @TODO: set request. Action can be passed as Amazon_FPS_Model_GetSubscriptionDetailsRequest
 // object or array of parameters
 // invokeGetSubscriptionDetails($service, $request);

                                                
/**
  * Get Subscription Details Action Sample
  * Returns the details of Subscription for a given subscriptionID.
  *   
  * @param Amazon_FPS_Interface $service instance of Amazon_FPS_Interface
  * @param mixed $request Amazon_FPS_Model_GetSubscriptionDetails or array of parameters
  */
  function invokeGetSubscriptionDetails(Amazon_FPS_Interface $service, $request) 
  {
      try {
              $response = $service->getSubscriptionDetails($request);
              
                echo ("Service Response\n");
                echo ("=============================================================================\n");

                echo("        GetSubscriptionDetailsResponse\n");
                if ($response->isSetGetSubscriptionDetailsResult()) { 
                    echo("            GetSubscriptionDetailsResult\n");
                    $getSubscriptionDetailsResult = $response->getGetSubscriptionDetailsResult();
                    if ($getSubscriptionDetailsResult->isSetSubscriptionDetails()) { 
                        echo("                SubscriptionDetails\n");
                        $subscriptionDetails = $getSubscriptionDetailsResult->getSubscriptionDetails();
                        if ($subscriptionDetails->isSetSubscriptionId()) 
                        {
                            echo("                    SubscriptionId\n");
                            echo("                        " . $subscriptionDetails->getSubscriptionId() . "\n");
                        }
                        if ($subscriptionDetails->isSetDescription()) 
                        {
                            echo("                    Description\n");
                            echo("                        " . $subscriptionDetails->getDescription() . "\n");
                        }
                        if ($subscriptionDetails->isSetSubscriptionAmount()) { 
                            echo("                    SubscriptionAmount\n");
                            $subscriptionAmount = $subscriptionDetails->getSubscriptionAmount();
                            if ($subscriptionAmount->isSetCurrencyCode()) 
                            {
                                echo("                        CurrencyCode\n");
                                echo("                            " . $subscriptionAmount->getCurrencyCode() . "\n");
                            }
                            if ($subscriptionAmount->isSetValue()) 
                            {
                                echo("                        Value\n");
                                echo("                            " . $subscriptionAmount->getValue() . "\n");
                            }
                        } 
                        if ($subscriptionDetails->isSetNextTransactionAmount()) { 
                            echo("                    NextTransactionAmount\n");
                            $nextTransactionAmount = $subscriptionDetails->getNextTransactionAmount();
                            if ($nextTransactionAmount->isSetCurrencyCode()) 
                            {
                                echo("                        CurrencyCode\n");
                                echo("                            " . $nextTransactionAmount->getCurrencyCode() . "\n");
                            }
                            if ($nextTransactionAmount->isSetValue()) 
                            {
                                echo("                        Value\n");
                                echo("                            " . $nextTransactionAmount->getValue() . "\n");
                            }
                        } 
                        if ($subscriptionDetails->isSetPromotionalAmount()) { 
                            echo("                    PromotionalAmount\n");
                            $promotionalAmount = $subscriptionDetails->getPromotionalAmount();
                            if ($promotionalAmount->isSetCurrencyCode()) 
                            {
                                echo("                        CurrencyCode\n");
                                echo("                            " . $promotionalAmount->getCurrencyCode() . "\n");
                            }
                            if ($promotionalAmount->isSetValue()) 
                            {
                                echo("                        Value\n");
                                echo("                            " . $promotionalAmount->getValue() . "\n");
                            }
                        } 
                        if ($subscriptionDetails->isSetNumberOfPromotionalTransactions()) 
                        {
                            echo("                    NumberOfPromotionalTransactions\n");
                            echo("                        " . $subscriptionDetails->getNumberOfPromotionalTransactions() . "\n");
                        }
                        if ($subscriptionDetails->isSetStartDate()) 
                        {
                            echo("                    StartDate\n");
                            echo("                        " . $subscriptionDetails->getStartDate() . "\n");
                        }
                        if ($subscriptionDetails->isSetEndDate()) 
                        {
                            echo("                    EndDate\n");
                            echo("                        " . $subscriptionDetails->getEndDate() . "\n");
                        }
                        if ($subscriptionDetails->isSetSubscriptionPeriod()) { 
                            echo("                    SubscriptionPeriod\n");
                            $subscriptionPeriod = $subscriptionDetails->getSubscriptionPeriod();
                            if ($subscriptionPeriod->isSetValue()) 
                            {
                                echo("                        Value\n");
                                echo("                            " . $subscriptionPeriod->getValue() . "\n");
                            }
                            if ($subscriptionPeriod->isSetTimeUnit()) 
                            {
                                echo("                        TimeUnit\n");
                                echo("                            " . $subscriptionPeriod->getTimeUnit() . "\n");
                            }
                        } 
                        if ($subscriptionDetails->isSetSubscriptionFrequency()) { 
                            echo("                    SubscriptionFrequency\n");
                            $subscriptionFrequency = $subscriptionDetails->getSubscriptionFrequency();
                            if ($subscriptionFrequency->isSetValue()) 
                            {
                                echo("                        Value\n");
                                echo("                            " . $subscriptionFrequency->getValue() . "\n");
                            }
                            if ($subscriptionFrequency->isSetTimeUnit()) 
                            {
                                echo("                        TimeUnit\n");
                                echo("                            " . $subscriptionFrequency->getTimeUnit() . "\n");
                            }
                        } 
                        if ($subscriptionDetails->isSetOverrideIPNUrl()) 
                        {
                            echo("                    OverrideIPNUrl\n");
                            echo("                        " . $subscriptionDetails->getOverrideIPNUrl() . "\n");
                        }
                        if ($subscriptionDetails->isSetSubscriptionStatus()) 
                        {
                            echo("                    SubscriptionStatus\n");
                            echo("                        " . $subscriptionDetails->getSubscriptionStatus() . "\n");
                        }
                        if ($subscriptionDetails->isSetNumberOfTransactionsProcessed()) 
                        {
                            echo("                    NumberOfTransactionsProcessed\n");
                            echo("                        " . $subscriptionDetails->getNumberOfTransactionsProcessed() . "\n");
                        }
                        if ($subscriptionDetails->isSetRecipientEmail()) 
                        {
                            echo("                    RecipientEmail\n");
                            echo("                        " . $subscriptionDetails->getRecipientEmail() . "\n");
                        }
                        if ($subscriptionDetails->isSetRecipientName()) 
                        {
                            echo("                    RecipientName\n");
                            echo("                        " . $subscriptionDetails->getRecipientName() . "\n");
                        }
                        if ($subscriptionDetails->isSetSenderEmail()) 
                        {
                            echo("                    SenderEmail\n");
                            echo("                        " . $subscriptionDetails->getSenderEmail() . "\n");
                        }
                        if ($subscriptionDetails->isSetSenderName()) 
                        {
                            echo("                    SenderName\n");
                            echo("                        " . $subscriptionDetails->getSenderName() . "\n");
                        }
                        if ($subscriptionDetails->isSetNextTransactionDate()) 
                        {
                            echo("                    NextTransactionDate\n");
                            echo("                        " . $subscriptionDetails->getNextTransactionDate() . "\n");
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
                                                                                    