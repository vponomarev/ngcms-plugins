<?php

namespace Plugins\GRecaptcha;

// Исключения.
use Exception;
use RuntimeException;
use Throwable;
use Plugins\GRecaptcha\Exceptions\MissingVariableException;
use Plugins\GRecaptcha\Exceptions\VerificationFailedException;

// Базовые расширения PHP.
use stdClass;

// Сторонние зависимости.
use Plugins\GRecaptcha\Filters\GRecaptchaCoreFilter;
use Plugins\GRecaptcha\Filters\GRecaptchaCommentsFilter;
use Plugins\GRecaptcha\Filters\GRecaptchaFeedbackFilter;
use Plugins\Traits\Renderable;

// Используем функции из пространства `Plugins`.
use function Plugins\dd;
use function Plugins\setting;
use function Plugins\trans;

/**
 * Защита форм сайта от интернет-ботов с Google reCAPTCHA v3.
 */
class GRecaptcha
{
    use Renderable;

    /**
     * Номер версии плагина.
     * @const string
     */
    const VERSION = '0.7.2';

    /**
     * Идентификатор плагина.
     * @var string
     */
    protected $plugin = 'ng-grecaptcha';

    /**
     * URL-адрес сервиса `создания` токена пользователя.
     * @var string
     */
    protected $apiRender = 'https://www.google.com/recaptcha/api.js?render=';

    /**
     * URL-адрес сервиса `проверки` токена пользователя.
     * @var string
     */
    protected $apiVerify = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Ключ сайта, используемый для `создания` токена пользователя.
     * @var string
     */
    protected $siteKey;

    /**
     * Секретный ключ, используемый для `проверки` токена пользователя.
     * @var string
     */
    protected $secretKey;

    /**
     * Нижний порог оценки действий пользователя.
     * @var float
     */
    protected $score;

    /**
     * Значение поля капчи из формы, переданное в методе `validate`.
     * @var string|null
     */
    protected $userToken;

    /**
     * Имена всех шаблонов плагина.
     * @var array
     */
    protected $templates = [
        'google_v3-input',
        'google_v3-script',

    ];

    /**
     * Маркер того, что уже был прикреплен JavaScript-перехватчик форм.
     * @var bool
     */
    protected $attachedJavascript;

    /**
     * Сообщение о причине отказа выполнения действия.
     * @var array
     */
    protected $rejectionReason = 'Google reCAPTCHA protected.';

    /**
     * Создать экземпляр плагина.
     */
    public function __construct(array $params = [])
    {
        $this->configure($params);
    }

    /**
     * Получить номер версии плагина.
     * @return string
     */
    public function version(): string
    {
        return self::VERSION;
    }

    /**
     * Конфигурирование параметров плагина.
     * @param  array  $params
     * @return $this
     */
    public function configure(array $params = []): self
    {
        // Сначала зададим настройки из плагина.
        $this->siteKey = trim(secure_html(setting($this->plugin, 'site_key', null)));
        $this->secretKey = trim(secure_html(setting($this->plugin, 'secret_key', null)));
        $this->score = (float) setting($this->plugin, 'score', 0.5);

        // Теперь зададим переданные через форму.
        $this->userToken = trim(secure_html($_POST['g-recaptcha-response'])) ?? null;

        // Определить все пути к шаблонам.
        $this->defineTemplatePaths(
            (bool) setting($this->plugin, 'localsource', 0)
        );

        return $this;
    }

    /**
     * Добавление JavaScript API в переменную `htmlvars`.
     * @return void
     */
    public function registerAPIJavaScript(): void
    {
        if (
            $this->siteKey
            && setting($this->plugin, 'use_api_js', true)
        ) {
            register_htmlvar('js', $this->apiRender.$this->siteKey);
        }
    }

    /**
     * Добавление JavaScript из шаблона в переменную `htmlvars`.
     * @param  string  $action  Действие, выполняемое пользователем.
     * @return void
     */
    public function registerAttachJavaScript(string $action = 'send_form'): void
    {
        // Если включено формирование переменной `htmlvars`.
        if (
            $this->siteKey
            && setting($this->plugin, 'use_attach_js', true)
            && ! $this->attachedJavascript
        ) {
            register_htmlvar('plain', $this->view('google_v3-script', [
                'api_render' => $this->apiRender,
                'site_key' => $this->siteKey,
                'action' => $action,

            ]));

            $this->attachedJavascript = true;
        }
    }

    public function verifying()
    {
        try {
            $this->ensureNecessaryVariables();

            $verified = $this->touchAnswer();

            if (! $verified->success) {
                throw new VerificationFailedException(
                    array_shift($verified->{'error-codes'})
                );
            }

            if ($verified->score < $this->score) {
                throw VerificationFailedException::lowScore();
            }

            return true;

        } catch (MissingVariableException $e) {
            $this->rejectionReason = $e->getMessage();

            return false;
        } catch (VerificationFailedException $e) {
            $this->rejectionReason = $e->getMessage();

            return false;
        } catch (Throwable $e) {

            throw $e;
        }
    }

    protected function touchAnswer(): stdClass
    {
        $query = $this->prepareQuery();

        if (extension_loaded('curl') and function_exists('curl_init')) {
            $answer = $this->getCurlAnswer($query);
        } elseif (ini_get('allow_url_fopen')) {
            $answer = $this->getFopenAnswer($query);
        } else {
            throw new RuntimeException(
                'Not supported: cURL, allow_fopen_url.'
            );
        }

        $answer = json_decode($answer);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new RuntimeException('JSON answer error.');
        }

        return $answer;
    }

    protected function prepareQuery()
    {
        return http_build_query([
            'secret' => $this->secretKey,
            'response' => $this->userToken
        ]);
    }

    protected function getCurlAnswer(string $query)
    {
        $ch = curl_init();
        if (curl_errno($ch) != 0) {
            throw new RuntimeException(
                'err_curl_'.curl_errno($ch).' '.curl_error($ch)
            );
        }
        curl_setopt($ch, CURLOPT_URL, $this->apiVerify);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $answer = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (404 === $status) {
            throw new RuntimeException(
                'Source file not found.'
            );
        } elseif ($status !== 200) {
            throw new RuntimeException(
                'err_curl_'.$status
            );
        }

        curl_close($ch);

        return $answer;
    }

    protected function getFopenAnswer(string $query)
    {
        return file_get_contents(
            $this->apiVerify.'?'.$query
        );
    }

    /**
     * Получить сообщение о причине отказа выполнения действия.
     * @return string
     */
    public function rejectionReason(): string
    {
        return trans(
            $this->plugin.':error.'.$this->rejectionReason
        );
    }

    protected function ensureNecessaryVariables()
    {
        if (empty($this->siteKey)) {
            throw MissingVariableException::siteKey();
        }

        if (empty($this->secretKey)) {
            throw MissingVariableException::secretKey();
        }

        if (empty($this->userToken)) {
            throw MissingVariableException::inputResponse();
        }
    }
}
