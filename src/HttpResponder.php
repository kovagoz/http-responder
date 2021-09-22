<?php

namespace Kovagoz\Http;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriInterface;

class HttpResponder
{
    private ResponseFactoryInterface $responseFactory;
    private StreamFactoryInterface   $streamFactory;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory
    ) {
        $this->responseFactory = $responseFactory;
        $this->streamFactory   = $streamFactory;
    }

    /**
     * Create PSR response from various type of messages
     *
     * @param mixed $message
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \JsonException
     */
    public function reply(mixed $message = null): ResponseInterface
    {
        if ($message instanceof ResponseInterface) {
            return $message;
        }

        $response = $this->responseFactory
            ->createResponse()
            ->withHeader('Content-Type', 'text/html');

        if ($message === null) {
            return $response->withStatus(204);
        }

        if (is_array($message)) {
            $message  = json_encode($message, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
            $response = $response->withHeader('Content-Type', 'application/json');
        }

        if (is_string($message)) {
            return $response->withBody(
                $this->streamFactory->createStream($message)
            );
        }

        throw new \InvalidArgumentException();
    }

    /**
     * Redirect client to another location
     *
     * @param string|\Psr\Http\Message\UriInterface $url
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function redirect(string|UriInterface $url): ResponseInterface
    {
        return $this->reply()
            ->withStatus(302)
            ->withHeader('Location', (string) $url);
    }
}
