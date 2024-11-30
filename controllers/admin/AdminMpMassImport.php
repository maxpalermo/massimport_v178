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

use MpSoft\MpMassImport\Helpers\CsvImporter;
use MpSoft\MpMassImport\Helpers\HeaderProduct;
use MpSoft\MpMassImport\Helpers\ImportCSV;
use MpSoft\MpMassImport\Helpers\InsertProduct;
use MpSoft\MpMassImport\Models\ModelMpMassImportProduct;

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
        $this->addCSS($this->module->getLocalPath() . 'views/css/toast.css', 'all', 1001);
        $this->addCSS($this->module->getLocalPath() . 'views/css/style.css', 'all', 10000);
        $this->addJS($this->module->getLocalPath() . 'views/js/datatables.min.js');
        $this->addJS($this->module->getLocalPath() . 'views/js/toast.js');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('fetch')) {
            $action = 'fetch' . Tools::ucfirst(Tools::getValue('fetch'));
            if (method_exists($this, $action)) {
                $this->response($this->{$action}());
            }
        }

        return parent::postProcess();
    }

    public function initContent()
    {
        $configuration = Configuration::get('MPMASSIMPORT_CSV_CONFIGURATION');
        if (!$configuration) {
            $configuration = [
                'csv_divider' => ';',
                'category_divider' => '',
                'id_supplier' => 0,
                'id_category_default' => Configuration::get('PS_HOME_CATEGORY'),
                'stock_min' => 0,
            ];
        } else {
            $configuration = json_decode($configuration, true);
        }

        $tpl_path = $this->module->getLocalPath() . 'views/templates/admin/main.tpl';
        $tpl = $this->context->smarty->createTemplate($tpl_path, $this->context->smarty);
        $tpl->assign([
            'module' => $this->module,
            'prodotti' => [],
            'controller_name' => $this->context->link->getAdminLink($this->controller_name),
            'templates' => MpSoft\MpMassImport\Models\ModelMpMassImportConfig::getConfigs(),
            'suppliers' => Supplier::getSuppliers(false, $this->context->language->id),
            'SITE_URL' => Tools::getShopDomainSsl() . __PS_BASE_URI__,
            'categories' => json_encode(Category::getSimpleCategories($this->context->language->id)),
            'configuration' => $configuration,
            'MODULE_VIEWS' => $this->module->getLocalPath() . 'views/',
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

    public function ajaxProcessReadCsv()
    {
        $file = Tools::fileAttachment('file-upload');

        if ($file['error']) {
            $this->response(['error' => $this->module->l('Errore durante la lettura del file.')]);
        }

        $class = new MpSoft\MpMassImport\Helpers\ImportCSV();
        $result = $class->readCsvWithPhpOffice($file['tmp_name']);

        if ($result) {
            $this->response([
                'error' => false,
                'headers' => $class->getHeader(),
                'body' => $class->getBody(),
            ]);
        }

        $this->response(['error' => $this->module->l('Errore durante la lettura del file.')]);
    }

    public function ajaxProcessUpdateConfiguration()
    {
        $key = Tools::getValue('key');
        $value = Tools::getValue('value');

        \Configuration::updateValue($key, $value);

        $this->response([
            'error' => false,
            'title' => $this->module->l('Configurazione aggiornata'),
            'message' => $this->module->l('Configurazione aggiornata con successo'),
            'type' => 'success',
            'icon' => 'fa-info',
            'delay' => 3000,
        ]);
    }

    public function ajaxProcessGetTableProducts()
    {
        $values = Tools::getAllValues();

        $params = [
            'draw' => $values['draw'],
            'start' => $values['start'],
            'length' => $values['length'],
            'search' => isset($values['search']) ? $values['search'] : '',
            'order' => isset($values['order']) ? $values['order'] : [],
            'columns' => isset($values['columns']) ? $values['columns'] : [],
        ];

        $data = MpSoft\MpMassImport\Helpers\Datatable::getProductsRows($params);

        $this->response($data);
    }

    public function ajaxProcessImportFromFile()
    {
        $template = (int) Tools::getValue('template');
        $file = Tools::fileAttachment('file-upload', false);

        $template = new MpSoft\MpMassImport\Models\ModelMpMassImportConfig($template);
        if (!Validate::isLoadedObject($template)) {
            $this->response([
                'error' => true,
                'title' => $this->module->l('Errore'),
                'message' => $this->module->l('Template non trovato.'),
                'type' => 'danger',
                'icon' => 'fa-exclamation',
                'delay' => 3000,
            ]);
        }

        if ($file['error']) {
            $this->response([
                'error' => true,
                'title' => $this->module->l('Errore'),
                'message' => $this->module->l('Errore durante la lettura del file.'),
                'type' => 'danger',
                'icon' => 'fa-exclamation',
                'delay' => 3000,
            ]);
        }

        $importer = new CsvImporter($template, $file);
        $load = $importer->parseCsvData($template);
        if ($load) {
            $data = $importer->getDataCsv();
            $body = $data['body'];
            $inserted = ModelMpMassImportProduct::insert($body);
        }
        // $content = $importer->getContent();

        $this->response([
            'error' => false,
            'title' => $this->module->l('Importazione file'),
            'message' => sprintf($this->module->l('Importati %d prodotti su un totale di %d.'), $inserted['inserted'], $inserted['total']),
            'type' => 'info',
            'icon' => 'fa-info',
            'delay' => 3000,
            'content' => $body,
            'errors' => $inserted['errors'],
        ]);
    }

    public function processCreateSelectCategories()
    {
        $categories = Category::getSimpleCategories($this->context->language->id);

        $select = "<select name='prestashop_categories' id='prestashop_categories' class='form-control chosen'>";
        foreach ($categories as $key => $category) {
            $select .= "<option value='{$category['id_category']}'>{$category['name']}</option>";
        }
        $select .= '</select>';

        $this->response([
            'error' => false,
            'content' => $select,
        ]);
    }

    public function processGetHeader()
    {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);

        $header = new HeaderProduct($this->module, $data['header'], $data['delimiter']);
        $this->response([
            'error' => false,
            'header' => $header->getHeader(),
        ]);
    }

    public function processGetAllProducts()
    {
        $db = \Db::getInstance();
        $sql = new \DbQuery();
        $sql->select('id_mpmassimport_product')
            ->from('mpmassimport_product');

        $products = $db->executeS($sql);
        if ($products) {
            $products = array_column($products, 'id_mpmassimport_product');
        }

        $this->response([
            'error' => false,
            'products' => $products,
        ]);
    }

    public function fetchSaveConfiguration()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $config = new MpSoft\MpMassImport\Models\ModelMpMassImportConfig();
        $config->type = $data['type'];
        $config->url = isset($data['url']) ? $data['url'] : '';
        $config->file_name = isset($data['filename']) ? $data['filename'] : '';
        $config->file_extract_path = isset($data['file_extract_path']) ? $data['file_extract_path'] : '';
        $config->name = $data['name'];
        $config->divider = $data['csv_divider'];
        $config->category_divider = $data['category_divider'];
        $config->stock_min = (int) $data['stock_min'];
        $config->id_category_default = (int) $data['id_category_default'];
        $config->headers = $data['headers'];
        $config->categories = isset($data['categories']) ? $data['categories'] : [];
        $config->surcharge = isset($data['surcharge']) ? $data['surcharge'] : [];
        $config->id_supplier = (int) $data['id_supplier'];
        $config->id_employee = $this->context->employee->id;

        $res = $config->add();

        if ($res) {
            $this->response([
                'error' => false,
                'title' => $this->module->l('Configurazione salvata'),
                'message' => $this->module->l('Configurazione salvata con successo'),
                'type' => 'success',
                'icon' => 'fa-info',
                'delay' => 3000,
            ]);
        }

        $this->response([
            'error' => true,
            'title' => $this->module->l('Errore'),
            'message' => $this->module->l('Errore durante il salvataggio della configurazione'),
            'type' => 'danger',
            'icon' => 'fa-exclamation',
            'delay' => 5000,
        ]);
    }

    public function fetchGetTemplate()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int) $data['templateId'];
        $template = new MpSoft\MpMassImport\Models\ModelMpMassImportConfig($id);

        if (!Validate::isLoadedObject($template)) {
            $this->response([
                'error' => true,
                'title' => $this->module->l('Errore'),
                'message' => $this->module->l('Template non trovato.'),
                'type' => 'danger',
                'icon' => 'fa-exclamation',
                'delay' => 3000,
            ]);
        }

        $this->response([
            'error' => false,
            'title' => $this->module->l('Template'),
            'message' => $this->module->l('Template caricato.'),
            'type' => 'success',
            'icon' => 'fa-info',
            'delay' => 3000,
            'configuration' => $template->getConfig(),
        ]);
    }

    public function fetchParseFile()
    {
        $id_employee = \Context::getContext()->employee->id;
        $delimiter = \Tools::getValue('csv_divider', ';');
        $file = \Tools::fileAttachment('csvFile', false);

        if (!$file) {
            $this->response([
                'error' => false,
                'title' => $this->module->l('Leggi File CSV'),
                'message' => $this->module->l('File non trovato.'),
                'type' => 'danger',
                'icon' => 'fa-exclamation',
                'delay' => 3000,
            ]);
        }

        if ($file && $file['error']) {
            $this->response([
                'error' => false,
                'title' => $this->module->l('Leggi File CSV'),
                'message' => $this->module->l('Errore durante la lettura del file.'),
                'type' => 'danger',
                'icon' => 'fa-exclamation',
                'delay' => 3000,
            ]);
        }

        $old_files = glob(_PS_UPLOAD_DIR_ . 'mpmassimport/*');
        foreach ($old_files as $old_file) {
            if (is_file($old_file)) {
                unlink($old_file);
            }
        }

        $csv = new ImportCSV();
        // $res = $csv->readCsvWithPhpOffice($file['tmp_name'], $divider);
        $res = $csv->readCsv($file['tmp_name'], $delimiter);
        if ($res) {
            $path = _PS_UPLOAD_DIR_ . 'mpmassimport/';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $fileTmpJson = basename($file['tmp_name']) . '.json';
            $content = [
                'header' => $csv->getHeader(),
                'body' => $csv->getBody(),
            ];
            $put = file_put_contents($path . $fileTmpJson, json_encode($content));
            if (!$put) {
                $this->response([
                    'error' => false,
                    'title' => $this->module->l('Leggi File CSV'),
                    'message' => $this->module->l('Errore durante la scrittura del file.'),
                    'type' => 'danger',
                    'icon' => 'fa-exclamation',
                    'delay' => 3000,
                ]);
            }
            chmod($path . $fileTmpJson, 0777);

            \Configuration::updateValue('MPMASSIMPORT_CSV_FILE_' . $id_employee, $fileTmpJson);
        }

        $this->response([
            'error' => false,
            'title' => $this->module->l('Leggi File CSV'),
            'message' => $this->module->l('File letto con successo.'),
            'type' => 'success',
            'icon' => 'fa-info',
            'delay' => 3000,
            'header' => $csv->getHeader(),
            'body' => $csv->getPreviewBody(),
        ]);
    }

    public function processImportPrestashopCatalog()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $errors = [];
        $rows = $data['products'];

        foreach ($rows as $row) {
            $model = new ModelMpMassImportProduct((int) $row);
            if (!Validate::isLoadedObject($model)) {
                continue;
            }

            $content = $model->getContent();
            $product = new InsertProduct();
            $res = $product->insertProduct($content);
            if ((int) $res == 0) {
                $errors[] = $res;
            }
        }

        if ($errors) {
            $this->response([
                'error' => true,
                'title' => $this->module->l('Errore'),
                'message' => implode('<br>', $errors),
                'type' => 'danger',
                'icon' => 'fa-exclamation',
                'delay' => 3000,
            ]);
        }

        $this->response([
            'error' => false,
            'title' => $this->module->l('Importazione Catalogo'),
            'message' => $this->module->l('Catalogo importato con successo.'),
            'type' => 'success',
            'icon' => 'fa-info',
            'delay' => 3000,
        ]);
    }

    public function getTempFileCsv($ajax = true)
    {
        $id_employee = \Context::getContext()->employee->id;
        $file = \Configuration::get('MPMASSIMPORT_CSV_FILE_' . $id_employee);
        $path = _PS_UPLOAD_DIR_ . 'mpmassimport/' . $file;
        if (!file_exists($path)) {
            if ($ajax) {
                $this->response([
                    'error' => true,
                    'title' => $this->module->l('Errore'),
                    'message' => $this->module->l('File non trovato.'),
                    'type' => 'danger',
                    'icon' => 'fa-exclamation',
                    'delay' => 3000,
                ]);
            }

            return false;
        }

        $content = file_get_contents($path);
        $content = json_decode($content, true);

        if ($ajax) {
            $this->response([
                'error' => false,
                'header' => $content['header'],
                'body' => $content['body'],
            ]);
        }

        return $content;
    }

    public function processGetCsvHeader()
    {
        $content = $this->getTempFileCsv(false);
        if (!$content) {
            $this->response([
                'error' => true,
                'title' => $this->module->l('Errore'),
                'message' => $this->module->l('File non trovato.'),
                'type' => 'danger',
                'icon' => 'fa-exclamation',
                'delay' => 3000,
            ]);
        }
        $header = $content['header'];

        $this->response([
            'error' => false,
            'header' => $header,
        ]);
    }

    public function fetchGetCategoriesFromCsv()
    {
        $content = $this->getTempFileCsv(false);
        if (!$content) {
            $this->response([
                'error' => true,
                'title' => $this->module->l('Errore'),
                'message' => $this->module->l('File non trovato.'),
                'type' => 'danger',
                'icon' => 'fa-exclamation',
                'delay' => 3000,
            ]);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $index = $data['index'];
        $category_divider = $data['category_divider'];
        $header = $content['header'];
        $hasHeader = false;
        $headerKey = null;
        $categoryList = [];

        foreach ($header as $key => $value) {
            if ($key == $index) {
                $hasHeader = true;
                $headerKey = $value;

                break;
            }
        }

        if ($hasHeader) {
            foreach ($content['body'] as $key => $value) {
                if (!isset($value[$headerKey])) {
                    continue;
                }
                $item = $value[$headerKey];
                if ($category_divider) {
                    $categories = explode($category_divider, $item);
                } else {
                    $categories = $item;
                }
                if (is_array($categories)) {
                    foreach ($categories as $category) {
                        $categoryList[$category] = $category;
                    }
                } else {
                    $categoryList[$categories] = $categories;
                }
            }
            $categoryList = array_values($categoryList);
            asort($categoryList);
            $categoryList = array_values($categoryList);

            $this->response([
                'error' => false,
                'categories' => $categoryList,
                'title' => $this->module->l('Categorie'),
                'message' => $this->module->l('Elenco categorie completato.'),
                'type' => 'success',
                'icon' => 'fa-info',
                'delay' => 3000,
            ]);
        }

        $this->response([
            'error' => true,
            'title' => $this->module->l('Errore'),
            'message' => $this->module->l('Indice non trovato.'),
            'type' => 'danger',
            'icon' => 'fa-exclamation',
            'delay' => 3000,
        ]);
    }

    public function processGetProductColumnsSelect()
    {
        $columns = new MpSoft\MpMassImport\Helpers\ColumnsProduct($this->module);
        $this->response([
            'error' => false,
            'select' => $columns->getSelect(),
        ]);
    }

    public function downloadFileWithWget($url, $destination = false)
    {
        if (!$destination) {
            $result = file_get_contents($url);

            return $result;
        }

        $destination = _PS_UPLOAD_DIR_ . 'mpmassimport/' . $destination;

        $cmd = "wget -O $destination $url";
        $return = false;
        exec($cmd, $output, $return);

        return $return;
    }

    public function downloadFile($url, $destination = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);

        // Esegui la richiesta
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // Controlla se la richiesta è stata completata con successo
        if ($httpCode == 200) {
            if ($destination) {
                // Salva il contenuto del file
                file_put_contents($destination, $response);

                return true;
            } else {
                // ho verificato l'esisenza del file
                return true;
            }
        } else {
            return $httpCode;
        }
    }

    public function ajaxProcessSetJsonConfiguration()
    {
        $json = Tools::getValue('json');
        Configuration::updateValue('MPMASSIMPORT_CSV_CONFIGURATION', $json);

        $this->response([
            'error' => false,
            'title' => $this->module->l('Configurazione JSON'),
            'message' => $this->module->l('Configurazione JSON salvata con successo.'),
            'type' => 'success',
            'icon' => 'fa-info',
            'delay' => 3000,
        ]);
    }

    public function ajaxProcessDownloadUrl()
    {
        $url = Tools::getValue('url');
        $filename = Tools::getValue('fileName', basename($url));
        $file_extract_path = Tools::getValue('fileExtractPath', '');

        $config = new MpSoft\MpMassImport\Models\ModelMpMassImportConfig();
        $config->url = $url;
        $config->filename = $filename;
        $config->file_extract_path = $file_extract_path;

        $content = $config->getContentFromUrl();
        if ($content) {
            $this->response([
                'error' => false,
                'title' => $this->module->l('Download URL'),
                'message' => $this->module->l('File scaricato con successo.'),
                'type' => 'success',
                'icon' => 'fa-info',
                'delay' => 3000,
                'header' => $content['header'],
                'body' => $content['body'],
                'json_configuration' => Configuration::get('MPMASSIMPORT_CSV_CONFIGURATION'),
            ]);
        }

        $this->response([
            'error' => true,
            'title' => $this->module->l('Download URL'),
            'message' => $this->module->l('Errore durante il download del file.'),
            'type' => 'danger',
            'icon' => 'fa-exclamation',
            'delay' => 3000,
        ]);
    }
}
