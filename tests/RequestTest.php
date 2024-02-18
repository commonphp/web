<?php

namespace CommonPHP\Tests\Web;

use CommonPHP\Web\Request;
use CommonPHP\Web\Support\RequestMethod;
use CommonPHP\Web\Support\RequestScheme;
use PHPUnit\Framework\TestCase;
use CommonPHP\Web\Exceptions\UndefinedRequestMethodException;
use CommonPHP\Web\Exceptions\UndefinedRequestSchemeException;

/**
 * This class contains the test cases for the Request class.
 *
 * Specifically, this class contains the test cases for the fromRequest()
 * method of the Request class.
 *
 */
class RequestTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testFromRequestOnCli()
    {
        $_SERVER = [
            'REQUEST_SCHEME' => 'HTTPS',
        ];
        $request = Request::fromRequest();
        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals(RequestMethod::GET, $request->method);
        $this->assertEquals(RequestScheme::HTTPS, $request->scheme);
        $this->assertEquals('127.0.0.1', $request->host);
    }

    /**
     * @runInSeparateProcess
     */
    public function testFromRequestOnUndefinedRequestMethod()
    {
        $_SERVER = [
            'REQUEST_SCHEME' => 'HTTPS',
            'REQUEST_METHOD' => 'INVALID',
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => '80',
            'REQUEST_URI' => '/',
            'SCRIPT_NAME' => '/',
        ];
        $this->expectException(UndefinedRequestMethodException::class);
        Request::fromRequest();
    }

    /**
     * @runInSeparateProcess
     */
    public function testFromRequestOnUndefinedRequestScheme()
    {
        $_SERVER = [
            'REQUEST_SCHEME' => 'INVALID',
            'REQUEST_METHOD' => 'GET',
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => '80',
            'REQUEST_URI' => '/',
            'SCRIPT_NAME' => '/',
        ];
        $this->expectException(UndefinedRequestSchemeException::class);
        Request::fromRequest();
    }

    /**
     * @runInSeparateProcess
     */
    public function testFromRequestOnValidRequest()
    {
        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => '80',
            'REQUEST_URI' => '/',
            'SCRIPT_NAME' => '/',
        ];
        $request = Request::fromRequest();
        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals(RequestMethod::GET, $request->method);
        $this->assertEquals(RequestScheme::HTTP, $request->scheme);
        $this->assertEquals('localhost', $request->host);
    }
}