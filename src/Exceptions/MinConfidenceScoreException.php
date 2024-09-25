<?php

namespace ErayAydin\Fingerprint\Exceptions;

final class MinConfidenceScoreException extends FingerprintException
{
    public static function minConfidenceScoreNotReached(float $confidenceScore, float $minConfidenceScore): self
    {
        return new self("Confidence score $confidenceScore is lower than required $minConfidenceScore score");
    }
}
