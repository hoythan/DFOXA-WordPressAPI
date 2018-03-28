<?php

namespace gateway;

class method
{
    private $methodClass;

    public function run()
    {
        // 定义中国时区
        date_default_timezone_set('PRC');

        /*
        接口允许的调用方式
            {网关}?method=tests.test1.test2
            {网关}/tests/test1/test2
        */
        $method = null;
        try {
            // 多站点切换
            $this->setupMultiSize();

            // 检查网关
            $this->_setupCheckGateway();

            // 检查并设置查询类
            $this->_setupCheckMethod();

            // 筛选查询数据
            $this->_getRequest();

            // 执行相关查询类
            $method = new $this->methodClass();

            $method->run();

            dfoxaError('gateway.empty-run', '', 204);
        } catch (\Exception $e) {
            self::_responseJSON($e);
        }
    }

    /*
     * 检查网关
     */
    private function _setupCheckGateway()
    {
        global $wp_query;
        $gateway = get_option('dfoxa_gateway');

        if (!isset($wp_query->query['pagename']))
            dfoxaError('gateway.empty-gateway', array(), -1);

        $pagename = $wp_query->query['pagename'];

        // 检测网关是否配置
        if ($gateway == '')
            dfoxaError('gateway.empty-gateway', array(), -1);


        // 检查是否匹配网关
        $gateway = get_option('dfoxa_gateway');
        if ($pagename != $gateway && strpos($pagename, $gateway) !== 0)
            dfoxaError('gateway.undefined', array(), -1);

        return true;
    }

    /**
     * 检查查询内容接口是否存在
     * 如果存在则定义 $methodClass 为所需的类
     * @throws \Exception
     */
    private function _setupCheckMethod()
    {
        // 检查请求体是否存在
        global $methodClass;
        $methodClass = '';
        $methodNameSpace = '';

        if (isset($_GET['method'])) {
            $class = explode('.', $_GET['method']);
            $num = count($class);
            if ($num < 1)
                dfoxaError('gateway.method-undefined');

            /*
            method
                 =>     '?method=access.sign.up'
            $methodClass
                 =>     '\access\sign\up'
            */

            for ($i = 0; $i < $num; $i++) {
                $methodClass .= '\\' . $class[$i];

                if ($i < $num - 1)
                    $methodNameSpace .= $methodNameSpace == '' ? $class[$i] : '\\' . $class[$i];
            }
        } else {
            global $wp_query;
            $pagename = $wp_query->query['pagename'];
            $class = explode('/', $pagename);
            $num = count($class);
            if ($num < 2)
                dfoxaError('gateway.method-undefined');

            for ($i = 1; $i < $num; $i++) {
                $methodClass .= '\\' . $class[$i];
                if ($i < $num - 1)
                    $methodNameSpace .= $methodNameSpace == '' ? $class[$i] : '\\' . $class[$i];
            }
        }

        /*
         * 如果无法找到原生的接口,则判断是否有注册插件
         * 否则执行hook dfoxa_wpapi_method_exists_class
         */
        if (!class_exists($methodClass)) {
            // 自加载无效,加载插件
            foreach (get_dfoxa_active_plugins() as $pluginname => $plugin) {
                if (in_array($methodNameSpace, $plugin['Namespace']) && file_exists(DFOXA_PLUGINS . DFOXA_SEP . $pluginname)) {
                    include_once(DFOXA_PLUGINS . DFOXA_SEP . $pluginname);
                    // index.php
                    if (file_exists(str_replace('\\', DFOXA_SEP, DFOXA_PLUGINS . $methodClass . DFOXA_SEP . 'index.php')))
                        include_once(str_replace('\\', DFOXA_SEP, DFOXA_PLUGINS . $methodClass . DFOXA_SEP . 'index.php'));

                    // file.php
                    if (file_exists(str_replace('\\', DFOXA_SEP, DFOXA_PLUGINS . $methodClass . '.php')))
                        include_once(str_replace('\\', DFOXA_SEP, DFOXA_PLUGINS . $methodClass . '.php'));
                }
            }

            if (!class_exists($methodClass)) {
                apply_filters('dfoxa_wpapi_method_exists_class', $pagename);
                dfoxaError('gateway.empty-method');
            }
        }

        $this->methodClass = $methodClass;
    }

