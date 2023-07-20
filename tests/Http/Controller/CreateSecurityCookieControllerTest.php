<?php

it('create security cookie', function () {
    $response = $this->get('_native/api/cookie')->assertRedirect('/');
    $cookie = $response->headers->getCookies()[0];

    $this->assertEquals('_php_native', $cookie->getName());
    $this->assertEquals('localhost', $cookie->getDomain());
    $this->assertTrue($cookie->isHttpOnly());
});
