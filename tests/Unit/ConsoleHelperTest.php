<?php

use ErayAydin\Fingerprint\Console\ConsoleHelper;

it('formats success text', function () {
    $text = 'Success';
    $formattedText = ConsoleHelper::success($text);
    expect($formattedText)->toBe('<fg=green;options=bold>Success</>');
});

it('formats error text', function () {
    $text = 'Error';
    $formattedText = ConsoleHelper::error($text);
    expect($formattedText)->toBe('<fg=red;options=bold>Error</>');
});

it('formats warning text', function () {
    $text = 'Warning';
    $formattedText = ConsoleHelper::warning($text);
    expect($formattedText)->toBe('<fg=yellow;options=bold>Warning</>');
});

it('formats info text', function () {
    $text = 'Info';
    $formattedText = ConsoleHelper::info($text);
    expect($formattedText)->toBe('<fg=blue;options=bold>Info</>');
});

it('formats italic text with custom color', function () {
    $text = 'Italic';
    $formattedText = ConsoleHelper::colorText('cyan', $text, 'italic');
    expect($formattedText)->toBe('<fg=cyan;options=italic>Italic</>');
});
