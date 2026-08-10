<?php

test('registration screen is not available', function () {
    $this->get('/register')->assertNotFound();
});

test('registration is disabled (no public signup)', function () {
    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();

    $this->assertGuest();
});
