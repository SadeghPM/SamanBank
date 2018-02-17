<?php

namespace SadeghPM\SamanBank\Exception;
/**
 * SamanPayDoubleSpendingException
 */
class SamanPayDoubleSpendingException extends \Exception
{
    function __construct()
    {
        parent::__construct('double spending exception', 0);
    }
}
