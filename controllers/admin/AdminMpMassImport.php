<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Massimiliano Palermo <maxx.palermo@gmail.com>
 * @copyright Since 2016 Massimiliano Palermo
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
class AdminMpMassImportController extends ModuleAdminController
{
    public function __construct()
    {
        $this->module = Module::getInstanceByName('mpmassimport');
        $this->translator = Context::getContext()->getTranslator();

        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->table = 'product';
        $this->className = 'Product';
        $this->identifier = 'id_product';
        $this->lang = true;

        parent::__construct();
    }

    public function _initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        unset($this->page_header_toolbar_btn['new']);
        $this->page_header_toolbar_btn = [
            'configure' => [
                'href' => $this->context->link->getAdminLink($this->controller_name) . '&action=configure',
                'desc' => $this->trans('Configuration'),
            ],
        ];
    }

    public function _initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
        $this->toolbar_btn = [
            'configure' => [
                'href' => $this->context->link->getAdminLink($this->controller_name) . '&action=configure',
                'desc' => $this->trans('Configuration'),
            ],
        ];
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS($this->module->getLocalPath() . 'views/css/datatables.min.css', 'all', 1000);
        $this->addJS($this->module->getLocalPath() . 'views/js/datatables.min.js');
    }

    public function initContent()
    {
        $tpl_path = $this->module->getLocalPath() . 'views/templates/admin/main.tpl';
        $tpl = $this->context->smarty->createTemplate($tpl_path, $this->context->smarty);
        $tpl->assign([
            'module' => $this->module,
            'prodotti' => [],
        ]);
        $this->content .= $tpl->fetch();

        /*
        $pluginClass = new MpSoft\MpMassImport\Plugins\Plugin($this->module);
        $pluginClass->fetchPlugins();
        $this->content .= $pluginClass->renderPluginMenu();

        if (Tools::getValue('configure')) {
            // nothing;
        }
        */

        parent::initContent();
    }

    public function _renderForm()
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Product'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Location'),
                    'name' => 'location',
                    'required' => true,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        return parent::renderForm();
    }

    protected function response($data)
    {
        header('Content-Type: application/json');
        ob_clean();
        echo json_encode($data);
        exit;
    }

    public function ajaxProcessPluginCallback()
    {
        $plugin_name = Tools::getValue('plugin');
        $callback = Tools::getValue('callback_method');
        $params = Tools::getValue('params');

        $pluginClass = new MpSoft\MpMassImport\Plugins\Plugin($this->module);
        $plugin = $pluginClass->loadPlugin($plugin_name);
        $content = $plugin->callBack($callback, $params);
        $this->response(['content' => $content]);
    }
}