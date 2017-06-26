<?php

namespace Zoolanders\Framework\Listener\Environment;

use Zoolanders\Framework\Listener\Listener;
use Zoolanders\Framework\Service\Data;
use Zoolanders\Framework\Service\Filesystem;
use Zoolanders\Framework\Service\Link;
use Zoolanders\Framework\Service\System\Document;
use Zoolanders\Framework\Service\Zoo;

class LoadFieldAssets extends Listener
{
    public function __construct (Document $document, Zoo $zoo, Link $link, Filesystem $fs, Data $data)
    {
        $this->document = $document;
        $this->zoo = $zoo;
        $this->link = $link;
        $this->fs = $fs;
        $this->data = $data;
    }

    /**
     * @param \Zoolanders\Framework\Event\Environment\Init $event
     */
    public function handle (\Zoolanders\Framework\Event\Environment\Init $event)
    {
        // perform admin tasks
        if ($event->is('zoo-type-edit')) {
            // init vars
            $cid = $event->getRequest()->get('cid.0', 'string', '');
            $type = $cid ? $cid : $event->getRequest()->get('type', 'string', '');

            $url = $this->link->link(array('option' => 'com_zoo', 'controller' => 'zlframework', 'format' => 'raw', 'type' => $type), false);
            $enviroment_args = json_decode($event->getRequest()->get('enviroment_args', 'string', ''), true);
            $enviroment_args = $this->data->create($enviroment_args);

            // load zlfield assets
            $this->document->addStylesheet('zlfield:zlfield.css');
            $this->document->addStylesheet('zlfield:layouts/field/style.css');
            $this->document->addStylesheet('zlfield:layouts/separator/style.css');
            $this->document->addStylesheet('zlfield:layouts/wrapper/style.css');
            $this->document->addScript('zlfield:zlfield.js');

            if ($event->getRequest()->getVar('enviroment', false) == 'module') {
                $this->document->addScript('libraries:jquery/jquery-ui.custom.min.js');
                $this->document->addStylesheet('libraries:jquery/jquery-ui.custom.css');
                $this->document->addScript('libraries:jquery/plugins/timepicker/timepicker.js');
                $this->document->addStylesheet('libraries:jquery/plugins/timepicker/timepicker.css');
            }

            // workaround for jQuery 1.9 transition
            $this->document->addScript('zlfw:assets/js/jquery.plugins/jquery.migrate.min.js');

            // load libraries
            $this->zoo->getApp()->zlfw->zlux->loadMainAssets();
            $this->zoo->getApp()->zlfw->loadLibrary('qtip');
            $this->document->addStylesheet('zlfw:assets/libraries/zlux/zlux.css');

            // load wk2 assets
            if ($this->fs->has(JPATH_ADMINISTRATOR . '/components/com_widgetkit/widgetkit.xml')) {
                $wk_manifest = simplexml_load_file(JPATH_ADMINISTRATOR . '/components/com_widgetkit/widgetkit.xml');

                if (version_compare($wk_manifest->version, '2.0', '>=') &&
                    $app = @include(JPATH_ADMINISTRATOR . '/components/com_widgetkit/widgetkit-app.php')
                ) {
                    $app->trigger('init.admin', array($app));
                }
            }

            // init scripts
            $javascript = "jQuery(function($){ $('body').ZLfield({ url: '{$url}', type: '{$type}', enviroment: '{$event->getRequest()->get('enviroment', '')}', enviroment_args: '{$enviroment_args}' }); });";
            $this->document->addScriptDeclaration($javascript);
        }
    }
}
