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

class Datatable
{
    public static function getProductsRows($params)
    {
        // Database connection
        $db = \Db::getInstance();

        // Extract parameters
        $draw = isset($params['draw']) ? intval($params['draw']) : 0;
        $start = isset($params['start']) ? intval($params['start']) : 0;
        $length = isset($params['length']) ? intval($params['length']) : 10;

        // Build the query
        $query = new \DbQuery();
        $query->select('SQL_CALC_FOUND_ROWS a.*')
            ->from('mpmassimport_product', 'a');

        // External Category Name
        $id_lang = (int) \Context::getContext()->language->id;

        $query->leftJoin('category_lang', 'cl', "cl.id_category = a.id_category AND cl.id_lang = {$id_lang}");
        $query->select('cl.name as category_name');

        if (!empty($searchValue)) {
            $query->where('product_name LIKE "%' . pSQL($searchValue) . '%"');
        }

        foreach ($params['columns'] as $key => $column) {
            if ($column['name'] == 'id_category' && $column['search']['value']) {
                $query->where('cl.name LIKE "%' . pSQL($column['search']['value']) . '%"');

                continue;
            }

            if ($column['name'] == 'name' && $column['search']['value']) {
                $query->where('a.name LIKE "%' . pSQL($column['search']['value']) . '%"');

                continue;
            }

            if ($column['searchable'] != 'false') {
                $search_value = $column['search']['value'];
                if ($search_value) {
                    $query->where(pSQL($column['name']) . ' LIKE "%' . pSQL($search_value) . '%"');
                }
            }
        }

        foreach ($params['order'] as $order) {
            $column = $params['columns'][$order['column']];

            if ($column['name'] == 'id_category') {
                $query->orderBy('category_name' . ' ' . pSQL($order['dir']));

                continue;
            }

            if ($column['orderable'] != 'false') {
                $query->orderBy(pSQL($order['name']) . ' ' . pSQL($order['dir']));
            }
        }

        $query->limit($length, $start);

        $sql = $query->build();

        // Execute the query
        $results = $db->executeS($sql);

        // Get total records count
        $totalRecords = $db->getValue('SELECT FOUND_ROWS()');

        if ($results) {
            foreach ($results as &$result) {
                $result['checkbox'] = '<input type="checkbox" name="bulk_action[]" value="' . $result['id_mpmassimport_product'] . '">';
                // $cat = 'SELECT name FROM ' . _DB_PREFIX_ . 'category_lang WHERE id_category = ' . $result['id_category'] . ' AND id_lang = ' . \Context::getContext()->language->id;
                // $cat_name = $db->getValue($cat);
                // $result['category_name'] = $cat_name;
            }
        }

        // Prepare the response
        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $results,
        ];

        return $response;
    }
}
