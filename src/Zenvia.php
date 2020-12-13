<?php

namespace NotificationChannels\Zenvia;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Str;
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

    /** @var null|string from do zenvia. */
    protected $pretend = null;

    /** @var null|string from do zenvia. */
    protected $aggregateId = null;

    /**
     * @param null $conta
     * @param null $senha
     * @param null $from
     * @param false $pretend
     * @param null $aggregateId
     */
    public function __construct($conta = null, $senha = null, $from = null, $pretend = false, $aggregateId = null)
    {
        $this->conta        = $conta;
        $this->senha        = $senha;
        $this->from         = $from;
        $this->pretend      = $pretend;
        $this->aggregateId  = $aggregateId;
    }

    /**
     * Get HttpClient.
     *
     * @return HttpClient
     */
    protected function httpClient()
    {
        return new HttpClient([
            'base_uri' => 'https://api.zenvia.com',
            'headers' => [
                'Content-Type'  => 'application/json',
                'X-API-TOKEN'   => config('services.zenvia.token')
            ]
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sendMessage($to, $params)
    {
//        if (empty($to)) {
//            throw CouldNotSendNotification::receiverNotProvided();
//        }
//
//        if (empty($this->conta)) {
//            throw CouldNotSendNotification::contaNotProvided();
//        }
//
//        if (empty($this->senha)) {
//            throw CouldNotSendNotification::senhaNotProvided();
//        }
//        if(empty($this->aggregateId)){
//            throw CouldNotSendNotification::aggregateIdNotProvided();
//        }

        try {

            $data = [
                'from' => config('services.zenvia.from'),
                'to' => $to,
                'contents' => [
                    0 => [
                        'type' => 'text',
                        'text' => $params['msg'],
                    ],
                ],
            ];

            $client = new HttpClient([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-API-TOKEN' => config('services.zenvia.token')
                ]
            ]);

            $response = $client->post('https://api.zenvia.com/v2/channels/sms/messages',
                [
                    'body' => json_encode($data)
                ]
            );

            return $response;
        } catch (ClientException $exception) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($exception);
        } catch (\Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithZenvia($exception->getMessage());
        }
    }

    protected function msg($params)
    {
        if ($params['from']) {
            return $params['from'] . ': ' . $params['msg'];
        }

        return $this->from . ': ' . $params['msg'];
    }
}
