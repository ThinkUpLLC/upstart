<?php
/**
 * Mock classes for use in testing.
 */
class SignatureException extends Exception {}

class SignatureUtilsForOutbound {

    public function validateRequest(array $parameters, $urlEndPoint, $httpMethod)  {
      return true;
    }
}