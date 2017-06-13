<?php

namespace Zoolanders\Framework\Service\Filesystem;

trait Size
{

    /**
     * Output filesize with suffix.
     *
     * @param $bytes integer byte size
     * @param $format string|boolean the size format
     * @param $precision integer the number precision
     *
     * @return string
     */
    public function formatFilesize($bytes, $format = false, $precision = 2)
    {
        $kilobyte = 1024;
        $megabyte = $kilobyte * 1024;
        $gigabyte = $megabyte * 1024;
        $terabyte = $gigabyte * 1024;

        if (($bytes >= 0) && ($bytes < $kilobyte) && !$format || $format == 'B') {
            return $bytes . ' B';

        } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte) && !$format || $format == 'KB') {
            return round($bytes / $kilobyte, $precision) . ' KB';

        } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte) && !$format || $format == 'MB') {
            return round($bytes / $megabyte, $precision) . ' MB';

        } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte) && !$format || $format == 'GB') {
            return round($bytes / $gigabyte, $precision) . ' GB';

        } elseif ($bytes >= $terabyte && !$format || $format == 'TB') {
            return round($bytes / $terabyte, $precision) . ' TB';
        } else {
            return $bytes . ' B';
        }
    }

    /**
     * get the file or folder files size (with extension filter - incomplete)
     *
     * @param $source string the source path string
     * @param $format boolean if true will return the result formated for better reading
     * @return string
     */
    public function getSourceSize($source = null, $format = true)
    {
        // init vars
        $sourcepath = $this->app->path->path('root:' . $source);

        if (strpos($source, 'http') === 0) // external source
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_URL, $source); //specify the url
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $head = curl_exec($ch);

            $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        }

        // value check
        if (empty($size)) {
            $size = 0;
        }

        if (is_file($sourcepath)) {
            $size = filesize($sourcepath);
        } else if (is_dir($sourcepath)) foreach ($this->app->path->files('root:' . $source, false, '/^.*()$/i') as $file) {
            $size += filesize($this->app->path->path("root:{$source}/{$file}"));
        }

        // return size
        return $format ? $this->formatFilesize($size) : $size;
    }
}
