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

class HeaderProduct
{
    protected $columns;
    protected $module;
    protected $header;
    protected $delimiter;

    public function __construct(\Module $module, $header = '', $delimiter = ';')
    {
        $id_employee = \Context::getContext()->employee->id;
        $this->module = $module;
        $this->columns = new ColumnsProduct($module);

        if (!$header) {
            $header = \Configuration::get('MPMASSIMPORT_CSV_DIVIDER_' . $id_employee);
            if ($header) {
                $header = json_decode($header, true);
            } else {
                $header = [];
            }
        } else {
            $this->header = $header;
            $this->delimiter = $delimiter;

            if (!is_array($header)) {
                $this->split($header, $delimiter);
            }

            \Configuration::updateValue('MPMASSIMPORT_CSV_DIVIDER', $delimiter);
            \Configuration::updateValue('MPMASSIMPORT_CSV_DIVIDER_' . $id_employee, json_encode($this->header));
        }
    }

    public function split($header, $delimiter)
    {
        $this->delimiter = $delimiter;
        $this->header = explode($delimiter, $header);
    }

    public function getHeader()
    {
        return $this->header;
    }
}
