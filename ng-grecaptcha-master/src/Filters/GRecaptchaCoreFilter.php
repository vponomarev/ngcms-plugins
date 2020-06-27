<?php

namespace Plugins\GRecaptcha\Filters;

// Сторонние зависимости.
use CoreFilter;
use Plugins\GRecaptcha\GRecaptcha;

class GRecaptchaCoreFilter extends CoreFilter
{
    /**
     * [protected description]
     * @var GRecaptcha
     */
    protected $recaptcha;

    public function __construct(GRecaptcha $recaptcha)
    {
        $this->recaptcha = $recaptcha;
    }

    public function registerUserForm(&$tvars)
    {

    }

    public function registerUser($params, &$msg)
    {
        if (! $this->recaptcha->verifying()) {
            $msg = $this->recaptcha->rejectionReason();

            return false;
        }

        return true;
    }
}
