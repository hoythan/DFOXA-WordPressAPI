<?php
// 时区设置
date_default_timezone_set('PRC');

require_once('wp_action.php');

/*
 * 获取相关 usermeta 的用户id
 */
function get_usermeta_userid($key, $value)
{

    global $wpdb;
    $request = $wpdb->get_row($wpdb->prepare("SELECT `user_id` FROM $wpdb->usermeta WHERE `meta_key` = %s AND `meta_value` = %s", array($key, $value)));
    if ($request != NULL) {
        return $request->user_id;
    }
    return false;
}

/*
 * 获取随机字符串
 */
function get_RandStr($minlength = 10, $maxlength = 16)
{
    $length = rand($minlength, $maxlength);
    $returnStr = '';
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
    for ($i = 0; $i < $length; $i++) {
        $returnStr .= $pattern{mt_rand(0, 61)};
    }
    return $returnStr;
}

/*
 * 获取与时间有关(唯一性)的字符串
 */
function get_MicroTimeStr()
{
    list($usec, $sec) = explode(" ", microtime());
    $msec = round($usec * 1000);
    $time = date('YmdHis', time());
    $onlyid = $time . $msec . rand(1000, 9999) . rand(1000, 9999);
    $id_line = strlen($onlyid);
    if ($id_line != 25) {
        if ($id_line < 25) {
            $n = 25 - $id_line;
            for ($i = 0; $i < $n; $i++) {
                $onlyid .= '0';
            }
        }
    }

    return $onlyid;
}

/**
 * 获取用户IP地址
 * @return string 用户IP
 */
function get_ClientIP()
{
    $userip = false;
    if ($_SERVER['REMOTE_ADDR']) {
        $userip = $_SERVER['REMOTE_ADDR'];
    } elseif (getenv("REMOTE_ADDR")) {
        $userip = getenv("REMOTE_ADDR");
    } elseif (getenv("HTTP_CLIENT_IP")) {
        $userip = getenv("HTTP_CLIENT_IP");
    }
    return $userip;
}

function get_MicroStr()
{
    $guid = str_split(get_GUIDStr());
    foreach ($guid as $k => $v) {
        $check = ord($v);
        if (($check >= 65 && $check <= 90) || ($check >= 97 && $check <= 122)) {
            $rand = rand(0, 1);
            $temp[] = $rand == 1 ? strtoupper($v) : strtolower($v);
        } else {
            $temp[] = $v;
        }
    }
    return implode('', $temp);
}

/*
 * 生成唯一的GUID
 */
function get_GUIDStr($strtolower = false)
{
    $charid = strtoupper(md5(uniqid(mt_rand(), true)));

    $uuid = substr($charid, 0, 8)
        . substr($charid, 8, 4)
        . substr($charid, 12, 4)
        . substr($charid, 16, 4)
        . substr($charid, 20, 12);
    if ($strtolower)
        return strtolower($uuid);

    return $uuid;
}

/*
 * 生成数字
 */
function get_FifteenNum($start = 1, $length = 15)
{
    $number = substr_replace('0', base_convert(get_GUIDStr(), 16, 10), 0, 1);
    return substr($number, $start, $length);
}


/*
 * 设置BIZ
 */
function setBizContent($arr)
{
    global $bizContent;

    if (count(get_object_vars($bizContent)) == 0) {
        $bizContent = new StdClass();
    }

    foreach ($arr as $key => $value) {
        $bizContent->$key = $value;
    }

    return true;
}

/*
 * 过滤不需要的请求内容并返回适当的内容
 */
