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

class InsertProduct
{
    protected $context;
    protected $module;

    public function __construct()
    {
        $this->context = \Context::getContext();
        $this->module = \Module::getInstanceByName('mpmassimport');
    }

    public static function getMandatoryiIelds()
    {
        $fields = [
            'id_supplier',
            'reference',
            'name',
            'category',
            'price',
            'quantity',
            'id_tax_rules_group',
        ];

        return $fields;
    }

    public static function getProductFields()
    {
        $fields = [
            'id_product', 'id_supplier', 'id_manufacturer', 'id_category_default', 'id_shop_default',
            'id_tax_rules_group', 'on_sale', 'online_only', 'ean13', 'isbn', 'upc', 'mpn', 'ecotax',
            'quantity', 'minimal_quantity', 'price', 'wholesale_price', 'unity', 'unit_price_ratio',
            'additional_shipping_cost', 'reference', 'supplier_reference', 'location', 'width', 'height',
            'depth', 'weight', 'out_of_stock', 'quantity_discount', 'customizable', 'uploadable_files',
            'text_fields', 'active', 'redirect_type', 'available_for_order', 'available_date', 'condition',
            'show_price', 'indexed', 'visibility', 'cache_default_attribute', 'advanced_stock_management',
            'date_add', 'date_upd', 'pack_stock_type', 'state', 'additional_delivery_times', 'id_shop_list',
            'description', 'description_short', 'link_rewrite', 'meta_description', 'meta_keywords',
            'meta_title', 'name', 'available_now', 'available_later', 'delivery_in_stock', 'delivery_out_stock',
        ];

        return $fields;
    }

