<?php
/**
 * Mock classes for use in testing.
 */
class Amazon_FPS_SignatureException extends Exception {}

class Amazon_FPS_SignatureUtilsForOutbound {

    public function validateRequest(array $parameters, $urlEndPoint, $httpMethod)  {
      return true;
    }
}