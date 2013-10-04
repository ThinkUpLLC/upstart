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
 * Amazon_FPS_Model_GetRecipientVerificationStatusResponse
 * 
 * Properties:
 * <ul>
 * 
 * <li>GetRecipientVerificationStatusResult: Amazon_FPS_Model_GetRecipientVerificationStatusResult</li>
 * <li>ResponseMetadata: Amazon_FPS_Model_ResponseMetadata</li>
 *
 * </ul>
 */ 
class Amazon_FPS_Model_GetRecipientVerificationStatusResponse extends Amazon_FPS_Model
{


    /**
     * Construct new Amazon_FPS_Model_GetRecipientVerificationStatusResponse
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>GetRecipientVerificationStatusResult: Amazon_FPS_Model_GetRecipientVerificationStatusResult</li>
     * <li>ResponseMetadata: Amazon_FPS_Model_ResponseMetadata</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'GetRecipientVerificationStatusResult' => array('FieldValue' => null, 'FieldType' => 'Amazon_FPS_Model_GetRecipientVerificationStatusResult'),
        'ResponseMetadata' => array('FieldValue' => null, 'FieldType' => 'Amazon_FPS_Model_ResponseMetadata'),
        );
        parent::__construct($data);
    }

       
    /**
     * Construct Amazon_FPS_Model_GetRecipientVerificationStatusResponse from XML string
     * 
     * @param string $xml XML string to construct from
     * @return Amazon_FPS_Model_GetRecipientVerificationStatusResponse 
     */
    public static function fromXML($xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
    	$xpath->registerNamespace('a', 'http://fps.amazonaws.com/doc/2010-08-28/');
        $response = $xpath->query('//a:GetRecipientVerificationStatusResponse');
        if ($response->length == 1) {
            return new Amazon_FPS_Model_GetRecipientVerificationStatusResponse(($response->item(0))); 
        } else {
            throw new Exception ("Unable to construct Amazon_FPS_Model_GetRecipientVerificationStatusResponse from provided XML. 
                                  Make sure that GetRecipientVerificationStatusResponse is a root element");
        }
          
    }
    
    /**
     * Gets the value of the GetRecipientVerificationStatusResult.
     * 
     * @return GetRecipientVerificationStatusResult GetRecipientVerificationStatusResult
     */
    public function getGetRecipientVerificationStatusResult() 
    {
        return $this->_fields['GetRecipientVerificationStatusResult']['FieldValue'];
    }

    /**
     * Sets the value of the GetRecipientVerificationStatusResult.
     * 
     * @param GetRecipientVerificationStatusResult GetRecipientVerificationStatusResult
     * @return void
     */
    public function setGetRecipientVerificationStatusResult($value) 
    {
        $this->_fields['GetRecipientVerificationStatusResult']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the GetRecipientVerificationStatusResult  and returns this instance
     * 
     * @param GetRecipientVerificationStatusResult $value GetRecipientVerificationStatusResult
     * @return Amazon_FPS_Model_GetRecipientVerificationStatusResponse instance
     */
    public function withGetRecipientVerificationStatusResult($value)
    {
        $this->setGetRecipientVerificationStatusResult($value);
        return $this;
    }


    /**
     * Checks if GetRecipientVerificationStatusResult  is set
     * 
     * @return bool true if GetRecipientVerificationStatusResult property is set
     */
    public function isSetGetRecipientVerificationStatusResult()
    {
        return !is_null($this->_fields['GetRecipientVerificationStatusResult']['FieldValue']);

    }

    /**
     * Gets the value of the ResponseMetadata.
     * 
     * @return ResponseMetadata ResponseMetadata
     */
    public function getResponseMetadata() 
    {
        return $this->_fields['ResponseMetadata']['FieldValue'];
    }

    /**
     * Sets the value of the ResponseMetadata.
     * 
     * @param ResponseMetadata ResponseMetadata
     * @return void
     */
    public function setResponseMetadata($value) 
    {
        $this->_fields['ResponseMetadata']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the ResponseMetadata  and returns this instance
     * 
     * @param ResponseMetadata $value ResponseMetadata
     * @return Amazon_FPS_Model_GetRecipientVerificationStatusResponse instance
     */
    public function withResponseMetadata($value)
    {
        $this->setResponseMetadata($value);
        return $this;
    }


    /**
     * Checks if ResponseMetadata  is set
     * 
     * @return bool true if ResponseMetadata property is set
     */
    public function isSetResponseMetadata()
    {
        return !is_null($this->_fields['ResponseMetadata']['FieldValue']);

    }



    /**
     * XML Representation for this object
     * 
     * @return string XML for this object
     */
    public function toXML() 
    {
        $xml = "";
        $xml .= "<GetRecipientVerificationStatusResponse xmlns=\"http://fps.amazonaws.com/doc/2010-08-28/\">";
        $xml .= $this->_toXMLFragment();
        $xml .= "</GetRecipientVerificationStatusResponse>";
        return $xml;
    }

}