<?php

namespace Plugins\GRecaptcha\Exceptions;

/**
 *
 */
class VerificationFailedException extends \RuntimeException
{
    /**
     * [lowScore description]
     * @return self
     */
    public static function lowScore(): self
    {
        return new self(
            'low-score'
        );
    }
}
