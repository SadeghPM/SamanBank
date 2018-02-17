<?php

namespace SadeghPM\SamanBank;

interface SamanStorageAdapterInterface
{

    /**
     * Save pay request information
     * @param string $ResNum pay request Result Number
     * @param int $Amount pay request amount in rial
     * @return mixed
     */
    public function savePay(string $ResNum, int $Amount);

    /**
     * @param string $RefNum
     * @return bool
     */
    public function isDoubleSpending(string $RefNum): bool;

    /**
     * get pay amount from Result NUmber
     * @param string $ResNum
     * @return int
     */
    public function getPayAmount(string $ResNum): int;

    /**
     * pay is successful and save its params
     * @param string $ResNum
     * @param string $RefNum
     * @param string $State
     * @param int $StateCode
     * @return mixed
     */
    public function logSuccessfulPay(string $ResNum, string $RefNum, string $State = 'OK', int $StateCode);

    /**
     * fault in payment, save it
     * @param string $ResNum
     * @param string $RefNum
     * @param string $State fault reason in string
     * @param int $StateCode fault code
     * @return mixed
     */
    public function logErrorPay(string $ResNum, string $RefNum, string $State, int $StateCode);

}