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

class ColumnsProduct
{
    protected $columns;
    protected $module;

    public function __construct($module)
    {
        $this->module = $module;

        $this->columns = [
            ['id' => 'id_product', 'text' => 'ID Prodotto'],
            ['id' => 'name', 'text' => 'Nome Prodotto'],
            ['id' => 'reference', 'text' => 'Riferimento'],
            ['id' => 'ean13', 'text' => 'EAN13'],
            ['id' => 'upc', 'text' => 'UPC'],
            ['id' => 'wholesale_price', 'text' => 'Prezzo Acquisto'],
            ['id' => 'price', 'text' => 'Prezzo Vendita'],
            ['id' => 'quantity', 'text' => 'Quantità'],
            ['id' => 'category', 'text' => 'Categoria'],
            ['id' => 'description', 'text' => 'Descrizione'],
            ['id' => 'short_description', 'text' => 'Descrizione Breve'],
            ['id' => 'meta_title', 'text' => 'Meta Titolo'],
            ['id' => 'meta_description', 'text' => 'Meta Descrizione'],
            ['id' => 'meta_keywords', 'text' => 'Meta Keywords'],
            ['id' => 'link_rewrite', 'text' => 'Link Rewrite'],
            ['id' => 'active', 'text' => 'Attivo'],
            ['id' => 'available_for_order', 'text' => 'Disponibile per Ordine'],
            ['id' => 'condition', 'text' => 'Condizione'],
            ['id' => 'weight', 'text' => 'Peso'],
            ['id' => 'width', 'text' => 'Larghezza'],
            ['id' => 'height', 'text' => 'Altezza'],
            ['id' => 'depth', 'text' => 'Profondità'],
            ['id' => 'additional_shipping_cost', 'text' => 'Costo Spedizione Aggiuntivo'],
            ['id' => 'unity', 'text' => 'Unità di Misura'],
            ['id' => 'minimal_quantity', 'text' => 'Quantità Minima'],
            ['id' => 'available_date', 'text' => 'Data Disponibilità'],
            ['id' => 'images', 'text' => 'Immagini'],
            ['id' => 'features', 'text' => 'Caratteristiche'],
            ['id' => 'attributes', 'text' => 'Attributi'],
            ['id' => 'online_only', 'text' => 'Solo Online'],
            ['id' => 'customizable', 'text' => 'Personalizzabile'],
            ['id' => 'uploadable_files', 'text' => 'File Caricabili'],
            ['id' => 'text_fields', 'text' => 'Campi Testo'],
            ['id' => 'out_of_stock', 'text' => 'Esaurito'],
            ['id' => 'quantity_discount', 'text' => 'Sconto Quantità'],
            ['id' => 'customization_required', 'text' => 'Personalizzazione Richiesta'],
            ['id' => 'quantity', 'text' => 'Quantità di magazzino'],
            ['id' => 'supplier_reference', 'text' => 'Codice Fornitore'],
            ['id' => 'attachment', 'text' => 'Allegato'],
            ['id' => 'supplier', 'text' => 'Fornitore'],
            ['id' => 'manufacturer', 'text' => 'Produttore'],
            ['id' => 'brand', 'text' => 'Caratteristica - Marchio'],
        ];
    }

    public function getColumns($sort = true)
    {
        if ($sort) {
            usort($this->columns, function ($a, $b) {
                return strcmp($a['text'], $b['text']);
            });
        }

        return $this->columns;
    }

    public function getSelect()
    {
        $select = '<select class="form-control csv-categories chosen"><option value="">Seleziona...</option>';
        $columns = $this->getColumns();
        foreach ($columns as $column) {
            $select .= "<option value=\"{$column['id']}\">{$column['text']}</option>";
        }
        $select .= '</select>';

        return $select;
    }

    public function parseRow(array $row, ModelMpMassImportConfig $config)
    {
        $categories = $config->categories;
        $surcharge = $config->surcharge;
        $categoryDivider = $config->category_divider;
        $json_row = [];

        $row = $this->reKey($row, $config);

        foreach ($row as $key => $value) {
            /**
             * in key ho il nome della colonna
             * 
             * in value ho un array:
             *  - value: il valore
             *  - pattern: il pattern da applicare
             */
            $pattern = $value['pattern'];
            $value = $value['value'];

            if ($pattern) {
                $value = trim($value);
                $pattern = explode('==>', $pattern);
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

            /**
             * Se la colonna è category, devo cercare l'id corrispondente
             */
            if ($key == 'category') {
                $id_category = $this->parseCategory($value, $categories, $categoryDivider);
                if ($id_category === false) {
                    continue;
                }

                $json_row['category'] = $id_category['category'];
                $json_row['id_category'] = (int) $id_category['id_category'];

                continue;
            }

            /**
             * Se la colonna è price, devo impostare l'eventuale ricarico
             */
            if ($key == 'price') {
                $value = (float) $value;
                $json_row['price_original'] = $value;

                if (!is_array($surcharge)) {
                    $surcharge = json_decode($surcharge, true);
                }

                if (is_array($surcharge)) {
                    foreach ($surcharge as $s) {
                        if ($s['type'] == 'percentuale') {
                            $value += $value * ((float) $s['value']) / 100;
                        } else {
                            $value += $s['value'];
                        }
                    }
                }

                $json_row['surcharge'] = $surcharge;
                $json_row['price'] = $value;

                continue;
            }

            $json_row[$key] = $value;
        }

        $json_row = $this->finalizeRow($json_row, $config);

        return $json_row;
    }

    protected function parseCategory($category, $config_categories, $categoryDivider)
    {
        if ($categoryDivider) {
            $categories = explode($categoryDivider, $category);
        } else {
            $categories = [$category];
        }
        $categories = array_map('trim', $categories);

        $category = [];
        $lastCategory = end($categories);

        foreach ($config_categories as $config_category) {
            if ($config_category['category'] == $lastCategory) {
                $category = $config_category;
                if ($category['skip'] == 'true') {
                    return false;
                }

                return [
                    'id_category' => $category['select'],
                    'category' => $lastCategory,
                ];
            }
        }

        return false;
    }

    protected function finalizeRow($row, ModelMpMassImportConfig $config)
    {
        $new_row = [
            'id_supplier' => (int) $config->id_supplier,
            'reference' => $row['reference'],
            'name' => $row['name'],
            'category' => $row['category'],
            'id_category' => $row['id_category'],
            'images' => $row['images'],
            'price_original' => $row['price_original'],
            'surcharge' => $row['surcharge'],
            'price' => $row['price'],
            'quantity' => (int) $row['quantity'],
            'content_json' => json_encode($row),
        ];

        return $new_row;
    }

    protected function reKey(array $row, ModelMpMassImportConfig $config)
    {
        $reKey = [];
        $headers = $config->headers;
        $new_row = [];

        foreach ($row as $key => $value) {
            foreach ($headers as $header) {
                if ($header['header'] == $key) {
                    if ($header['skip'] == 'true') {
                        break;
                    }
                    $new_row[$header['select']] = [
                        'value' => $value,
                        'pattern' => $header['pattern'],
                    ];

                    break;
                }
            }
        }

        return $new_row;
    }
}
