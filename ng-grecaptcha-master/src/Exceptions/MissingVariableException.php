<?php

namespace Plugins\GRecaptcha\Exceptions;

/**
 *
 */
class MissingVariableException extends \InvalidArgumentException
{
    /**
     * [siteKey description]
     * @return self
     */
    public static function siteKey(): self
    {
        return new self(
            'empty-site-key'
        );
    }

    /**
     * [secretKey description]
     * @return self
     */
    public static function secretKey(): self
    {
        return new self(
            'empty-secret-key'
        );
    }

    /**
     * [inputResponse description]
     * @return self
     */
    public static function inputResponse(): self
    {
        return new self(
            'missing-input-response'
        );
    }
}
