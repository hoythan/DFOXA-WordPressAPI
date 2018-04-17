<?php

namespace media;

class get extends file
{
    public function run()
    {
        $file_id = '';
        $size = 'full';

        // 验证身份
        $query = bizContentFilter(array(
            'file_id',
            'size'
        ));

        if (empty($query->file_id) && !isset($_GET['file_id']))
            dfoxaError('media.empty-notfound', array(), 404);

        // 文件ID
        if (!empty($query->file_id)) {
            $file_id = $query->file_id;
        } elseif (isset($_GET['file_id'])) {
            $file_id = $_GET['file_id'];
        }

        // 文件大小
        if (!empty($query->file_id)) {
            $size = $query->size;
        } elseif (isset($_GET['size'])) {
            $size = $_GET['size'];
        }

        load_fileContent($file_id, $size);
    }
}