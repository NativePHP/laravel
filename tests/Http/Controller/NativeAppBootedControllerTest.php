<?php

use Illuminate\Support\Facades\Event;
use Native\Laravel\Events\App\ApplicationBooted;

class TestProvider {
    public function boot() {

    }
}

it('boots the NativePHP provider', function () {
    Event::fake();

    config([
        'nativephp.provider' => TestProvider::class
    ]);

    $this->withoutMiddleware()
        ->post('_native/api/booted')
        ->assertOk();

    Event::assertDispatched(ApplicationBooted::class);
});
