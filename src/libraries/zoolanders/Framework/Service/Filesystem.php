<?php

namespace Zoolanders\Framework\Service;

use League\Flysystem\Plugin\EmptyDir;
use League\Flysystem\Plugin\ForcedCopy;
use League\Flysystem\Plugin\ForcedRename;
use League\Flysystem\Plugin\GetWithMetadata;
use League\Flysystem\Plugin\ListFiles;
use League\Flysystem\Plugin\ListPaths;
use League\Flysystem\Plugin\ListWith;
use Zoolanders\Framework\Service\Filesystem\Adapter\Local;
use League\Flysystem\Filesystem as LeagueFilesystem;

class Filesystem
{
    /**
     * Mime type related stuff
     */
    use Filesystem\Mime;

    /**
     * All the stuff related to file name cleaning, path cleaning, etc
     */
    use Filesystem\Clean;

    /**
     * Size and calculation related methods
     */
    use Filesystem\Size;

    /**
     * @var \League\Flysystem\Filesystem
     */
    protected $filesystem;

    /**
     * @var Zoo
     */
    protected $app;

    /**
     * Filesystem constructor.
     * @param \League\Flysystem\Filesystem|null $fs
     */
    public function __construct (LeagueFilesystem $fs = null, Zoo $zoo)
    {
        if (!$fs) {
            $adapter = new Local('/');
            $fs = new LeagueFilesystem($adapter);
        }

        $this->app = $zoo;
        $this->filesystem = $fs;

        // Load the extra plugins for all the methods
        $this->filesystem->addPlugin(new ForcedCopy());
        $this->filesystem->addPlugin(new ForcedRename());
        $this->filesystem->addPlugin(new GetWithMetadata());
        $this->filesystem->addPlugin(new ListFiles());
        $this->filesystem->addPlugin(new ListPaths());
        $this->filesystem->addPlugin(new ListWith());
        $this->filesystem->addPlugin(new EmptyDir());
    }

    /**
     * Proxy to filesystem
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call ($name, $arguments)
    {
        // do we have a path in the first argument?
        if ($arguments && count($arguments) > 0) {
            // make path absolute!
            $arguments[0] = $this->makePathAbsolute($arguments[0]);

            // make path absolute for the second argument too, if needed
            if (count($arguments) > 1 && in_array($name, ['copy', 'forceCopy', 'rename', 'forceRename'])) {
                $arguments[0] = $this->makePathAbsolute($arguments[0]);
            }
        }

        return call_user_func_array([$this->filesystem, $name], $arguments);
    }

    /**
     * Proxy to filesystem
     * @param $name
     * @return mixed
     */
    public function __get ($name)
    {
        return $this->filesystem->$name;
    }

    /**
     * Get a list of directories from the given directory
     *
     * @param string $path The path of the directory
     * @param string $prefix A prefix to prepend
     * @param string|boolean $filter A regex used to filter directories
     * @param boolean $recursive If the search should be recursive (default: true)
     *
     * @return array The list of subdirectories
     */
    public function readDirectory ($path, $prefix = '', $filter = false, $recursive = true)
    {

        $dirs = array();
        $ignore = array('.', '..', '.DS_Store', '.svn', '.git', '.gitignore', '.gitmodules', 'cgi-bin');

        if (is_readable($path) && is_dir($path) && $handle = @opendir($path)) {
            while (false !== ($file = readdir($handle))) {

                // continue if ignore match
                if (in_array($file, $ignore)) {
                    continue;
                }

                if (is_dir($path . '/' . $file)) {

                    // continue if not recursive
                    if (!$recursive) {
                        continue;
                    }

                    // continue if no regex filter match
                    if ($filter && !preg_match($filter, $file)) {
                        continue;
                    }

                    // read subdirectory
                    $dirs[] = $prefix . $file;
                    $dirs = array_merge($dirs, $this->readDirectory($path . '/' . $file, $prefix . $file . '/', $filter, $recursive));

                }
            }
            closedir($handle);
        }

        return $dirs;
    }

