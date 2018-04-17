<?php
/*
 * 对数据表的操作添加
 */

namespace tools\sql;

use Respect\Validation\Validator as Validator;

class DFOXA_Sql
{
    public function get($table, $where = array(), $need = array(), $filters = array(), $order = array(), $single = true ,$limit = array())
    {
        global $wpdb;

        /*
         * 拼接查询条件
         */
        $where_format = [];
        foreach ($where as $key => $value) {
            if (!in_array($key, array_keys($filters))) {
                unset($where[$key]);
                continue;
            }

            $where_format[] = $filters[$key];
        }

        $query_where = '';
        $i = 0;
        foreach ($where as $key => $value) {
            if (is_array($value)) {
                $w = '';
                foreach ($value as $v) {
                    if (!empty($where_format[$key]) || $where_format[$i] !== '%d') {
                        $v = "'{$v}'";
                    }
                    $w .= "{$v},";
                }
                $w = chop($w, ",");
                $query_where .= " `{$key}` in ({$w}) AND ";
            } else {
                if (!empty($where_format[$i]) || $where_format[$i] !== '%d') {
                    $value = "'{$value}'";
                }
                $query_where .= "`{$key}` = {$value}  AND ";
            }


            $i++;
        }
        $query_where = chop($query_where, 'AND ');

        /*
         * 拼接查询条件
         */
        $query_need = '';
        foreach ($need as $key) {
            $query_need .= '`' . $key . '`,';
        }
        $query_need = chop($query_need, ',');

        if (empty(trim($query_need)))
            $query_need = '*';

        /*
         * 排序处理
         */
        $query_order = '';
        if (Validator::arrayType()->validate($order) && count($order) > 0) {
            $query_order = ' ORDER BY ';
            foreach ($order as $k => $v) {
                $query_order .= "`{$k}` {$v} ,";
            }
            $query_order = chop($query_order, ' ,');
        }

        $query_limit = '';
        if(is_array($limit) && count($limit) > 0 && count($limit) < 3){
            $query_limit = ' LIMIT ' . implode(' , ',$limit);
        }

        $sql = "SELECT {$query_need} FROM `{$table}` WHERE {$query_where} {$query_order} {$query_limit}";

        if ($single === true) {
            $result = $wpdb->get_row($sql);
        } else {
            $result = $wpdb->get_results($sql);
        }

        if ($result === NULL)
            return false;

        return $result;
    }

    public function add($table, $data, $filters = array())
    {
        global $wpdb;

        $format = [];

        foreach ($data as $key => $value) {
            if (!in_array($key, array_keys($filters))) {
                unset($data[$key]);
                continue;
            }

            $format[] = $filters[$key];


            // 序列化$value
            if (!is_serialized($value, false)) {
                $data[$key] = maybe_serialize($value);
            }

        }

        $wpdb->insert($table, $data, $format);
        if ($wpdb->insert_id === 0)
            return false;

        return $wpdb->insert_id;
    }

    public function update($table, $data = array(), $where = array(), $filters)
    {
        global $wpdb;

        /*
         * 过滤数据
         */
        $format = [];
        foreach ($data as $key => $value) {
            if (!in_array($key, array_keys($filters))) {
                unset($data[$key]);
                continue;
            }


            $format[] = $filters[$key];

            // 序列化$value
            if (!is_serialized($value)) {
                $data[$key] = maybe_serialize($value);
            }
        }

        $where_format = [];
        foreach ($where as $key => $value) {
            if (!in_array($key, array_keys($filters))) {
                unset($where[$key]);
                continue;
            }

            $where_format[] = $filters[$key];
        }

        $query_where = '';
        $i = 0;
        foreach ($where as $key => $value) {
            if (empty($where_format[$i]) && $where_format[$i] === '%d') {
            } else {
                $value = "'{$value}'";
            }

            $query_where .= " `{$key}` = {$value} AND";
            $i++;
        }
        $query_where = chop($query_where, 'AND');

        // 检查是否存在，不存在创建，存在更新
        $response = $wpdb->get_var("SELECT * FROM {$table} WHERE {$query_where}");
        if ($response === NULL)
            return $this->add($table, array_merge($where, $data), $filters);

        // 更新
        $result = $wpdb->update($table, $data, $where, $format, $where_format);
        if ($result === false)
            return false;

        return $response;
    }

    public function remove()
    {

    }

    public function unSerializeData($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->unSerializeData($value);
            }
        }
        if (is_object($data)) {
            foreach ($data as $key => $value) {
                $data->$key = $this->unSerializeData($value);
            }
        }

        return maybe_unserialize($data);
    }
}