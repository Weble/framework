<?php

namespace Zoolanders\Framework\Response;
/**
 * Class FileResponse
 * @package Zoolanders\Framework\Response
 */
class FileResponse extends Response
{
    /**
     * Mime type related stuff
     */
    use \Zoolanders\Framework\Service\Filesystem\Mime;

    /**
     * FileResponse constructor.
     *
     * @param string $file
     * @param int $code
     */
    public function __construct ($file, $code = 200)
    {
        if (!file_exists($file)) {
            parent::__construct('', 404);
            return;
        }

        $fileContents = file_get_contents($file);

        parent::__construct($fileContents, $code);

        $this->setHeadersFromFile($file);
    }

    /**
     * @param $file
     */
    protected function setHeadersFromFile ($file)
    {
        $name = basename($file);
        $type = $this->getContentType($name);
        $size = @filesize($file);
        $mod = date('r', filemtime($file));

        $this->setHeader("Pragma", "public");
        $this->setHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
        $this->setHeader("Expires", "0");
        $this->setHeader("Content-Transfer-Encoding", "binary");
        $this->setHeader('Content-Type', $type);
        $this->setHeader('Content-Disposition', 'attachment;'
            . ' filename="' . $name . '";'
            . ' modification-date="' . $mod . '";'
            . ' size=' . $size . ';');
        $this->setHeader("Content-Length",  $size);
    }
}
