<?php

use ErayAydin\Fingerprint\Exceptions\MinConfidenceScoreException;

it('throws an exception when confidence score is lower than required', function () {
    $confidenceScore = 0.5;
    $minConfidenceScore = 0.7;
    $exception = MinConfidenceScoreException::minConfidenceScoreNotReached($confidenceScore, $minConfidenceScore);

    expect($exception)->toBeInstanceOf(MinConfidenceScoreException::class)
        ->and($exception->getMessage())->toBe("Confidence score $confidenceScore is lower than required $minConfidenceScore score");
});