    /**
     * Get a list of files in the given directory
     *
     * @param string $path The path to search in
     * @param string $prefix A prefix to prepend
     * @param string|bool $filter A regex to filter the files
     * @param boolean $recursive If the search should be recursive (default: true)
     *
     * @return array The list of files
     */
    public function readDirectoryFiles ($path, $prefix = '', $filter = false, $recursive = true)
    {
        $files = array();
        $ignore = array('.', '..', '.DS_Store', '.svn', '.git', '.gitignore', '.gitmodules', 'cgi-bin');

        if (is_readable($path) && is_dir($path) && $handle = @opendir($path)) {
            while (false !== ($file = readdir($handle))) {

                // continue if ignore match
                if (in_array($file, $ignore)) {
                    continue;
                }

                if (is_dir($path . '/' . $file)) {

                    // continue if not recursive
                    if (!$recursive) {
                        continue;
                    }

                    // read subdirectory
                    $files = array_merge($files, $this->readDirectoryFiles($path . '/' . $file, $prefix . $file . '/', $filter, $recursive));

                } else {

                    // continue if no regex filter match
                    if ($filter && !preg_match($filter, $file)) {
                        continue;
                    }

                    $files[] = $prefix . $file;
                }
            }
            closedir($handle);
        }

        return $files;
    }

    /**
     * Get the file extension
     *
     * @param string $filename The file name
     *
     * @return string The file extension
     */
    public function getExtension ($filename)
    {
        $mimes = $this->getMimeMapping();
        $file = pathinfo($filename);
        $ext = isset($file['extension']) ? $file['extension'] : null;

        if ($ext) {
            // check extensions content type (with dot, like tar.gz)
            if (($pos = strrpos($file['filename'], '.')) !== false) {
                $ext2 = strtolower(substr($file['filename'], $pos + 1) . '.' . $ext);
                if (array_key_exists($ext2, $mimes)) {
                    return $ext2;
                }
            }

            // check extensions content type
            $ext = strtolower($ext);
            if (array_key_exists(strtolower($ext), $mimes)) {
                return $ext;
            }
        }

        return null;
    }

    /**
     * Concat two paths together. Basically $a + $b
     * @param string $a path one
     * @param string $b path two
     * @param string $ds optional directory seperator
     * @return string $a DIRECTORY_SEPARATOR $b
     */
    public function makePath ($a, $b, $ds = DIRECTORY_SEPARATOR)
    {
        return $this->cleanPath($a . $ds . $b, $ds);
    }

    /**
     * Alias for createDir() method
     * @param $folder
     * @return boolean
     */
    public function folderCreate ($folder)
    {
        return $this->filesystem->createDir($folder);
    }

    /**
     * Original Credits:
     * @package    JCE
     * @copyright    Copyright �� 2009-2011 Ryan Demmer. All rights reserved.
     * @license    GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
     *
     * Adapted to ZOO by ZOOlanders
     * Copyright 2011, ZOOlanders.com
     */
    public function getUploadValue ()
    {
        $upload = trim(ini_get('upload_max_filesize'));
        $post = trim(ini_get('post_max_size'));

        $upload = $this->returnBytes($upload);
        $post = $this->returnBytes($post);

        $result = $post;
        if (intval($upload) <= intval($post)) {
            $result = $upload;
        }

        return $this->formatFilesize($result, 'KB');
    }

    /**
     * Output size in bytes
     *
     * @param  string $size_str size string
     * @return string
     */
    public function returnBytes ($size_str)
    {
        $last_sign = substr($size_str, -1);
        $last_sign = in_array($last_sign, ['B', 'b']) ? substr($size_str, -2, 1) : $last_sign;

        switch ($last_sign) {
            case 'M':
            case 'm':
                return (int)$size_str * 1048576;
            case 'K':
            case 'k':
                return (int)$size_str * 1024;
            case 'G':
            case 'g':
                return (int)$size_str * 1073741824;
            default:
                return $size_str;
        }
    }

    /**
     * @param string $path
     * @return string
     */
    protected function makePathAbsolute ($path)
    {
        if (is_string($path)) {
            // is it relative? Convert it into absolute
            if (substr($path, 0, 1) !== DIRECTORY_SEPARATOR) {
                $path = JPATH_ROOT . '/' . $path;
            }
        }
        return $path;
    }
}
