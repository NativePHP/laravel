<?php

it('create security cookie', function () {
    $response = $this->get('_native/api/cookie')->assertRedirect('/');
    $cookie = $response->headers->getCookies()[0];

    $this->assertEquals('_php_native', $cookie->getName());
    $this->assertEquals('localhost', $cookie->getDomain());
    $this->assertTrue($cookie->isHttpOnly());
});

it('check if secret is not equal of config secret key abort 403 page', function () {
    config()->set('native-php.secret', 'milwad');

    $response = $this->get('_native/api/cookie')->assertStatus(403);

    $this->assertEquals([], $response->headers->getCookies());
});
