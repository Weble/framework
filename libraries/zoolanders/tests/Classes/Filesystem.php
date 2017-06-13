<?php

namespace ZFTests\Classes;

use Zoolanders\Framework\Service\Filesystem as FSBase;
use Zoolanders\Framework\Service\Filesystem\Size;
use Zoolanders\Framework\Service\Filesystem\Mime;
use Zoolanders\Framework\Service\Filesystem\Clean;

/**
 * Class Filesystem
 * Filesystem assembly class for testing FS traits
 *
 * @package ZFTests\Classes
 */
class Filesystem extends FSBase
{
    use Size, Mime, Clean;
}
