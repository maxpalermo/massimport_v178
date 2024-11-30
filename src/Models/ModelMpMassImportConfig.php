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

namespace MpSoft\MpMassImport\Models;

use MpSoft\MpMassImport\Helpers\WgetPHP;

class ModelMpMassImportConfig extends \ObjectModel
{
    public $type;
    public $url;
    public $file_name;
    public $file_extract_path;
    public $name;
    public $divider;
    public $category_divider;
    public $stock_min;
    public $id_category_default;
    public $headers;
    public $categories;
    public $surcharge;
    public $id_supplier;
    public $id_employee;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'mpmassimport_config',
        'primary' => 'id_mpmassimport_config',
        'fields' => [
            'type' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true,
            ],
            'url' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isUrl',
                'required' => false,
            ],
            'file_name' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
                'required' => false,
            ],
            'file_extract_path' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
                'required' => false,
            ],
            'name' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true,
            ],
            'divider' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true,
            ],
            'category_divider' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false,
            ],
            'stock_min' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false,
            ],
            'id_category_default' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false,
            ],
            'headers' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
                'required' => true,
            ],
            'categories' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
                'required' => true,
            ],
            'surcharge' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
                'required' => false,
            ],
            'id_employee' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ],
            'id_supplier' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false,
            ],
            'date_add' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'required' => true,
            ],
            'date_upd' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'required' => true,
            ],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        if ($this->headers) {
            $this->headers = json_decode($this->headers, true);
        }

        if ($this->categories) {
            $this->categories = json_decode($this->categories, true);
        }

        if ($this->surcharge) {
            $this->surcharge = json_decode($this->surcharge, true);
        }
    }

    public static function getConfigByName($name)
    {
        $sql = new \DbQuery();
        $sql->select(self::$definition['primary']);
        $sql->from(self::$definition['table']);
        $sql->where('name like \'' . pSQL($name) . '\'');

        $id = (int) \Db::getInstance()->getValue($sql);
        if ($id) {
            return new self($id);
        }

        return false;
    }

    public function getConfig()
    {
        $result = [
            'id_template' => $this->id,
            'type' => $this->type,
            'url' => $this->url,
            'file_name' => $this->file_name,
            'file_extract_path' => $this->file_extract_path,
            'name' => $this->name,
            'csv_divider' => $this->divider,
            'category_divider' => $this->category_divider,
            'stock_min' => (int) $this->stock_min,
            'id_category_default' => (int) $this->id_category_default ?: (int) \Configuration::get('PS_HOME_CATEGORY'),
            'headers' => $this->headers,
            'categories' => $this->categories,
            'surcharge' => $this->surcharge,
            'id_supplier' => (int) $this->id_supplier,
            'id_employee' => (int) $this->id_employee,
            'date_add' => $this->date_add,
            'date_upd' => $this->date_upd,
        ];

        return $result;
    }

    public function add($autodate = true, $nullValues = false)
    {
        if (is_array($this->headers)) {
            $this->headers = json_encode($this->headers);
        }

        if (is_array($this->categories)) {
            $this->categories = json_encode($this->categories);
        }

        if (is_array($this->surcharge)) {
            $this->surcharge = json_encode($this->surcharge);
        }

        return parent::add($autodate, $nullValues);
    }

    public function update($nullValues = false)
    {
        if (is_array($this->headers)) {
            $this->headers = json_encode($this->headers);
        }

        if (is_array($this->categories)) {
            $this->categories = json_encode($this->categories);
        }

        if (is_array($this->surcharge)) {
            $this->surcharge = json_encode($this->surcharge);
        }

        return parent::update($nullValues);
    }

    public static function getConfigs()
    {
        $sql = new \DbQuery();
        $sql->select(self::$definition['primary']);
        $sql->select('name');
        $sql->from(self::$definition['table']);
        $sql->orderBy('name');

        $ids = \Db::getInstance()->executeS($sql);
        $configs = [];
        foreach ($ids as $id) {
            $configs[] = [
                'id' => $id[self::$definition['primary']],
                'name' => $id['name'],
            ];
        }

        return $configs;
    }

    public static function getConfigsByIdSupplier($id_supplier)
    {
        $sql = new \DbQuery();
        $sql->select(self::$definition['primary']);
        $sql->select('name');
        $sql->from(self::$definition['table']);
        $sql->where('id_supplier = ' . (int) $id_supplier);
        $sql->orderBy('name');

        $ids = \Db::getInstance()->executeS($sql);
        $configs = [];
        foreach ($ids as $id) {
            $configs[] = [
                'id' => $id[self::$definition['primary']],
                'name' => $id['name'],
            ];
        }

        return $configs;
    }

    public function getContentFromUrl($url = null, $filename = null, $file_extract_path = null)
    {
        if (!$url) {
            $url = trim($this->url);
        }

        if (!$filename) {
            $filename = trim($this->filename);
        }

        if (!$file_extract_path) {
            $file_extract_path = trim($this->file_extract_path);
            if ($file_extract_path && !preg_match('/\/$/', $file_extract_path)) {
                $file_extract_path .= '/';
            }
        }

        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $uploadDir = _PS_UPLOAD_DIR_ . 'mpmassimport/';
            $filePath = $uploadDir . $filename;

            try {
                $body = '';
                $class = new WgetPHP();
                $response = $class->get($url, $filePath);

                if (!$response) {
                    throw new \Exception("Error parsing URL: $url");
                }
            } catch (\Throwable $th) {
                $command = 'wget -q -O ' . escapeshellarg($filePath) . ' ' . escapeshellarg($url);
                exec($command, $output, $fileContent);
                if ($fileContent !== 0) {
                    return false;
                }
            }

            if (file_exists($filePath)) {
                if (mime_content_type($filePath) === 'application/zip') {
                    $zip = new \ZipArchive();
                    if ($zip->open($filePath) === true) {
                        $zip->extractTo($uploadDir);
                        $zip->close();
                        unlink($filePath); // Remove the zip file after extraction
                        $zipPath = $uploadDir . $file_extract_path;
                        $extractedFiles = scandir($zipPath, SCANDIR_SORT_ASCENDING);
                        $files = [];
                        foreach ($extractedFiles as $file) {
                            if ($file !== '.' && $file !== '..') {
                                $mime = mime_content_type($zipPath . $file);
                                switch ($mime) {
                                    case 'text/csv':
                                    case 'text/plain':
                                    case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                                        $files[] = $zipPath . $file;

                                        break;
                                }
                            }
                        }

                        usort($files, function ($a, $b) {
                            return filemtime($b) - filemtime($a);
                        });

                        // Prendo il file più nuovo
                        $path = reset($files);

                        return file_get_contents($path);
                    } else {
                        throw new \Exception('Impossibile estrarre il file zip.');
                    }
                }

                return file_get_contents($filePath);
            }

            return false;
        } else {
            throw new \Exception('URL non valido.');
        }
    }
}
