<?php

namespace Test;

use Kovagoz\Http\HttpResponder;
use Nyholm\Psr7\Factory\Psr17Factory;

class HttpResponderTest extends \PHPUnit\Framework\TestCase
{
    private HttpResponder $responder;

    public function setUp(): void
    {
        $responseFactory = new Psr17Factory();
        $streamFactory   = new Psr17Factory();

        $this->responder = new HttpResponder($responseFactory, $streamFactory);
    }

    public function testReplyWithNoContent(): void
    {
        $response = $this->responder->reply();

        self::assertEquals(204, $response->getStatusCode());
    }

    public function testReplyWithHtml(): void
    {
        $response = $this->responder->reply('hello');

        self::assertEquals(200, $response->getStatusCode());
        self::assertTrue($response->hasHeader('Content-Type'));
        self::assertEquals('text/html', $response->getHeaderLine('Content-Type'));
        self::assertEquals('hello', (string) $response->getBody());
    }

    public function testReplyWithJson(): void
    {
        $response = $this->responder->reply(['foo' => 'bar']);

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));

        $json = (string) $response->getBody();

        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals('bar', $data['foo']);
    }

    public function testReplyWithResponseObject(): void
    {
        $response = (new Psr17Factory())->createResponse(201);

        self::assertSame($response, $this->responder->reply($response));
    }

    public function testReplyWithUnknownType(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->responder->reply(new \stdClass());
    }

    public function testRedirectWithStringUri(): void
    {
        $response = $this->responder->redirect('https://example.com');

        self::assertEquals(302, $response->getStatusCode());
        self::assertTrue($response->hasHeader('Location'));
        self::assertEquals('https://example.com', $response->getHeaderLine('Location'));
    }

    public function testRedirectWithUriObject(): void
    {
        $uri = (new Psr17Factory())->createUri('https://example.com');

        $response = $this->responder->redirect($uri);

        self::assertEquals('https://example.com', $response->getHeaderLine('Location'));
    }
}
