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

namespace MpSoft\MpMassImport\Helpers;

use MpSoft\MpMassImport\Models\ModelMpMassImportConfig;

class CsvImporter
{
    protected $config;
    protected $type;
    protected $config_header;
    protected $config_categories;
    protected $delimiter;
    protected $categoryDivider;
    protected $surcharge;
    protected $csv_header;
    protected $csv_body;
    protected $file;
    protected $content;
    protected $id_supplier;

    public function __construct(ModelMpMassImportConfig $config, array $file)
    {
        $this->config = $config;
        $this->file = $file;
        $this->content = '';
        $this->config_header = [];
        $this->csv_header = [];
        $this->csv_body = [];
        $this->id_supplier = (int) $config->id_supplier;
    }

    public function parseCsvData(ModelMpMassImportConfig $config = null)
    {
        if ($config) {
            $this->config = $config;
        }
        $this->parseConfig();

        return $this->readCsv();
    }

    protected function parseConfig()
    {
        $this->type = $this->config->type;
        $this->delimiter = $this->config->divider;
        $this->categoryDivider = $this->config->category_divider;

        foreach ($this->config->headers as $header) {
            $this->config_header[$header['header']] = $header;
        }

        foreach ($this->config->categories as $category) {
            $this->config_categories[$category['category']] = $category;
        }

        $this->surcharge = $this->config->surcharge;
    }

    public function readCsv()
    {
        $module = \Module::getInstanceByName('mpmassimport');
        $columnsProduct = new ColumnsProduct($module);

        $handle = fopen($this->file['tmp_name'], 'r');
        if ($handle === false) {
            return false;
        }

        $header = null;
        $data = [];
        $parsed = [];
        while (($row = fgetcsv($handle, 0, $this->delimiter)) !== false) {
            if ($header === null) {
                $header = $row;

                continue;
            }

            $line = array_combine($header, $row);
            $line = array_map('trim', $line);
            $line = array_map('utf8_encode', $line);

            $columnsParse = $columnsProduct->parseRow($line, $this->config);
            if ($columnsParse) {
                $parsed[] = $columnsParse;
            }

            $data[] = array_combine($header, $row);
        }

        fclose($handle);

        $this->csv_header = $header;
        $this->csv_body = $parsed;

        return true;
    }

    public function getDataCsv()
    {
        return [
            'header' => $this->csv_header,
            'body' => $this->csv_body,
        ];
    }

    public function parseCsv()
    {
        $row = [];
        $config_header = $this->config_header;
        $config_categories = $this->config_categories;

        $this->content = [];
        foreach ($this->csv_body as $key => $row) {
            $row = array_map('trim', $row);
            $row = array_map('utf8_encode', $row);

            $search_fields = [
                'id_supplier',
                'reference',
                'name',
                'category',
                'images',
                'price',
                'stock',
            ];

            $content = [
                'id_supplier' => $this->id_supplier,
                'reference' => '',
                'name' => '',
                'category' => '',
                'images' => '',
                'price' => 0,
                'stock' => 0,
                'content_json' => json_encode($row),
            ];

            $json_row = [];

            foreach ($row as $key => $value) {
                if (isset($config_header[$key])) {
                    if ($config_header[$key]['skip'] != 'false') {
                        continue;
                    }

                    if ($config_header[$key]['pattern']) {
                        $value = trim($value);
                        $pattern = explode('==>', $config_header[$key]['pattern']);
                        $pattern = array_map('trim', $pattern);
                        if (count($pattern) == 1) {
                            $matches = [];
                            $res_match = preg_match("{$pattern[0]}/i", $value, $matches);
                            if ($res_match) {
                                $value = $matches[1];
                            }
                        } elseif (count($pattern) == 2) {
                            $search = "/{$pattern[0]}/i";
                            $replace = $pattern[1];
                            $value = preg_replace($search, $replace, $value);
                        }
                    }

                    $json_row[$config_header[$key]['select']] = $value;

                    if (in_array($config_header[$key]['select'], $search_fields)) {
                        $content[$config_header[$key]['select']] = $value;
                    }

                    continue;
                }
            }

            $id_category = $this->parseCategory($content['category'], $config_categories);
            if ($id_category === false) {
                continue;
            }

            $content['category'] = $id_category;
            $content['content_json'] = json_encode($json_row);
            $content['original_price'] = $content['price'];
            $content['surcharge'] = $this->surcharge;
            $content['price'] = 0;

            $this->content[] = $content;
        }

        return true;
    }

    protected function parseCategory($category, $config_categories)
    {
        $categories = explode($this->categoryDivider, $category);
        $categories = array_map('trim', $categories);

        $category = [];
        foreach ($categories as $cat) {
            if (isset($config_categories[$cat])) {
                if ($config_categories[$cat]['skip'] != 'false') {
                    return false;
                }

                return (int) $config_categories[$cat]['select'];
            }
        }

        return false;
    }

    public function getContent()
    {
        return $this->content;
    }
}
