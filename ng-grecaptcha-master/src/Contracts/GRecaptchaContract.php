<?php

namespace Plugins\Contracts;

// Сторонние зависимости.
use Illuminate\Contracts\Validation\Validator as ValidatorContract;

/**
 * Контракт публичных методов валидатора капчи.
 * @var interface
 */
interface GRecaptchaContract
{
    /**
     * Получить проанализированное HTML строковое представление
     * поля для ввода капчи пользователем.
     * @param  string  $view
     * @return string|null
     */
    public function input(string $view): ?string;

    /**
     * Получить проанализированное HTML строковое представление
     * JavaScript'ов капчи.
     * @param  string  $view
     * @return string|null
     */
    public function script(string $view): ?string;

    /**
     * Выполнить валидацию капчи (токена), полученого из формы от пользователя.
     * @param  string  $attribute
     * @param  string|null  $userToken
     * @param  array  $parameters
     * @param  ValidatorContract  $validator
     * @return bool
     */
    public function validate(
        string $attribute,
        string $userToken = null,
        array $parameters = [],
        ValidatorContract $validator
    ): bool;
}
