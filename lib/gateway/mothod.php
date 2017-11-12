<?php

namespace gateway;

class mothod
{
    private $methodClass;

    public function run()
    {
        /*
        接口允许的调用方式
            {网关}?method=tests.test1.test2
            {网关}/tests/test1/test2
        */
        $method = null;
        try {
            // 检查网关
            $this->_setupCheckGateway();

            // 检查并设置查询类
            $this->_setupCheckMothod();

            // 筛选查询数据
            $this->_getRequest();

            // 执行相关查询类
            $method = new $this->methodClass();
            $method->run();

            dfoxaError('gateway.empty-run');
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
    private function _setupCheckMothod()
    {
        // 检查请求体是否存在
        global $methodClass;
        $methodClass = '';
        $methodNameSpace = '';

        if (isset($_GET['method'])) {
            $class = explode('.', $_GET['method']);
            $num = count($class);
            if ($num < 2)
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
            if ($num < 3)
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
        header('Access-Control-Allow-Headers:Origin, X-Requested-With, Content-Type, Accept');
        header('Access-Control-Allow-Credentials:true'); // 接收 Cookie
        header('Content-type: application/json');

        echo json_encode(code::_e($e->getMessage()));
        exit;
    }

    /*
     * 输出 json 格式结果,在成功的情况下执行
     * $arrkey 如果填写，则拼接到code之下
     */
    public static function responseSuccessJSON($response = '', $status = '10000', $code = '200', $arrayKey = '')
    {
        ob_clean();
        status_header($code);
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Headers:Origin, X-Requested-With, Content-Type, Accept');
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
        global $bizContent;
        $response['request'] = $bizContent;
        if ($arrayKey == '') {
            $echo_array = array_merge(code::_e($status), $response);
        } else {
            $echo_array = array_merge(code::_e($status), array($arrayKey => $response));
        }
        echo json_encode($echo_array);
        exit;
    }

}