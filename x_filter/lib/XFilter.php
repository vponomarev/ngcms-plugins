<?php

namespace Plugins;

use \LogicException;

/**
 *
 */
final class XFilter
{
    /**
     * The current globally available plugin (if any).
     *
     * @var static
     */
    private static $instance;

    /**
     * The XFilter plugin version.
     *
     * @var string
     */
    const VERSION = '0.1.0';

    /**
     * Name of plugin.
     *
     * @var string
     */
    protected $plugin = 'x_filter';

    /**
     * All available x_fields from config file.
     * .
     * @var array
     */
    protected $x_fields = [];

    /**
     * All requested inputs to filter from global $_REQUEST.
     *
     * @var null|array
     */
    protected $requested = [];

    /**
     * SQL query filter.
     *
     * @var array
     */
    protected $filter = [
        'AND'
    ];

    /**
     * Includes all requested parameters.
     *
     * @var string
     */
    protected $formAction;

    /**
     * Rendered category list.
     *
     * @var string
     */
    protected $categoryList;

    /**
     * Default parameters to plugin.
     *
     * @var array
     */
    protected $params = [
        'order' => 'id_desc',
        'showNumber' => 8,
        'skipcat' => null,
        'showAllCat' => false,

        'localsource' => 1,

        'use_css' => false,
        'use_js' => false,
        'canonical' => false,
        'meta_robots' => false,

        'cache' => false,
        'cacheExpire' => 60,
    ];

    /**
     * All the allowed types of the sort of news.
     *
     * @var array
     */
    protected $orderAllowed = [
        'id_desc' => 'id desc',
        'id_asc' => 'id asc',
        'postdate_desc' => 'postdate desc',
        'postdate_asc' => 'postdate asc',
        'title_desc' => 'title desc',
        'title_asc' => 'title asc',
    ];

    /**
     * Define all names to templates.
     *
     * @var array
     */
    protected $templates = [
        'filter_form',
        'search_form',
        ':x_filter.css',
        ':x_filter.js',
    ];

    /**
     * Rendered news list.
     *
     * @var object|null
     */
    protected $newsList = null;

    /**
     * Gets the instance via lazy initialization (created on first usage).
     *
     * @return XFilter
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from XFilter::getInstance() instead
     *
     * @return void
     */
    private function __construct()
    {
        include_once root.'includes/news.php';

        $this->loadParams();
        $this->registerXFields(
            $this->loadXFieldsConfig()
        );
        $this->registerRequested(
            $this->loadRequested()
        );
        $this->prepareFilter();

        $this->formAction = generatePageLink($this->paginationParams(), 1);
        $this->categoryList = $this->makeCategoryList();
    }

    /**
     * Get the version number of the plugin.
     *
     * @return string
     */
    public function version()
    {
        return static::VERSION;
    }

    /**
      * Fill plugin parameters.
      *
      * @return void
      */
    protected function loadParams()
    {
        foreach ($this->params as $param => $default) {
            $this->params[$param] = config($this->plugin, $param, $default);
        }

        $this->params = array_merge($this->params, [
            'page' => (int) request('page', 1),
            'search' => empty($s = trim(secure_html(request('s')))) ? null : $s,
            'category' => (int) request('catid', $this->getNewsCategory()),
            'order' => isset($this->orderAllowed[$order = request('order', 'id_desc')]) ? $order : 'id_desc',

            'skippedCategories' => $this->loadSkippedCategories(),
            'pluginLink' => generatePluginLink($this->plugin, null),
            'templatePath' => $this->findTemplates($this->localsource),
        ]);
    }

    protected function getNewsCategory()
    {
        return is_array($catid = getCurrentNewsCategory()) ? $catid[1] : null;
    }

