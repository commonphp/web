<?php

namespace CommonPHP\Tests\Web;

use CommonPHP\Web\Exceptions\UndefinedResponseStatusCodeException;
use PHPUnit\Framework\TestCase;
use CommonPHP\Web\Response;
use CommonPHP\Web\Support\ResponseStatus;


class ResponseTest extends TestCase
{
    /**
     * @test
     */
    public function itCanInitializeAnEmptyResponseInstance()
    {
        $response = new Response();

        $this->assertSame('', $response->body);
        $this->assertSame(202, $response->statusCode);
        $this->assertSame([], $response->headers);
    }

    /**
     * @test
     */
    public function itCanInitializeAResponseInstanceWithBody()
    {
        $body = 'Body content for the HTTP response';
        $response = new Response($body);

        $this->assertSame($body, $response->body);
        $this->assertSame(202, $response->statusCode);
        $this->assertSame([], $response->headers);
    }

    /**
     * @test
     */
    public function itCanInitializeAResponseInstanceWithResponseStatusEnum ()
    {
        $responseStatus = ResponseStatus::INFO_CONTINUE;
        $response = new Response('', $responseStatus);

        $this->assertSame('', $response->body);
        $this->assertSame($responseStatus->value, $response->statusCode);
        $this->assertSame([], $response->headers);
    }

    /**
     * @test
     */
    public function itCanInitializeAResponseInstanceWithValidStatusCode ()
    {
        $statusCode = 200; // status code for 'OK'
        $response = new Response('', $statusCode);

        $this->assertSame('', $response->body);
        $this->assertSame($statusCode, $response->statusCode);
        $this->assertSame([], $response->headers);
    }

    /**
     * @test
     */
    public function itThrowsExceptionWithInvalidStatusCode ()
    {
        $this->expectException(UndefinedResponseStatusCodeException::class);

        $statusCode = 999; // status code is not valid
        $response = new Response('', $statusCode);
    }

    /**
     * @test
     */
    public function itCanInitializeAResponseInstanceWithHeaders()
    {
        $headers = ['Content-Type' => 'application/json'];
        $response = new Response('', 202, $headers);

        $this->assertSame('', $response->body);
        $this->assertSame(202, $response->statusCode);
        $this->assertSame($headers, $response->headers);
    }
}
