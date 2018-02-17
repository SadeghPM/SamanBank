<?php

namespace SadeghPM\SamanBank;
/**
 * SamanPayException
 */
class SamanPayException extends \Exception
{

    function __construct($message)
    {
        parent::__construct($this->translateMessage($message));
    }

    public function translateMessage($enMessage)
    {
        $messages =
            [
                'Canceled By User' => 'تراکنش توسط خریدار کنسل شده است.',
                'Invalid Amount' => 'مبلغ سند برگشتی، از مبلغ تراکنش
                    اصلی بیشتر است.',
                'Invalid Transaction' => 'درخواست برگشت یک تراکنش رسیده
                    است، در حالی که تراکنش اصلی پیدا نمی شود.',
                'Invalid Card Number' => 'شماره کارت اشتباه است.',
                'No Such Issuer' => 'چنین صادر کننده کارتی وجود ندارد.',
                'Expired Card Pick Up' => 'از تاریخ انقضای کارت گذشه است و
                    کارت دیگر معتبر نیست.',
                'Allowable PIN Tries Exceeded Pick Up' => 'رمز کارت )PIN )3 مرتبه اشتباه وارد
                    شده است در نتیجه کارت غیر فعال
                    خواهد شد.',
                'Incorrect PIN' => 'خریدار رمز کارت ) PIN )را اشتباه وارد
                    کرده است.',
                'Exceeds Withdrawal Amount Limit' => 'مبلغ بیش از سقف برداشت می باشد.',
                'ExceedsWithdrawalAmountLimit' => 'مبلغ بیش از سقف برداشت می باشد.',
                'Transaction Cannot Be Completed' => 'تراکنش Authorize شده است ) شماره
                    PIN وPAN درست هستند( ولی امکان
                    سند خوردن وجود ندارد.',
                'Response Received Too Late' => 'تراکنش در شبکه بامکی Timeout
                    خورده است.',
                'Suspected Fraud Pick Up' => 'خریدار یا فیلد CVV2 و یا فیلد
                    ExpDate را اشتباه زده است. ) یا اصلا
                    وارد نکرده است(',
                'No Sufficient Funds' => 'موجودی به اندازی کافی در حساب وجود
                    ندارد.',
                'Issuer Down Slm' => 'سیسم کارت بانک صادر کننده در
                    وضعیت عملیاتی نیست',
                'TME Error' => 'کلیه خطاهای دیگر بانکی باعث ایجاد خطا شده است. '
            ];
        return !empty($messages[$enMessage]) ? $messages[$enMessage] : 'خطای تعریف نشده.';
    }
}
