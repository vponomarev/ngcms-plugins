<?php

namespace Plugins;

// Исключения.
use RuntimeException;

// Базовые расширения PHP.
use stdClass;

/**
 * RSS поток для Yandex Turbo.
 */
class TurboYandex
{
    /**
     * Номер версии плагина.
     * @const string
     */
    const VERSION = '0.6.0';

    /**
     * Идентификатор плагина.
     * @var string
     */
    protected $plugin = 'turbo_yandex';

    /**
     * Максимальное количество элементов в каждой ленте.
     * @var int
     */
    protected $countItems = 200;

    /**
     * Извлекать URL-адреса изображений из текста новости.
     * @var bool
     */
    protected $extractImages;

    /**
     * Текущая страница ленты.
     * @var int
     */
    protected $page = 1;

    /**
     * Текущая категория страницы ленты.
     * @var stdClass
     */
    protected $category;

    /**
     * Расположение шаблонов плагина.
     *  - `0` - шаблон сайта;
     *  - `1` - директория плагина.
     * @var int
     */
    protected $localsource;

    /**
     * Имена всех шаблонов плагина.
     * @var array
     */
    protected $templates = [
        'channel',
        'entries',

    ];

    /**
     * Список файлов шаблонов с полными путями, исключая имя шаблона.
     * @var array
     */
    protected $templatePath = [];

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
    public function version()
    {
        return self::VERSION;
    }

    protected function configure(array $params)
    {
        // Сначала зададим настройки из плагина.
        $this->countItems = setting($this->plugin, 'countItems', 200);
        $this->extractImages = setting($this->plugin, 'extractImages', false);
        $this->localsource = setting($this->plugin, 'localsource', 0);
        $this->templatePath = $this->findTemplates($this->localsource);

        // Теперь зададим переданные через URL-адрес настройки.
        if (isset($params['page']) && is_numeric($params['page'])) {
            $this->page = (int) $params['page'];
        }

        $categories = catz();

        if (isset($params['category']) && array_key_exists($params['category'], $categories)) {
            $this->category = (object) $categories[$params['category']];
        }
    }

    protected function cacheFilename()
    {
        $cacheFilename = $this->plugin;
        $cacheFilename .= config('theme', 'default');
        $cacheFilename .= config('home_url', home);
        $cacheFilename .= config('default_lang', 'ru');
        $cacheFilename .= $this->countItems;
        $cacheFilename .= $this->extractImages;
        $cacheFilename .= $this->localsource;
        $cacheFilename .= $this->page;

        if ($this->category instanceof stdClass) {
            $cacheFilename .= $this->category->id;
        }

        return md5($cacheFilename).'.txt';
    }

    public function generate()
    {
        $entries = $this->fetchNewsList();

        return $this->render([
            'link' => config('home_url', home),
            'title' => config('home_title', engineName),
            'description' => config('description', null),
            'language' => config('default_lang', 'ru'),
            'entries' => $entries,

        ]);
    }

    public function cachedContent()
    {
        return cacheRemember($this->plugin, $this->cacheFilename(), function () {
            return $this->generate();
        });
    }

    protected function stripTags(string $content)
    {
        return strip_tags(
            $content,
            '<p><figure><img><iframe><br><ul><ol><li><b><strong><i><em><sup><sub><ins><del><small><big><pre></pre><abbr><u><a>'
        );
    }

    public function render(array $vars)
    {
        return view($this->template('channel'), $vars);
    }

    /**
     * Извлечь список новостей из базы данных с дополнительной обработкой результата.
     * @return object
     */
    protected function fetchNewsList()
    {
        $showResult = news_showlist([], [], [
    		'plugin' => $this->plugin,
    		'extractEmbeddedItems' => $this->extractImages,
    		'extendedReturn' => true,
    		'extendedReturnData' => true,
    		'twig' => true,
    		'overrideSQLquery' => $this->overrideSQLquery(),
    		'overrideTemplatePath' => $this->templatePath('entries'),
    		'overrideTemplateName' => 'entries',

    	]);

        return $showResult['data'];
    }

    protected function overrideSQLquery()
    {
        $where = "where approve = 1";

        if ($this->category instanceof stdClass) {
            $catid = (int) $this->category->id;

            $where .= " AND `catid` REGEXP '[[:<:]](".$catid.")[[:>:]]'";
        }

        $start = ($this->page - 1) * $this->countItems;

        return "select * from ".prefix."_news ".$where." order by id asc limit ".$start.",".$this->countItems;
    }

    /**
     * Определить все пути к файлам шаблонов.
     * @return array
     */
    protected function findTemplates(string $localsource, string $skin = null)
    {
        if (is_null($skin)) {
            return locatePluginTemplates($this->templates, $this->plugin, $localsource);
        }

        return locatePluginTemplates($this->templates, $this->plugin, $localsource, $skin);
    }

    /**
     * Получить путь к файлу шаблона.
     * @param  string  $tpl
     * @return string
     */
    protected function templatePath(string $tpl)
    {
        if (empty($path = $this->templatePath[$tpl])) {
            throw new RuntimeException("Template [{$tpl}] is not define.");
        }

        return $path;
    }

    /**
     * Получить полный путь к файлу шаблона, включая имя шаблона.
     * @param  string  $tpl
     * @return string
     */
    protected function template(string $tpl)
    {
        $file = ('url:' == substr($tpl, 0, 4)) ? '/'.substr($tpl, 5) : ($tpl . '.tpl');

        return $this->templatePath($tpl).$file;
    }
}
