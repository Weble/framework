<?php

namespace Zoolanders\Framework\Service;

class Path
{
    /**
     * Shortcut to the zoo's path helper (where zoo and zoolanders have to register the paths)
     * @var \PathHelper
     */
    protected $helper;

    /**
     * The filesystem to use
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * The list of registered paths
     * @var array
     */
    protected $registeredPaths = [];

    /**
     * Path constructor.
     * @param Filesystem|null $fs
     * @param Zoo $zoo
     */
    public function __construct(Filesystem $fs = null, Zoo $zoo)
    {
        $this->filesystem = $fs;
        $this->helper = $zoo->path;
    }

    /**
     * Register a path to a namespace
     *
     * @param string $path The path to register
     * @param string $namespace The namespace to register the path to
     */
    public function register($path, $namespace = 'default')
    {
        if (!isset($this->registeredPaths[$namespace])) {
            $this->registeredPaths[$namespace] = array();
        }

        array_unshift($this->registeredPaths[$namespace], $path);

        // also add to the zoo's ones
        $this->helper->register($path, $namespace);
    }

    /**
     * Get an absolute path to a file or a directory
     *
     * @param string $resource The resource with a namespace (ie: "assets:js/app.js")
     *
     * @return array|string The path(s) to the resource
     *
     * @since 1.0.0
     */
    public function path($resource)
    {
        return $this->helper->path($resource);
    }

    /**
     * Get all absolute paths registered to a file or a directory
     *
     * @param string $resource The resource with a namespace (ex: "assets:js/app.js")
     *
     * @return array The list of paths
     *
     * @since 1.0.0
     */
    public function paths($resource)
    {
        return $this->helper->paths($resource);
    }

    /**
     * Get the absolute url to a file
     *
     * @param string $resource The resource with a namespace (ex: "assets:js/app.js")
     *
     * @return string The absolute url
     *
     * @since 1.0.0
     */
    public function url($resource)
    {
        // init vars
        $parts = explode('?', $resource);
        $path = $this->path($parts[0]);

        $path = $path ? $path : $parts[0];

        $url = str_replace(DIRECTORY_SEPARATOR, '/', $path);

        if ($url) {

            if (isset($parts[1])) {
                $url .= '?' . $parts[1];
            }

            $url = \JURI::root(true) . '/' . $this->relative($url);
        }

        return $url;
    }

    /**
     * Get a list of files from a resource
     *
     * @param string $resource The resource with a namespace (ex: "assets:js/")
     * @param boolean $recursive If the search should be recursive (default: false)
     * @param string $filter A regex filter for the search
     *
     * @return array The list of files
     *
     * @since 1.0.0
     */
    public function files($resource, $recursive = false, $filter = null)
    {
        return $this->ls($resource, 'file', $recursive, $filter);
    }

    /**
     * Get a list of directories from a resource
     *
     * @param string $resource The resource with a namespace (ex: "assets:js/")
     * @param boolean $recursive If the search should be recursive (default: false)
     * @param string $filter A regex filter for the search
     *
     * @return array The list of directories
     *
     * @since 1.0.0
     */
    public function dirs($resource, $recursive = false, $filter = null)
    {
        return $this->ls($resource, 'dir', $recursive, $filter);
    }

    /**
     * Get a list of files or diretories from a resource
     *
     * @param string $resource The resource with a namespace (ex: "assets:js/")
     * @param string $mode Can be 'file' or 'dir'.
     * @param boolean $recursive If the search should be recursive (default: false)
     * @param string $filter A regex filter for the search
     *
     * @return array The list of files or directories
     */
    public function ls($resource, $mode = 'file', $recursive = false, $filter = null)
    {
        $files = array();
        $res = $this->parse($resource);

        foreach ($res['paths'] as $path) {
            if (file_exists($path . '/' . $res['path'])) {
                foreach ($this->_list($this->normalizePath($path . '/' . $res['path']), '', $mode, $recursive, $filter) as $file) {
                    if (!in_array($file, $files)) {
                        $files[] = $file;
                    }
                }
            }
        }

        return $files;
    }

