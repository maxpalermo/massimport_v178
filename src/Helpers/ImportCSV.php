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

class ImportCSV
{
    protected $header;
    protected $body;
    protected $errors;

    public function readCsv($file, $delimiter = ';', $enclosure = '"', $escape = '\\')
    {
        $handle = fopen($file, 'r');
        if ($handle === false) {
            return false;
        }

        $header = null;
        $data = [];
        while (($row = fgetcsv($handle, 0, $delimiter, $enclosure, $escape)) !== false) {
            $row = array_map('trim', $row);

            if ($header === null) {
                $header = $row;

                continue;
            }

            if (count($header) != count($row)) {
                $this->errors[] = sprintf('La riga %d ha %d colonne invece di %d', count($data), count($row), count($header));

                continue;
            }

            $data[] = array_combine($header, $row);
        }

        fclose($handle);

        $this->header = $header;
        $this->body = $data;

        return true;
    }

    public static function export($data, $file, $delimiter = ';', $enclosure = '"', $escape = '\\')
    {
        $handle = fopen($file, 'w');
        if ($handle === false) {
            return false;
        }

        foreach ($data as $row) {
            fputcsv($handle, $row, $delimiter, $enclosure, $escape);
        }

        fclose($handle);

        return true;
    }

    public static function getHeaders($file, $delimiter = ';', $enclosure = '"', $escape = '\\')
    {
        $handle = fopen($file, 'r');
        if ($handle === false) {
            return false;
        }

        $header = fgetcsv($handle, 0, $delimiter, $enclosure, $escape);

        fclose($handle);

        return $header;
    }

    public static function getRows($file, $delimiter = ';', $enclosure = '"', $escape = '\\')
    {
        $handle = fopen($file, 'r');
        if ($handle === false) {
            return false;
        }

        $header = fgetcsv($handle, 0, $delimiter, $enclosure, $escape);
        $data = [];
        while (($row = fgetcsv($handle, 0, $delimiter, $enclosure, $escape)) !== false) {
            $data[] = array_combine($header, $row);
        }

        fclose($handle);

        return $data;
    }

    public static function getRow($file, $rowNumber, $delimiter = ';', $enclosure = '"', $escape = '\\')
    {
        $handle = fopen($file, 'r');
        if ($handle === false) {
            return false;
        }

        $header = fgetcsv($handle, 0, $delimiter, $enclosure, $escape);
        $i = 0;
        while (($row = fgetcsv($handle, 0, $delimiter, $enclosure, $escape)) !== false) {
            if ($i == $rowNumber) {
                return array_combine($header, $row);
            }
            ++$i;
        }

        fclose($handle);

        return false;
    }

    public static function getRowByValue($file, $fieldName, $fieldValue, $delimiter = ';', $enclosure = '"', $escape = '\\')
    {
        $handle = fopen($file, 'r');
        if ($handle === false) {
            return false;
        }

        $header = fgetcsv($handle, 0, $delimiter, $enclosure, $escape);
        while (($row = fgetcsv($handle, 0, $delimiter, $enclosure, $escape)) !== false) {
            $data = array_combine($header, $row);
            if ($data[$fieldName] == $fieldValue) {
                return $data;
            }
        }

        fclose($handle);

        return false;
    }

    public static function getRowsByValue($file, $fieldName, $fieldValue, $delimiter = ';', $enclosure = '"', $escape = '\\')
    {
        $handle = fopen($file, 'r');
        if ($handle === false) {
            return false;
        }

        $header = fgetcsv($handle, 0, $delimiter, $enclosure, $escape);
        $data = [];
        while (($row = fgetcsv($handle, 0, $delimiter, $enclosure, $escape)) !== false) {
            $dataRow = array_combine($header, $row);
            if ($dataRow[$fieldName] == $fieldValue) {
                $data[] = $dataRow;
            }
        }

        fclose($handle);

        return $data;
    }

    public static function getRowByIndex($file, $rowNumber, $delimiter = ';', $enclosure = '"', $escape = '\\')
    {
        $handle = fopen($file, 'r');
        if ($handle === false) {
            return false;
        }

        $i = 0;
        while (($row = fgetcsv($handle, 0, $delimiter, $enclosure, $escape)) !== false) {
            if ($i == $rowNumber) {
                return $row;
            }
            ++$i;
        }

        fclose($handle);

        return false;
    }

    public function readCsvWithPhpOffice($file, $delimiter = ';')
    {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $reader->setDelimiter($delimiter);
        $spreadsheet = $reader->load($file);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $this->header = array_shift($sheetData);
        $this->body = $sheetData;

        return true;
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getPreviewBody()
    {
        return array_slice($this->body, 0, 25);
    }
}
