<?php

namespace Plugins\GRecaptcha\Filters;

// Сторонние зависимости.
use FilterComments;
use Plugins\GRecaptcha\GRecaptcha;
use function Plugins\dd;

class GRecaptchaCommentsFilter extends FilterComments
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

    public function addCommentsForm($newsID, &$tvars)
    {

    }

    public function addComments($userRec, $newsRec, &$tvars, &$SQL)
    {
        if (! $this->recaptcha->verifying()) {
            return [
        		'errorText' => $this->recaptcha->rejectionReason(),

        	];
        }

        return true;
    }
}
