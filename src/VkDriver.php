<?php

namespace Botman\Drivers\Vk;

use BotMan\BotMan\Drivers\HttpDriver;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class VkDriver extends HttpDriver
{
    const DRIVER_NAME = 'Vk';
    const API_URL = 'https://api.vk.com/method/';

    protected $messages = [];

    public function buildPayload(Request $request)
    {
        $this->payload = new ParameterBag(json_decode($request->getContent(), true) ?? []);
        $this->event = Collection::make($this->payload->all());
        $this->config = Collection::make($this->config->get('vk', []));
    }

    public function buildServicePayload($message, $matchingMessage, $additionalParameters = [])
    {
        //TODO: Implement buttons and attachments features.
        $payload = [
            'peer_id' => $matchingMessage->getSender(),
        ];

        if ($message instanceof Question) {
            $payload['message'] = $message->getText();
        } elseif ($message instanceof OutgoingMessage) {
            $payload['message'] = $message->getText();
        } else {
            $payload['message'] = $message;
        }

        return $payload;
    }

    public function getConversationAnswer(IncomingMessage $message)
    {
        // TODO: Implement interactive features.
        return Answer::create($message->getText())->setMessage($message);
    }

    public function getMessages()
    {
        if (empty($this->messages)) {
            $this->loadMessages();
        }
        return $this->messages;
    }

    public function getUser(IncomingMessage $matchingMessage)
    {
        // TODO: Implement getUser() method.
    }

    public function isConfigured()
    {
        // TODO: Implement isConfigured() method.
        return true;
    }

    protected function loadMessages()
    {
        $message = $this->event->get('object');

        $this->messages = [new IncomingMessage($message['text'], $message['from_id'], $message['peer_id'], $this->event->toArray())];
    }

    public function matchesRequest()
    {
        // TODO: Implement matchesRequest() method.
        return true;
    }

    public function sendPayload($payload)
    {
        return $this->sendRequest('messages.send', $payload, new IncomingMessage('', '', ''));
    }

    public function sendRequest($endpoint, array $parameters, IncomingMessage $matchingMessage)
    {
        $parameters['access_token'] = $this->config->get('access_token');
        $parameters['v'] = $this->config->get('api_version');
        $parameters['lang'] = $this->config->get('lang');
        return $this->http->post(self::API_URL . $endpoint, [], $parameters);
    }
}