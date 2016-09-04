<?php

namespace NotificationChannels\Zenvia;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use NotificationChannels\Zenvia\Exceptions\CouldNotSendNotification;

class Zenvia
{
    /** @var HttpClient HTTP Client */
    protected $http;

    /** @var null|string conta do zenvia. */
    protected $conta = null;

    /** @var null|string senha do zenvia. */
    protected $senha = null;

    /** @var null|string from do zenvia. */
    protected $from = null;

    /**
     * @param null $conta
     * @param null $senha
     * @param null $from
     */
    public function __construct($conta = null, $senha = null, $from = null)
    {
        $this->conta = $conta;
        $this->senha = $senha;
        $this->from  = $from;
    }

    /**
     * Get HttpClient.
     *
     * @return HttpClient
     */
    protected function httpClient()
    {
        return new HttpClient([
            'base_uri' => 'https://api-rest.zenvia360.com.br',
            'headers' => [
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => 'Basic ' . base64_encode(config('services.zenvia.conta') . ':' . config('services.zenvia.senha'))
            ]
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function sendMessage($to, $params)
    {
        if (empty($to)) {
            throw CouldNotSendNotification::receiverNotProvided();
        }

        if (empty($this->conta)) {
            throw CouldNotSendNotification::contaNotProvided();
        }

        if (empty($this->senha)) {
            throw CouldNotSendNotification::senhaNotProvided();
        }

        try {
            data = [
                'sendSmsRequest' => [
                    'from' => $this->from,
                    'to'   => $to,
                    'msg'  => $this->msg,
                    'id'   => $this->id,
                ],
            ];

            return $this->httpClient()->post('/services/send-sms', ['json' => $data]);
        } catch (ClientException $exception) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($exception);
        } catch (\Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithZenvia();
        }
    }
}