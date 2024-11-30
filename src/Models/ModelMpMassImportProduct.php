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

class ModelMpMassImportProduct extends \ObjectModel
{
    public $id_supplier;
    public $reference;
    public $name;
    public $category;
    public $id_category;
    public $images;
    public $price_original;
    public $surcharge;
    public $price;
    public $quantity;
    public $content_json;
    public $date_add;
    public $date_upd;

    /** @var ModelMpMassImportConfig */
    protected $config;

    public static $definition = [
        'table' => 'mpmassimport_product',
        'primary' => 'id_mpmassimport_product',
        'fields' => [
            'id_supplier' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
            ],
            'reference' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true,
            ],
            'name' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true,
            ],
            'category' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
                'required' => true,
            ],
            'id_category' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ],
            'images' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true,
            ],
            'price_original' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat',
                'required' => true,
            ],
            'surcharge' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
                'required' => true,
            ],
            'price' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat',
                'required' => true,
            ],
            'quantity' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
            ],
            'content_json' => [
                'type' => self::TYPE_HTML,
                'validate' => 'isAnything',
                'required' => true,
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
        if ($this->id) {
            $this->content_json = json_decode($this->content_json, true);
            $this->surcharge = json_decode($this->surcharge, true);
        }
    }

    public function setConfiguration(ModelMpMassImportConfig $config)
    {
        $this->config = $config;
    }

    public function add($autodate = true, $null_values = false)
    {
        if (is_array($this->content_json)) {
            $this->content_json = json_encode($this->content_json);
        }
        if (is_array($this->surcharge)) {
            $this->surcharge = json_encode($this->surcharge);
        }

        return parent::add($autodate, $null_values);
    }

    public function update($null_values = false)
    {
        if (is_array($this->content_json)) {
            $this->content_json = json_encode($this->content_json);
        }
        if (is_array($this->surcharge)) {
            $this->surcharge = json_encode($this->surcharge);
        }

        return parent::update($null_values);
    }

    public function save($null_values = false, $auto_date = true)
    {
        if (is_array($this->content_json)) {
            $this->content_json = json_encode($this->content_json);
        }
        if (is_array($this->surcharge)) {
            $this->surcharge = json_encode($this->surcharge);
        }

        return parent::save($null_values, $auto_date);
    }

    public static function getProductByReference($reference)
    {
        $sql = new \DbQuery();
        $sql->select('id_mpmassimport_product');
        $sql->from(self::$definition['table']);
        $sql->where('reference = \'' . pSQL($reference) . '\'');

        $id = (int) \Db::getInstance()->getValue($sql);
        if ($id) {
            return new self($id);
        }

        return false;
    }

    public function getContent()
    {
        if (is_array($this->content_json)) {
            return $this->content_json;
        }

        $json = json_decode($this->content_json, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json;
        }

        return false;
    }

    public static function insert(array $data)
    {
        \Db::getInstance()->execute('TRUNCATE TABLE ' . _DB_PREFIX_ . self::$definition['table']);

        $errors = [];
        $inserted = 0;

        foreach ($data as $record) {
            $model = self::getProductByReference($record['reference']);
            if (!$model) {
                $model = new self();
            }

            $model->hydrate($record);

            try {
                $res = $model->save();
                if ($res) {
                    $inserted++;
                }
            } catch (\Throwable $th) {
                $errors[] = $th->getMessage();
            }
        }

        return [
            'inserted' => $inserted,
            'total' => count($data),
            'errors' => $errors,
        ];
    }
}
