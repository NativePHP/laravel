<?php

test('test system', function () {
    $system = \Native\Laravel\Facades\System::printers();

    expect($system)
        ->toBeArray()
        ->and($system)
        ->toBeEmpty();
});