function bizContentFilter($filters = array(), $bizContent = '')
{
    if (empty($bizContent)) {
        global $bizContent;
    }

    // 深拷贝
    if (isset($bizContent)) {
        /* @var cloneBiz $bizContent */
        $query = clone $bizContent;
    }

    if (empty($query)) {
        return array();
    }

    foreach ($query as $k => $v) {
        // 转换字符串true为布尔类型
        if ($query->$k === 'true') {
            $query->$k = true;
        } else if ($query->$k === 'false') {
            $query->$k = false;
        }
    }

    foreach ($query as $k => $v) {
        if (is_array($filters) && count($filters) == 0)
            break;

        if (!in_array($k, $filters)) {
            unset($query->$k);
            continue;
        }
    }

    // 过滤 usermeta
    $metaFilter = get_option('dfoxa_t_account_edit_usermetakey');

    if (trim($metaFilter) === '*' || !isset($query->usermeta))
        return $query;

    if (empty($metaFilter)) {
        unset($query->usermeta);
        return $query;
    }


    $metaFilters = explode("\n", $metaFilter);
    foreach ($metaFilters as $key => $value) {
        $metaFilters[$key] = trim($value);
    }

    foreach ($query->usermeta as $k => $v) {
        if (!in_array($k, $metaFilters)) {
            unset($query->usermeta->$k);
            continue;
        }
    }

    return $query;
}


/*
 * 设置全局错误信息，为了弥补报错参数少的问题
 */
function dfoxa_append_message($message, $key = '_')
{
    global $dfoxaAppendMessages;
    $dfoxaAppendMessages[$key][] = $message;
}

/*
 * 接口报错封装函数
 */
function dfoxaError($sub_code, $message = array(), $httpCode = 200)
{
    if (is_object($message))
        $message = objectToArray($message);

    dfoxa_append_message($message, $sub_code);
    throw new \Exception($sub_code, $httpCode);
}

/**
 * 正确接口返回封装
 */
use gateway\method as Gateway;

function dfoxaGateway($response = '', $status = '10000', $code = '200', $arrayKey = '', $hideRequest = false)
{
    if (empty($status))
        $status = '10000';

    if (empty($code))
        $code = '200';


    global $dfoxaAppendMessages;
    if (isset($dfoxaAppendMessages['_'])) {
        foreach ($dfoxaAppendMessages['_'] as $append_message) {
            $response = wp_parse_args($append_message, $response);
        }
    }

    Gateway::responseSuccessJSON($response, $status, $code, $arrayKey, $hideRequest);
}


/*
 * 数组转为对象
 */
function arrayToObject($e)
{
    if (gettype($e) != 'array') return;
    foreach ($e as $k => $v) {
        if (gettype($v) == 'array' || getType($v) == 'object')
            $e[$k] = (object)arrayToObject($v);
    }
    return (object)$e;
}

/*
 * 对象转数组
 */
function objectToArray($e)
{
    if (is_object($e)) {
        $e = (array)$e;
    }
    if (is_array($e)) {
        foreach ($e as $key => $value) {
            $e[$key] = objectToArray($value);
        }
    }
    return $e;
}

/*
 * 数组，对象，字符串反序列化
 */
function dataToUnserializeData($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = dataToUnserializeData($value);
        }
    } elseif (is_object($data)) {
        $data = arrayToObject(dataToUnserializeData(objectToArray($data)));
    } else {
        $data = maybe_unserialize($data);
    }

    return $data;
}

