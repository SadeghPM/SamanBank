<?php
/**
 * Saman bank payment package
 */

namespace SadeghPM\SamanBank;

class Saman
{
    const SAMAN_WSDL_URL = 'https://acquirer.samanepay.com/payments/referencepayment.asmx?WSDL';
    const SAMAN_SHAPARAK_PAGE_URL = 'https://sep.shaparak.ir/Payment.aspx';
    public $payParams = [];
    /**
     * @var SamanStorageAdapter
     */
    private $storageAdapter;

    public function __construct(SamanStorageAdapter $storageAdapter, int $merchantId)
    {
        $this->payParams['MID'] = $merchantId;
        $this->storageAdapter = $storageAdapter;
    }

    /**
     * @param int $amount in rial
     * @param string $redirectUrl redirect url after payment
     * @return Saman
     * @throws \Exception
     */
    public function payRequest(int $amount, string $redirectUrl)
    {
        if (!filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
            throw new \Exception('invalid redirect url!');
        }
        $this->payParams['Amount'] = $amount;
        $this->payParams['RedirectURL'] = $redirectUrl;
        $this->payParams['ResNum'] = md5(uniqid());
        $this->storageAdapter->savePay($this->payParams['ResNum'], $amount);
        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectScript()
    {
        $js_code = '<script>
	    				var form = document.createElement("form");
	                    form.setAttribute("method", "POST");
	                    form.setAttribute("action", "%s");
	                    form.setAttribute("target", "_self");';
        foreach ($this->payParams as $key => $value) {
            $js_code .= sprintf(
                'var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
	                    hiddenField.setAttribute("name", "%s");
	                    hiddenField.setAttribute("value", "%s");
	                    form.appendChild(hiddenField);
                        ', $key, $value);
        }
        $js_code .= 'document.body.appendChild(form);form.submit();</script>';
        return sprintf($js_code, self::SAMAN_SHAPARAK_PAGE_URL);
    }

    /**
     * check payment status
     *
     *
     * @param array $transactionResponse transaction callback data via post method
     * @return array
     * @throws SamanPayDoubleSpendingException if pay double spending
     * @throws SamanPayEmptyResponseException if empty callback data entered
     * @throws SamanPayException any pay error from saman bank
     * @throws SamanPayVerifyException
     * @throws \Exception if no soap extension installed
     */
    public function getPayStat(array $transactionResponse)
    {
        if ($this->isEmptyResponse($transactionResponse)) {
            throw new SamanPayEmptyResponseException;
        }
        //if Stat is not OK show error message and save in database
        if ($transactionResponse['State'] === "OK" and !empty($transactionResponse['RefNum'])) {
            //if double spending throw SamanPayDoubleSpendingException
            if ($this->storageAdapter->isDoubleSpending($transactionResponse['RefNum'])) {
                throw new SamanPayDoubleSpendingException;
            }
            $verify_state = $this->verify($transactionResponse['RefNum']);
            if ($verify_state > 0) {
                $this->storageAdapter->logSuccessfulPay($transactionResponse['ResNum'], $transactionResponse['RefNum'], $transactionResponse['State'], (int)$transactionResponse['StateCode']);
                return $transactionResponse;
            } else {
                $this->storageAdapter->logErrorPay($transactionResponse['ResNum'], $transactionResponse['RefNum'], $transactionResponse['State'], $verify_state);
                throw new SamanPayVerifyException($verify_state);
            }
        } else {
            $this->storageAdapter->logErrorPay($transactionResponse['ResNum'], $transactionResponse['RefNum'], $transactionResponse['State'], (int)$transactionResponse['StateCode']);
            throw new SamanPayException($transactionResponse['State']);
        }
    }

    public function isEmptyResponse(array $post = [])
    {
        return empty($post) or empty($post['ResNum']);
    }

    /**
     * @param $RefNum
     * @return int
     * @throws \Exception if no soap extension installed
     */
    public function verify($RefNum): int
    {
        if (!extension_loaded('soap')) {
            throw new  \Exception("No Soap extension installed!", 1);
        }
        $soapclient = new \SoapClient(self::SAMAN_WSDL_URL);
        $res = $soapclient->VerifyTransaction($RefNum, $this->payParams['MID']);#reference number and sellerId
        return (int)$res;
    }
}
