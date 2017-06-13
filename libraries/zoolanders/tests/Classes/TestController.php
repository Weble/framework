<?php

namespace ZFTests\Classes;

use Zoolanders\Framework\Controller\Controller;
use Zoolanders\Framework\Controller\Mixin\HasRedirects;
use Zoolanders\Framework\Controller\Mixin\NeedsCsrfProtection;

/**
 * Class TestController
 * @package ZFTests\Classes
 */
class TestController extends Controller
{
    use HasRedirects, NeedsCsrfProtection;
    
    /**
     * Simple test action
     *
     * @return string
     */
    public function ping(){

        return 'test';
    }

    /**
     * Default dummy action
     *
     * @return string
     */
    public function _default(){

        return 'default';
    }
}