    /**
     * Parse a resource string
     *
     * @param string $resource The resource with a namespace (ex: "assets:js/")
     *
     * @return array An associative array containing "namespace", "paths", "path"
     */
    public function parse($resource)
    {
        // init vars
        $parts = explode(':', $resource, 2);
        $count = count($parts);
        $path = '';
        $namespace = 'default';

        // parse resource path
        if ($count == 1) {
            list($path) = $parts;
        } elseif ($count == 2) {
            list($namespace, $path) = $parts;
        }

        // remove heading slash or backslash
        $path = ltrim($path, "\\/");

        // get paths for namespace, if exists
        $paths = $this->helper->paths($resource);

        return compact('namespace', 'paths', 'path');
    }

    /**
     * Get the list of files or directories in a given path
     *
     * @param string $path The path to search in
     * @param string $prefix A prefix to prepend
     * @param string $mode Can mode 'file' or 'dir'
     * @param boolean $recursive If the search should be recursive (default: false)
     * @param string $filter A regex filter to use
     *
     * @return array A list of files or directories
     *
     * @since 1.0.0
     */
    protected function _list($path, $prefix = '', $mode = 'file', $recursive = false, $filter = null)
    {
        $files = array();
        $ignore = array('.', '..', '.DS_Store', '.svn', '.git', '.gitignore', '.gitmodules', 'cgi-bin');

        if ($scan = $this->filesystem->listContents($path)) {
            foreach ($scan as $file) {
                // continue if ignore match
                if (in_array($file['basename'], $ignore)) {
                    continue;
                }

                if ($file['type'] == 'dir') {
                    // add dir
                    if ($mode == 'dir') {
                        // continue if no regex filter match
                        if ($filter && !preg_match($filter, $file)) {
                            continue;
                        }

                        $files[] = $prefix . $file['basename'];
                    }

                    // continue if not recursive
                    if (!$recursive) {
                        continue;
                    }

                    // read subdirectory
                    $files = array_merge($files, $this->_list($path . '/' . $file['basename'], $prefix . $file['basename'] . '/', $mode, $recursive, $filter));

                } else {

                    // add file
                    if ($mode == 'file') {

                        // continue if no regex filter match
                        if ($filter && !preg_match($filter, $file['basename'])) {
                            continue;
                        }

                        $files[] = $prefix . $file['basename'];
                    }

                }

            }
        }

        return $files;
    }

    /**
     * Makes a path relative to the Joomla root directory
     *
     * @param string $path The absolute path
     *
     * @return string The relative path
     *
     * @since 1.0.0
     */
    public function relative($path)
    {
        $relative = ltrim(preg_replace('/^' . preg_quote(str_replace(DIRECTORY_SEPARATOR, '/', JPATH_ROOT), '/') . '/i', '', str_replace(DIRECTORY_SEPARATOR, '/', realpath($path))), '/');

        // if equal, probably something went wrong, try without realpath (symlinks?)
        if ($path == $relative) {
            return ltrim(preg_replace('/^' . preg_quote(str_replace(DIRECTORY_SEPARATOR, '/', JPATH_ROOT), '/') . '/i', '', str_replace(DIRECTORY_SEPARATOR, '/', realpath($path))), '/');
        }

        return $relative;
    }

    /**
     * Normalizes the given path
     *
     * @param  string $path
     * @return string
     *
     * @since 3.3.8
     */
    protected function normalizePath($path)
    {
        $path = str_replace(['\\', '//'], '/', $path);
        $prefix = preg_match('|^(?P<prefix>([a-zA-Z]+:)?//?)|', $path, $matches) ? $matches['prefix'] : '';
        $path = substr($path, strlen($prefix));
        $parts = array_filter(explode('/', $path), 'strlen');
        $tokens = [];

        foreach ($parts as $part) {
            if ('..' === $part) {
                array_pop($tokens);
            } elseif ('.' !== $part) {
                array_push($tokens, $part);
            }
        }

        return $prefix . implode('/', $tokens);
    }
}
