<?php

use ErayAydin\Fingerprint\Exceptions\BotDetectedException;

it('throws an exception when bot detected', function () {
    $exception = BotDetectedException::botDetected();

    expect($exception)->toBeInstanceOf(BotDetectedException::class)
        ->and($exception->getMessage())->toBe('Bot detected.');
});

it('throws an exception when bad bot detected', function () {
    $exception = BotDetectedException::badBotDetected();

    expect($exception)->toBeInstanceOf(BotDetectedException::class)
        ->and($exception->getMessage())->toBe('Bad bot detected.');
});
