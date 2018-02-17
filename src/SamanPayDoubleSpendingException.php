<?php

namespace SadeghPM\SamanBank;
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