    /**
     * [description]
     *
     * @return array Array id's of categories.
     */
    protected function loadSkippedCategories()
    {
        if (is_null($this->skipcat)) {
            return [];
        }

        $skipcat = array_map(function ($cat) {
            return (int) $cat;
        }, preg_split('/,/', $this->skipcat, -1, PREG_SPLIT_NO_EMPTY));

        // Get all categories which have `id` parent or `id` in skip list.
        $categories = array_filter(catz(), function($data, $cat) use ($skipcat) {
            return in_array($data['parent'], $skipcat) or in_array($data['id'], $skipcat);
        }, ARRAY_FILTER_USE_BOTH);

        return array_map(function ($cat) {
            return (int) $cat['id'];
        }, $categories);
    }

    /**
     * Load XFields config.
     *
     * @return array
     */
    protected function loadXFieldsConfig()
    {
        $xarray = xf_configLoad();

        return array_filter($xarray['news'], function($v, $k) {
               return $v['storage'] == 1;
           }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Load all of the requested inputs.
     *
     * @return array
     */
    protected function loadRequested()
    {
        return array_map(
            function ($input) {
                return secure_html($input);
            },
            array_filter(
                request(), function($v, $k) {
                    return !empty($v) and 'x_' === substr($k, 0, 2);
                }, ARRAY_FILTER_USE_BOTH
            )
        );
    }

    /**
     * Register all xfields.
     *
     * @return void
     */
    protected function registerXFields(array $x_fields)
    {
        $this->x_fields = $x_fields;
    }

    /**
     * Register all of the requested inputs.
     *
     * @return void
     */
    protected function registerRequested(array $inputs)
    {
        $this->requested = $inputs;
    }

    /**
     * Prepare filter.
     *
     * @return void
     */
    protected function prepareFilter()
    {
        // If category is selected, then check it, use 'like' to check additional categories too.
        if ($this->category and ! in_array($this->category, $this->skippedCategories)) {
            $this->addToFilter('DATA', 'category', '=', $this->category);
        }

        // Generate the list of categories excluding 'skipcat' list.
        foreach ($this->skippedCategories as $skip) {
            $this->addToFilter('SQL', "`catid` NOT REGEXP '[[:<:]](".$skip.")[[:>:]]'");
        }

        foreach ($this->requested as $k => $v) {
            $this->addToFilter('DATA', str_ireplace('x_', 'xfields_', $k, strlen('x_')), '=', $v); // !!! "xfields_$id
        }

        if ($s = $this->search) {
            $this->addToFilter('SQL', "MATCH (title, content) AGAINST('$s' IN BOOLEAN MODE)");
        }
    }

    /**
     * Find paths to template files.
     *
     * @return array
     */
    protected function findTemplates(string $localsource)
    {
        return LocatePluginTemplates($this->templates, $this->plugin, $localsource);
    }

    /**
     * Get the path to template file.
     *
     * @param  string  $tpl
     * @return string
     */
    protected function templatePath(string $tpl)
    {
        if (empty($path = $this->templatePath[$tpl])) {
            throw new \LogicException("Template $tpl is not define.");
        }

        return $path;
    }

    /**
     * Get the full path to the template file, including its name.
     *
     * @param  string  $tpl
     * @return string
     */
    protected function template(string $tpl)
    {
        $file = ('url:' == substr($tpl, 0, 4)) ? '/'.substr($tpl, 5) : ($tpl . '.tpl');

        return $this->templatePath($tpl).$file;
    }

    protected function addToFilter()
    {
        $this->filter[] = func_get_args();
    }

    protected function getFilter()
    {
        return $this->filter;
    }

    protected function getXFields()
    {
        return $this->x_fields;
    }

    public function getRequested()
    {
        return $this->requested;
    }

    public function requestedParams()
    {
        $xparams = $this->getRequested();

        $xparams['catid'] = $this->category;
        $xparams['s'] = $this->search;
        $xparams['order'] = $this->order;

        return array_filter($xparams);
    }

    /**
     * Config params for page display.
     *
     * @return array
     */
    protected function paginationParams()
    {
        return [
            'pluginName' => $this->plugin,
            'xparams' => $this->requestedParams(),
            'params' => [],
            'paginator' => ['page', 1, false],
        ];
    }

    protected function callingParams()
    {
        return [
            'style' => 'short',
            'plugin' => $this->plugin,
            'customCategoryTemplate' => false,
            // 'overrideTemplatePath' => $this->templatePath('news.table'),
            'currentCategoryId' => $this->category,
            'page' => $this->page,
            'newsOrder' => $this->orderAllowed[$this->order],
            // Set number of news per page.
            'showNumber' => $this->showNumber ? (int) $this->showNumber : 8,
            'extendedReturn' => true,
            'extendedReturnData' => true,
            'entendedReturnPagination' => true,
        ];
    }

    /**
     * Fetch news list from DB.
     *
     * @return object
     */
    protected function fetchNewsList()
    {
        $this->newsList = (object) news_showlist(
            $this->getFilter(),
            $this->paginationParams(),
            $this->callingParams()
        );

        return $this->newsList;
    }

    /**
     * Get news list.
     *
     * @return object
     */
    protected function newsList()
    {
        return is_object($this->newsList) ? $this->newsList : $this->fetchNewsList();
    }

    /**
     * Create template variables.
     *
     * @return array
     */
    protected function makeFormFields()
    {
        $tvars = [];

        foreach ($this->getXFields() as $id => $data) {
            // var_dump($data);
            $tvars['x_'.$id] = [
                'title' => $data['title'],
                'input' => $this->createField($id, $data),
                'active' => request('x_'.$id) ? true : false,
                // 'value' => secure_html(request('x_'.$id)) ?: false,
                // {{ x_price.value ? ' <small class="text-muted">('~x_price.value~')</small>' : '' }}
            ];
        }

        return $tvars;
    }

    /**
     * Generate html input of the field.
     *
     * @param  string $id
     * @param  array  $data
     * @return null|string
     */
    protected function createField(string $id, array $data)
    {
        if(!in_array($type = $data['type'], ['text', 'select'])) {
            return null;
        }

        $input = '<select name="x_'.$id.'" class="form-control">';
        $input .= $data['required'] ? '' : '<option value>'.__('sh_all').'</option>';

        switch ($type) {
            case 'text':
                $rows = cacheRemember($this->plugin, md5("distinct_xfields_$id"), function () use ($id) {
                    return database()->select(
                        "SELECT DISTINCT xfields_$id AS xtext,
                            count(*) AS xcount
                        FROM ".prefix."_news
                        WHERE `xfields_$id` != '' ".
                            ($this->category
                                ? " AND `catid` REGEXP '[[:<:]](".$this->category.")[[:>:]]'"
                                : '')."
                        GROUP BY xtext
                        ORDER BY xtext ASC"
                    );
                });

                foreach ($rows as $row) {
                    $xcount = (int) $row['xcount'];
                    $xtext = secure_html($row['xtext']);
                    $selected = in_array($xtext, $this->getRequested(), true) ? ' selected' : '';

                    $input .= "<option value=\"$xtext\" $selected>$xtext ($xcount)</option>";
                }
            break;

            case 'select':
                foreach ($data['options'] as $k => $v) {
                    $key = secure_html($data['storekeys'] ? $k : $v);
                    $value = secure_html($v);
                    $selected = in_array($key, $this->getRequested(), true) ? ' selected' : '';

                    $input .= "<option value=\"$key\" $selected>$value</option>";
                }

            break;
        }

        return $input.'</select>';
    }

    /**
     * Generate the "select" list of the categories.
     *
     * @return string
     */
    protected function makeCategoryList()
    {
        return makeCategoryList([
            'name' => 'catid',
            'selected' => $this->category,
            'skip' => $this->skippedCategories,
            'doall' => $this->showAllCat,
            'class' => 'form-control'
        ]);
    }

    /**
     * Renders a `form` template.
     *
     * @return string
     */
    public function renderForm()
    {
        return view($this->template('filter_form'), $this->makeFormFields(), [
            'form_action' => $this->formAction,
            'plugin_link' => $this->pluginLink,
            'catid' => $this->category,
            'search' => $this->search,

            'catlist' => $this->categoryList,
            'order' => $this->order,
        ]);
    }

    /**
     * Renders a `search_form` template.
     *
     * @return string
     */
    public function renderSearchForm()
    {
        return view($this->template('search_form'), [
            'form_action' => $this->formAction,
            'plugin_link' => $this->pluginLink,
            'catid' => $this->category,
            'search' => $this->search,

            'count' => $this->newsList()->count,
        ]);
    }

    /**
     * Renders a `news.table` template.
     *
     * @return string
     */
    public function renderPage()
    {
        return view('news.table.tpl', [
            'form_action' => $this->formAction,
            'plugin_link' => $this->pluginLink,
            'catid' => $this->category,
            'search' => $this->search,

            'catlist' => $this->categoryList,

            'x_filter_page' => true,
            'x_filter_search_form' => $this->renderSearchForm(),

            'count' => $this->newsList()->count,
            'data' => $this->newsList()->data,
            'pages' => $this->newsList()->pages,
            'pagination' => $this->newsList()->pagination,
        ]);
    }

    /**
     * Register meta info about the page, including `css` and `js` files.
     *
     * @param  boolean $ppage Indicates that this is the page of the plugin
     * @return void
     */
    public function htmlVars($ppage = false)
    {
        if ($this->canonical and $ppage) {
            register_htmlvar('plain',
                '<link rel="canonical" href="'.$this->pluginLink.'" />'
            );
        }

        if ($this->meta_robots and $ppage) {
            register_htmlvar('plain',
                '<meta name="robots" content="noindex, follow" />'
            );
        }

        if ($this->use_css) {
            register_htmlvar('css',
                $this->template('url::x_filter.css')
            );
        }

        if ($this->use_js) {
            register_htmlvar('plain',
                // Defer loading !!!
                '<script type="text/javascript" src="'.$this->template('url::x_filter.js').'" defer></script>'
            );
        }
    }

    /**
     * Set system info about the page.
     *
     * @return void
     */
    public function makePageInfo()
    {
        $breadcrumbs[] = [
            'link' => $this->pluginLink,
            'text' => __('x_filter:title'),
        ];

        if ($this->newsList()->count) {
            $title = str_replace(
                '{count}', $this->newsList()->count, __('x_filter:msg.find_count')
            );

            pageInfo('info.title.title', $title);

            $breadcrumbs[] = [
                'link' => $this->pluginLink,
                'text' => $title,
            ];
        }

        pageInfo('info.title.group', __('x_filter:title'));
        pageInfo('info.breadcrumbs', $breadcrumbs);
        pageInfo('meta.description', 'Description.');
        pageInfo('meta.keywords', 'key,keyword,keywords');
    }

    /**
     * Get an parameter from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getParam($key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->getParamValue($key);
        }
    }

    /**
     * Get a plain parameter.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getParamValue($key)
    {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }

    /**
     * Dynamically retrieve parameters on the plugin.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getParam($key);
    }

    /**
     * Determine if the given parameter exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return ! is_null($this->getParam($offset));
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getParam($offset);
    }

    /**
     * Determine if an attribute or relation exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    // /**
    //  * Handle dynamic method calls into the plugin.
    //  *
    //  * @param  string  $method
    //  * @param  array  $parameters
    //  * @return mixed
    //  */
    // public function __call($method, $parameters)
    // {
    //     if (in_array($method, ['increment', 'decrement'])) {
    //         return $this->$method(...$parameters);
    //     }
    //
    //     return $this->newQuery()->$method(...$parameters);
    // }
    //
    // /**
    //  * Handle dynamic static method calls into the method.
    //  *
    //  * @param  string  $method
    //  * @param  array  $parameters
    //  * @return mixed
    //  */
    // public static function __callStatic($method, $parameters)
    // {
    //     return (new static)->$method(...$parameters);
    // }

    /**
     * Prevent the instance from being cloned (which would create a second instance of it).
     */
    private function __clone()
    {
        // code...
    }

    /**
     * Prevent from being unserialized (which would create a second instance of it).
     */
    private function __wakeup()
    {
        // code...
    }
}