function load_fileContent($meta_key, $size = 'full')
{
    global $wpdb;
    $table = $wpdb->prefix . 'wpapi_imagemeta';
    $response = $wpdb->get_row($wpdb->prepare("SELECT `post_id` FROM {$table} WHERE `meta_key` = '%s'", array($meta_key)));

    // 文件是否存在
    if (empty($response))
        dfoxaError('cache.empyt-cachetype');

    $attachment = get_post($response->post_id);
    if (empty($attachment))
        dfoxaError('cache.empyt-cachetype');

    // 获取文件地址
    if (wp_attachment_is('image', $attachment->ID)) {
        $file_url = wp_get_attachment_image_url($attachment->ID, $size);
    } else {
        $file_url = wp_get_attachment_url($attachment->ID);
    }

    $content = file_get_contents($file_url);
    ob_clean();
    status_header(200);
    header('Content-Type: ' . $attachment->post_mime_type);
    header('Accept-Ranges: none');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Content-Length: ' . strlen($content));
    header('Cache-Control: max-age=' . 864000 . ', must-revalidate');
    header('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('now +10 days')) . ' GMT');
    echo $content;
    exit;
}

/*
 * 删除目录以及目录下的所有文件
 */
use houdunwang\dir\Dir;

function dfoxa_removeDirFiles($path)
{
    if (Dir::del($path)) {
        return true;
    }
    return false;
}


/*
 * 获取和检查 DFOXA 插件数据
 */
function get_dfoxa_plugin_data($plugin_file, $markup = true, $translate = true)
{
    $default_headers = array(
        'Name' => 'Plugin Name',
        'PluginURI' => 'Plugin URI',
        'Version' => 'Version',
        'Description' => 'Description',
        'Author' => 'Author',
        'AuthorURI' => 'Author URI',
        'Tags' => 'Tags',
        'Namespace' => 'Namespace'
    );

    $plugin_data = get_file_data($plugin_file, $default_headers, 'plugin');

    return $plugin_data;
}

function get_dfoxa_plugins($plugin_name = '')
{
    $plugins = [];
    $plugins_dir = @opendir(DFOXA_PLUGINS);
    $plugin_files = array();
    if ($plugins_dir) {
        while (($file = readdir($plugins_dir)) !== false) {
            if (substr($file, 0, 1) == '.')
                continue;
            if (is_dir(DFOXA_PLUGINS . DFOXA_SEP . $file)) {
                $plugins_subdir = @ opendir(DFOXA_PLUGINS . DFOXA_SEP . $file);
                if ($plugins_subdir) {
                    while (($subfile = readdir($plugins_subdir)) !== false) {
                        if (substr($subfile, 0, 1) == '.')
                            continue;
                        if (substr($subfile, -4) == '.php')
                            $plugin_files[] = "$file/$subfile";
                    }
                    closedir($plugins_subdir);
                }
            } else {
                if (substr($file, -4) == '.php')
                    $plugin_files[] = $file;
            }
        }
        closedir($plugins_dir);
    }

    // 没有插件
    if (empty($plugin_files))
        return true;

    foreach ($plugin_files as $plugin_file) {

        // 检查文件是否可用
        if (!is_readable(DFOXA_PLUGINS . "/$plugin_file"))
            continue;


        $plugin_data = get_dfoxa_plugin_data(DFOXA_PLUGINS . "/$plugin_file", false, false); //Do not apply markup/translate as it'll be cached.

        if (empty ($plugin_data['Name']))
            continue;

        if (empty ($plugin_data['Namespace']))
            continue;

        $plugin_data['Namespace'] = explode(',', $plugin_data['Namespace']);
        $plugins[$plugin_file] = $plugin_data;
    }

    if ($plugin_name == '') {
        return $plugins;
    } elseif (isset($plugins[$plugin_name])) {
        return $plugins[$plugin_name];
    } else {
        return false;
    }

}


/*
 *  获取已经激活的插件
 */
function get_dfoxa_active_plugins()
{
    $plugins = get_dfoxa_plugins();
    $active_plugins = array();
    if (!is_array($plugins))
        return [];

    foreach ($plugins as $plugin_name => $plugin) {
        $plugin_key = 'dfoxa_' . $plugin_name;
        $active = get_option($plugin_key) == '1' ? true : false;
        if ($active)
            $active_plugins[$plugin_name] = $plugin;
    }
    return $active_plugins;
}

/*
 * 加载已激活的插件的 functions.php
 */
function load_plugins_funfile()
{
    $plugins = get_dfoxa_active_plugins();
    foreach ($plugins as $pluginfile => $plugin) {
        $funFilePath = dirname(DFOXA_PLUGINS . DFOXA_SEP . $pluginfile) . DFOXA_SEP . 'functions.php';
        if (file_exists($funFilePath))
            require_once($funFilePath);
    }
}

add_action('init', 'load_plugins_funfile', 1);