    /*
     * 检查查询数据并返回查询数组
     */
    public function _getRequest()
    {
        // OPTIONS 一律直接返回正确
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
            dfoxaError('gateway.options-success');


        global $bizContent;
        // 过滤GET POST以外的请求
        $request_method = array('GET', 'POST');

        if (!in_array($_SERVER['REQUEST_METHOD'], $request_method))
            dfoxaError('gateway.error-request');

        if (stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            if (file_get_contents('php://input')) {
                $biz = file_get_contents('php://input');
                $bizContent = json_decode($biz);
            } else if (!empty($GLOBALS ['HTTP_RAW_POST_DATA'])) {
                $biz = $GLOBALS ['HTTP_RAW_POST_DATA'];
                $bizContent = json_decode($biz);
            } else {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $bizContent = arrayToObject($_POST);
                } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $bizContent = arrayToObject($_GET);
                }
            }
        } else if (stripos($_SERVER['CONTENT_TYPE'], 'application/x-www-form-urlencoded') !== false) {
            $biz = file_get_contents('php://input') ? file_get_contents('php://input') : gzuncompress($GLOBALS ['HTTP_RAW_POST_DATA']);
            if (gettype($biz) == 'string') {
                $bizContent = arrayToObject($_POST);
            } else {
                $bizContent = json_decode($biz);
            }
        } else if (stripos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false) {
            $bizContent = arrayToObject($_POST);
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $bizContent = arrayToObject($_GET);
        }
        return true;
    }


    /**
     * 多站点检查和切换,在多站点情况下强制检查是否有提交 BLOG_ID ，如果不存在则直接报错
     */
    private function setupMultiSize()
    {
        if (!is_multisite()) return true;

        $blog_id = 0;
        $query = bizContentFilter(array(
            'blog_id'
        ));

        if (!empty($query->blog_id)) {
            // 从用户请求中获取
            $blog_id = $query->blog_id;
        } else if (isset($_GET['blog_id'])) {
            // 从 URL地址 中获取
            $blog_id = $_GET['blog_id'];
        } else if (isset($_COOKIE['blog_id'])) {
            // 从 Cookie 中获取
            $blog_id = $_COOKIE['blog_id'];
        } else if (isset($_SERVER['HTTP_BLOG_ID'])) {
            // 从 请求头 获取
            $blog_id = $_SERVER['HTTP_BLOG_ID'];
        }

        if (empty($blog_id) || $blog_id === 0 || get_blog_option($blog_id, 'siteurl') === false) {
            dfoxa_append_message(array('_is_multisite_mode' => false));
        } else {
            dfoxa_append_message(array('_is_multisite_mode' => true));
            switch_to_blog($blog_id);
            /**
             * 多站点模式下,缓存系统所保存的站点需固定,
             * 因在用户登录的时候还没有切换站点,缓存系统使用的是默认的主站点,
             * 如果不强制设置,这会导致登录后无法获取到缓存系统内容
             */
            wp_cache_switch_to_blog(get_current_network_id());
        }
    }

    /*
     * 输出 json 格式结果,在出错的情况下执行
     */
    private static function _responseJSON($e)
    {
        if ($e->getCode() === -1) {
            return true;
        }

        ob_clean();
        if (!empty($e->getCode())) {
            status_header($e->getCode());
        } else {
            status_header(200);
        }

        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Headers:Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With, Access-Token, Blog-ID');
        header('Access-Control-Allow-Credentials:true'); // 接收 Cookie
        header('Content-type: application/json');

        echo json_encode(code::_e($e->getMessage()));
        exit;
    }

    /*
     * 输出 json 格式结果,在成功的情况下执行
     * $arrkey 如果填写，则拼接到code之下
     */
    public static function responseSuccessJSON($response = '', $status = '10000', $code = '200', $arrayKey = '', $hideRequest = false)
    {
        status_header($code);
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Headers:Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With, Access-Token, Blog-ID');
        header('Access-Control-Allow-Credentials:true'); // 接收 Cookie
        header('Content-type: application/json');
        // 清理空的返回内容
        if (is_object($response))
            $response = objectToArray($response);

        if (!is_array($response))
            $response = array('res' => $response);

        if (empty($response)) {
            $response = array();
        }

        // 将用户的请求包含在返回的内容中

        if (!$hideRequest) {
            global $bizContent;
            $response['request'] = $bizContent;
        }

        if ($arrayKey == '') {
            $echo_array = array_merge(code::_e($status), $response);
        } else {
            $echo_array = array_merge(code::_e($status), array($arrayKey => $response));
        }

        /**
         * 强制关闭所有报错信息
         * https://developer.wordpress.org/reference/functions/wp_debug_mode/
         */
        ini_set('display_errors', 0);
        ob_clean();
        echo json_encode($echo_array);
        exit;
    }
}