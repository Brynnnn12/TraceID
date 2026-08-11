<?php

it('redirects the root path to the public verification page', function () {
    $response = $this->get('/');

    $response->assertRedirect('/verify');
});
