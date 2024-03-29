<?php

namespace CreditPilot;

use CreditPilot\Responses\FindPayResponse;
use CreditPilot\Responses\PayResponse;
use CreditPilot\Responses\PhoneRangesResponse;
use CreditPilot\Responses\PrepareResponse;

class Gateway
{

    protected $url;
    protected $login;
    protected $password;
    protected $providers;

    protected $request;
    protected $response;

    public function __construct($url, $login, $password, array $providers)
    {
        $this->url = $url;
        $this->login = $login;
        $this->password = $password;
        $this->providers = $providers;
    }

    /**
     * Check if transfer can be performed
     *
     * @param int $id
     * @param $provider
     * @param string $destination phone or card number
     * @param float $amount
     * @return PrepareResponse
     * @throws Exceptions\HttpException
     */
    public function prepare($id, $provider, $destination, $amount)
    {
        $this->request = [
            'actionName' => 'PREPARE',
            'dealerTransactionId' => $id,
            'serviceProviderId' => $provider,
            'fullAmount' => $amount,
            'amount' => $amount,
            'phoneNumber' => $destination,
        ];

        $request = new Request;
        $response = $request
            ->withBaseUrl($this->url)
            ->withLogin($this->login)
            ->withPassword($this->password)
            ->withParams($this->request)
            ->send();

        return new PrepareResponse($response);
    }

    /**
     * Send transfer
     *
     * @param int $id
     * @param $provider
     * @param string $destination phone or card number
     * @param float $amount
     * @return PayResponse
     * @throws Exceptions\HttpException
     */
    public function pay($id, $provider, $destination, $amount)
    {
        $this->request = [
            'actionName' => 'PAY',
            'dealerTransactionId' => $id,
            'serviceProviderId' => $provider,
            'fullAmount' => $amount,
            'amount' => $amount,
            'phoneNumber' => $destination,
        ];

        $request = new Request;
        $response = $request
            ->withBaseUrl($this->url)
            ->withLogin($this->login)
            ->withPassword($this->password)
            ->withParams($this->request)
            ->send();

        return new PayResponse($response);
    }

    /**
     * Get transaction status performed by pay()
     *
     * @param string $id
     * @param bool $isBillNumber
     * @return FindPayResponse
     * @throws Exceptions\HttpException
     */
    public function findPay($id, $isBillNumber = true)
    {
        $this->request = [
            'actionName' => 'FINDPAY',
        ];

        if ($isBillNumber) {
            $this->request['billNumber'] = $id;
        } else {
            $this->request['dealerTransactionId'] = $id;
        }

        $request = new Request;
        $response = $request
            ->withBaseUrl($this->url)
            ->withLogin($this->login)
            ->withPassword($this->password)
            ->withParams($this->request)
            ->send();

        return new FindPayResponse($response);
    }

    /**
     * Get information about phone number
     *
     * @param string $phoneNumber
     * @return PhoneRangesResponse
     * @throws Exceptions\HttpException
     */
    public function phoneRanges($phoneNumber)
    {
        $this->request = [
            'actionName' => 'PHONERANGES',
            'phoneNumber' => $phoneNumber,
        ];

        $request = new Request;
        $response = $request
            ->withBaseUrl($this->url)
            ->withLogin($this->login)
            ->withPassword($this->password)
            ->withParams($this->request)
            ->send();

        return new PhoneRangesResponse($response);
    }

    public function request()
    {
        return $this->request;
    }

}