    public static function prepareRow($data)
    {
        $module = \Module::getInstanceByName('mpmassimport');
        $languages = \Context::getContext()->language->getLanguages();
        $fields = self::getProductFields();
        $row = [];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $row[$field] = $data[$field];
            } else {
                $row[$field] = null;
            }
        }

        if (!$row['reference']) {
            return $module->l('Il riferimento prodotto è richiesto');
        }

        if (!$row['price']) {
            return $module->l('Il prezzo prodotto è richiesto');
        }

        if (!$row['quantity']) {
            $row['quantity'] = 0;
        }

        if (!$row['id_tax_rules_group']) {
            $row['id_tax_rules_group'] = 17;
            // return $module->l('Il gruppo di regole fiscali è richiesto');
        }

        if (!$row['unity']) {
            $row['unity'] = 'pz';
        }

        if (!$row['id_shop_default']) {
            $row['id_shop_default'] = 1;
        }

        if (!$row['id_manufacturer']) {
            $row['id_manufacturer'] = 0;
        }

        if (!$row['id_supplier']) {
            $row['id_supplier'] = 0;
        }

        if (!$row['id_shop_list']) {
            $row['id_shop_list'] = [1];
        }

        if (!$row['id_tax_rules_group']) {
            $row['id_tax_rules_group'] = 0;
        }

        if (!$row['on_sale']) {
            $row['on_sale'] = 0;
        }

        if (!$row['online_only']) {
            $row['online_only'] = 0;
        }

        if (!$row['ean13']) {
            $row['ean13'] = '';
        }

        if (!$row['isbn']) {
            $row['isbn'] = '';
        }

        if (!$row['upc']) {
            $row['upc'] = '';
        }

        if (!$row['mpn']) {
            $row['mpn'] = '';
        }

        if (!$row['ecotax']) {
            $row['ecotax'] = 0;
        }

        if (!$row['minimal_quantity']) {
            $row['minimal_quantity'] = 1;
        }

        if (!$row['wholesale_price']) {
            $row['wholesale_price'] = 0;
        }

        if (!$row['unit_price_ratio']) {
            $row['unit_price_ratio'] = 0;
        }

        if (!$row['additional_shipping_cost']) {
            $row['additional_shipping_cost'] = 0;
        }

        if (!$row['location']) {
            $row['location'] = '';
        }

        if (!$row['width']) {
            $row['width'] = 0;
        }

        if (!$row['height']) {
            $row['height'] = 0;
        }

        if (!$row['depth']) {
            $row['depth'] = 0;
        }

        if (!$row['weight']) {
            $row['weight'] = 0;
        }

        if (!$row['out_of_stock']) {
            $row['out_of_stock'] = 2;
        }

        if (!$row['quantity_discount']) {
            $row['quantity_discount'] = 0;
        }

        if (!$row['customizable']) {
            $row['customizable'] = 0;
        }

        if (!$row['uploadable_files']) {
            $row['uploadable_files'] = 0;
        }

        if (!$row['text_fields']) {
            $row['text_fields'] = 0;
        }

        if (!$row['active']) {
            $row['active'] = 1;
        }

        if (!$row['redirect_type']) {
            $row['redirect_type'] = '404';
        }

        if (!$row['available_for_order']) {
            $row['available_for_order'] = 1;
        }

        if (!$row['available_date']) {
            $row['available_date'] = date('Y-m-d H:i:s');
        }

        if (!$row['condition']) {
            $row['condition'] = 'new';
        }

        if (!$row['show_price']) {
            $row['show_price'] = 1;
        }

        if (!$row['indexed']) {
            $row['indexed'] = 1;
        }

        if (!$row['visibility']) {
            $row['visibility'] = 'both';
        }

        if (!$row['cache_default_attribute']) {
            $row['cache_default_attribute'] = 0;
        }

        if (!$row['advanced_stock_management']) {
            $row['advanced_stock_management'] = 0;
        }

        if (!$row['date_add']) {
            $row['date_add'] = date('Y-m-d H:i:s');
        }

        if (!$row['date_upd']) {
            $row['date_upd'] = date('Y-m-d H:i:s');
        }

        if (!$row['pack_stock_type']) {
            $row['pack_stock_type'] = 3;
        }

        if (!$row['state']) {
            $row['state'] = 1;
        }

        if (!$row['additional_delivery_times']) {
            $row['additional_delivery_times'] = 0;
        }

        if (!$row['name']) {
            return $module->l('Il nome prodotto è richiesto');
        }

        if (isset($row['category']) && $row['category']) {
            if (!is_array($row['category'])) {
                $categories = explode(',', $row['category']);
                $categories = array_map('trim', $categories);
                $categories = array_filter($categories);
                $categories = array_unique($categories);
                $categories = array_values($categories);
                $categories = array_map('intval', $categories);
            }
            if (!$row['id_category_default']) {
                $row['id_category_default'] = reset($categories);
            }
            $row['categories'] = $categories;
        } else {
            if (isset($data['id_category'])) {
                $row['id_category_default'] = $data['id_category'];
                $row['categories'] = [$row['id_category_default']];
            } else {
                return $module->l('La categoria prodotto è richiesta');
            }
        }

        if ($row['description']) {
            foreach ($languages as $language) {
                $row['description'][$language['id_lang']] = $row['description'];
            }
        } else {
            $row['description'] = [];
            foreach ($languages as $language) {
                $row['description'][$language['id_lang']] = '';
            }
        }

        if ($row['description_short']) {
            foreach ($languages as $language) {
                $row['description_short'][$language['id_lang']] = $row['description_short'];
            }
        } else {
            $row['description_short'] = [];
            foreach ($languages as $language) {
                $row['description_short'][$language['id_lang']] = '';
            }
        }

        if ($row['meta_description']) {
            foreach ($languages as $language) {
                $row['meta_description'][$language['id_lang']] = $row['meta_description'];
            }
        } else {
            $row['meta_description'] = [];
            foreach ($languages as $language) {
                $row['meta_description'][$language['id_lang']] = '';
            }
        }

        if ($row['meta_keywords']) {
            foreach ($languages as $language) {
                $row['meta_keywords'][$language['id_lang']] = $row['meta_keywords'];
            }
        } else {
            $row['meta_keywords'] = [];
            foreach ($languages as $language) {
                $row['meta_keywords'][$language['id_lang']] = '';
            }
        }

        if ($row['meta_title']) {
            foreach ($languages as $language) {
                $row['meta_title'][$language['id_lang']] = $row['meta_title'];
            }
        } else {
            $row['meta_title'] = [];
            foreach ($languages as $language) {
                $row['meta_title'][$language['id_lang']] = '';
            }
        }

        if ($row['available_now']) {
            foreach ($languages as $language) {
                $row['available_now'][$language['id_lang']] = $row['available_now'];
            }
        } else {
            $row['available_now'] = [];
            foreach ($languages as $language) {
                $row['available_now'][$language['id_lang']] = '';
            }
        }

        if ($row['available_later']) {
            foreach ($languages as $language) {
                $row['available_later'][$language['id_lang']] = $row['available_later'];
            }
        } else {
            $row['available_later'] = [];
            foreach ($languages as $language) {
                $row['available_later'][$language['id_lang']] = '';
            }
        }

        if ($row['delivery_in_stock']) {
            foreach ($languages as $language) {
                $row['delivery_in_stock'][$language['id_lang']] = $row['delivery_in_stock'];
            }
        } else {
            $row['delivery_in_stock'] = [];
            foreach ($languages as $language) {
                $row['delivery_in_stock'][$language['id_lang']] = '';
            }
        }

        if ($row['delivery_out_stock']) {
            foreach ($languages as $language) {
                $row['delivery_out_stock'][$language['id_lang']] = $row['delivery_out_stock'];
            }
        } else {
            $row['delivery_out_stock'] = [];
            foreach ($languages as $language) {
                $row['delivery_out_stock'][$language['id_lang']] = '';
            }
        }

        if (isset($data['images'])) {
            $row['images'] = $data['images'];
        }

        if (isset($data['attributes'])) {
            $row['attributes'] = $data['attributes'];
        }

        if (isset($data['features'])) {
            $row['features'] = $data['features'];
        }

        return $row;
    }

    /**
     * Insert a random product in the database.
     *
     * @return int|string The ID of the inserted product.or the error message
     */
    public function insertProduct($data)
    {
        $module = $this->module;
        $data = self::prepareRow($data);
        if (is_string($data)) {
            return $data;
        }

        if ($data['ean13']) {
            $id_product = (int) \Product::getIdByEan13($data['ean13']);
        } elseif ($data['reference']) {
            $id_product = (int) \Product::getIdByReference($data['reference']);
        } else {
            return $module->l('Il riferimento o EAN13 prodotto è richiesto');
        }

        $product = new \Product($id_product);
        $product->hydrate($data);

        try {
            $product->save();
            $id_product = $product->id;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }

        if ($id_product) {
            $product->addToCategories($data['categories']);
            if (isset($data['attributes']) && is_array($data['attributes'])) {
                $this->insertProductAttributes($id_product, $data['attributes']);
            }
            if (isset($data['features']) && is_array($data['features'])) {
                $this->insertProductFeatures($id_product, $data['features']);
            }

            if (isset($data['images']) && $data['images']) {
                if (!is_array($data['images'])) {
                    $data['images'] = [$data['images']];
                }
                $this->insertProductImages($id_product, $data['images']);
            } else {
                $this->insertProductImages($id_product);
            }

            if (isset($data['quantity'])) {
                \StockAvailable::setQuantity($id_product, 0, (int) $data['quantity']);
                if ($data['quantity'] == 0) {
                    $product->active = 0;
                    $product->save();
                }
            }
        }

        return $id_product;
    }

    public function insertProductAttributes($id_product, $attributes)
    {
        return true;
    }

    public function insertProductFeatures($id_product, $features)
    {
        return true;
    }

    public function insertProductImages($id_product, $images = [])
    {
        if (!is_array($images) || empty($images)) {
            $images = [
                'https://via.placeholder.com/300',
            ];
        }
        foreach ($images as $key => $image_link) {
            $content = $this->getImageFromLink($image_link);
            $this->insertImage($id_product, $content, $key == 0);
        }
    }

    public function getImageFromLink($image_link)
    {
        $content = @file_get_contents($image_link);

        if ($content) {
            $image_info = getimagesizefromstring($content);
            if ($image_info) {
                $mime_type = $image_info['mime'];

                return ['content' => $content, 'mime_type' => $mime_type];
            }
        }

        return false;
    }

    public function insertImage($id_product, $content, $cover = false)
    {
        $image = new \Image();
        $image->id_product = $id_product;
        $image->position = \Image::getHighestPosition($id_product) + 1;
        $image->cover = $cover;

        try {
            $image->add();
            $image->associateTo($id_product);

            $image_path = $image->getPathForCreation();
            $extension = '';
            $mime_type = $content['mime_type'];

            switch ($mime_type) {
                case 'image/jpeg':
                    $extension = 'jpg';

                    break;
                case 'image/png':
                    $extension = 'png';

                    break;
                case 'image/gif':
                    $extension = 'gif';

                    break;
                default:
                    throw new \Exception('Unsupported image type');
            }

            $image_path .= '.' . $extension;
            $image_data = $content['content'];

            $image->createImgFolder();

            file_put_contents($image_path, $image_data);
            chmod($image_path, 0777);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }

        return $image->id;
    }
}
