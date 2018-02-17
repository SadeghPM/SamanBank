<?php
namespace SadeghPM\SamanBank\Exception;
/**
 * SamanPayEmptyResponseException 
 */
class SamanPayEmptyResponseException extends \Exception
{
    function __construct()
    {
        parent::__construct('Empty response received!',0);
    }    
}
