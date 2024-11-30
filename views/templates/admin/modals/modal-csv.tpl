{**
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
 *}

<div class="modal fade" id="csvImportModal" tabindex="-1" role="dialog" aria-labelledby="csvImportModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-full-screen" role="document">
        <div class="modal-content">
            <div class="modal-header overflow-none" style="min-height: 72px;">
                <h4 class="modal-title float-left mr-2" id="csvImportModalLabel">
                    <i class="material-icons">cloud_upload</i>
                    <span>Importa CSV</span>
                </h4>

                <div class="d-flex justify-content-end">
                    <div class="form-group ml-2">
                        <button type="button" class="btn btn-primary float-right mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">Chiudi</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal-body">
                <form id="csvImportForm" enctype="multipart/form-data">
                    <!-- FILE INPUT ROW -->
                    <div class="row">
                        <!-- FILE INPUT -->
                        <div class="d-flex justify-content-between">
                            <div class="width-30-percent pr-1">
                                <div class="d-flex justify-content-between">
                                    <div class="form-group mr-1">
                                        <table class="table table-bordered table-condensed table-light">
                                            <tbody>
                                                <tr>
                                                    <td>Carica template</td>
                                                    <td>
                                                        <div class="input-group btn-addon">
                                                            <select class="form-control chosen" id="template-name" data-width="140px">
                                                                <option value="">Seleziona ...</option>
                                                                {foreach from=$templates item=template}
                                                                    <option value="{$template.id}">{$template.name}</option>
                                                                {/foreach}
                                                            </select>
                                                            <span class="input-group-addon" onclick="getTemplate();" title="Carica un template esistente">
                                                                <span>OK</span>
                                                            </span>
                                                        </div>
                                                        <script type="text/javascript">
                                                            async function fetchConfiguration(templateId) {
                                                                let response = await fetch(
                                                                    '{$controller_name}&fetch=getTemplate', {
                                                                    method: 'POST',
                                                                    headers: {
                                                                        'Content-Type': 'application/json'
                                                                    },
                                                                    body: JSON.stringify({ templateId: templateId })
                                                                });

                                                            let data = await response.json();

                                                            setToast(data.title, data.message, data.type);
                                                            if (!data.error) {
                                                                let conf = data.configuration;

                                                                $("#csv_divider").val(conf.csv_divider);
                                                                $("#category_divider").val(conf.category_divider);
                                                                $("[data-key='stock_min']").val(conf.stock_min);
                                                                $("[data-key='id_supplier']").val(conf.id_supplier);
                                                                $("[data-key='id_category_default']").val(conf.default_category);
                                                                $("#config-name").val(conf.name);

                                                                let surcharges = conf.surcharge;
                                                                let surchargeTable = $('#tableSurcharge');
                                                                $.each(surchargeTable.find('.surcharge-item'), function() {
                                                                    if ($(this).index() > 0) {
                                                                        $(this).remove();
                                                                    }
                                                                });
                                                                $.each(surcharges, function(index, surcharge) {
                                                                    let surchargeItem = surchargeTable.find('.surcharge-item').first().clone();
                                                                    let select = surchargeItem.find('.surcharge-type');
                                                                    let input = surchargeItem.find('.surcharge-value');
                                                                    select.val(surcharge.type);
                                                                    input.val(surcharge.value);
                                                                    surchargeTable.find("tbody").append(surchargeItem);
                                                                    surchargeItem.show();
                                                                });

                                                                let headers = data.headers;
                                                                let headerTable = $('#csvHeaders');
                                                                headerTable.find('.csv-header-item').remove();
                                                                $.each(headers, function(index, header) {
                                                                    let headerItem = headerTable.find('.csv-header-item').first().clone();
                                                                    let select = headerItem.find('.header-type');
                                                                    let input = headerItem.find('.header-pattern');
                                                                    select.val(header.type);
                                                                    input.val(header.pattern);
                                                                    headerTable.find("tbody").append(headerItem);
                                                                    headerItem.show();
                                                                });

                                                                let categories = data.categories;
                                                                let categoryTable = $('#categoryMapping');
                                                                categoryTable.find('.category-item').remove();
                                                                $.each(categories, function(index, category) {
                                                                    let categoryItem = categoryTable.find('.category-item').first().clone();
                                                                    let select = categoryItem.find('.category-type');
                                                                    select.val(category.id_category);
                                                                    categoryTable.find("tbody").append(categoryItem);
                                                                    categoryItem.show();
                                                                });
                                                            }
                                                            }

                                                            async function getTemplate() {
                                                                let templateId = document.getElementById('template-name').value;

                                                                if (!templateId) {
                                                                    setToast('Attenzione', 'Seleziona un template', 'warning');
                                                                    return;
                                                                }

                                                                await fetchConfiguration(templateId);
                                                            }
                                                        </script>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Carattere di divisione colonne</td>
                                                    <td>
                                                        <div class="input-group btn-addon fixed-width-md">
                                                            <input type="text" id="csv_divider" name="csv_divider" class="form-control text-center" data-key="csv_divider" value="{$configuration.csv_divider}">
                                                            <span class="input-group-addon">
                                                                <i class="icon icon-table"></i>
                                                            </span>
                                                        </div>
                                                        <small class="form-text text-muted">Le colonne del CSV saranno separate da questo carattere</small>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Carattere di divisione categorie</td>
                                                    <td>
                                                        <div class="input-group btn-addon fixed-width-md">
                                                            <input type="text" id="category_divider" name="category_divider" class="form-control text-center" data-key="category_divider" value="{$configuration.category_divider}">
                                                            <span class="input-group-addon">
                                                                <i class="icon icon-table"></i>
                                                            </span>
                                                        </div>
                                                        <small class="form-text text-muted">Le categorie del CSV saranno separate da questo carattere</small>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Stock minimo</td>
                                                    <td>
                                                        <div class="input-group btn-addon fixed-width-md">
                                                            <input type="text" class="form-control text-right" data-key="stock_min" value="{$configuration.stock_min}">
                                                            <span class="input-group-addon">
                                                                <i class="icon icon-cubes"></i>
                                                            </span>
                                                        </div>
                                                        <small class="form-text text-muted">Quantità minima per poter importare il prodotto.</small>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Seleziona un fornitore</td>
                                                    <td>
                                                        <div class="input-group btn-addon">
                                                            <select id="id_supplier" name="id_supplier" class="form-control chosen" data-key="id_supplier" value="{$configuration.id_supplier}">
                                                                <option value="">Nessuno</option>
                                                                {foreach from=$suppliers item=supplier}
                                                                    <option value="{$supplier.id_supplier}">{$supplier.name}</option>
                                                                {/foreach}
                                                            </select>
                                                            <span class="input-group-addon">
                                                                <i class="icon icon-user"></i>
                                                            </span>
                                                        </div>
                                                        <small class="form-text text-muted">Se non esiste un fornitore nel CSV, sarà usato questo di default</small>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Seleziona una categoria di default</td>
                                                    <td>
                                                        <div class="input-group btn-addon">
                                                            <select class="form-control chosen" data-key="id_category_default" value="">
                                                                <option value="">Seleziona una categoria</option>
                                                                {assign var=list_categories value=$categories|@json_decode:true}
                                                                {foreach from=$list_categories item=category}
                                                                    <option value="{$category.id_category}">{$category.name}</option>
                                                                {/foreach}
                                                            </select>
                                                            <span class="input-group-addon">
                                                                <i class="icon icon-folder"></i>
                                                            </span>
                                                        </div>
                                                        <small class="form-text text-muted">Se non esiste una categoria nel CSV, sarà usata questa di default</small>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Sovrapprezzo</td>
                                                    <td>
                                                        <table class="table table-bordered table-condensed" id="tableSurcharge">
                                                            <thead>
                                                                <tr>
                                                                    <th>Tipo</th>
                                                                    <th>Valore</th>
                                                                    <th>Azioni</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr class="surcharge-item" style="display: none;">
                                                                    <td>
                                                                        <select class="form-control surcharge-type fixed-width-lg">
                                                                            <option value="percentuale">Percentuale</option>
                                                                            <option value="fisso">Fisso</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group fixed-width-md">
                                                                            <input type="text" class="form-control surcharge-value text-right" placeholder="Valore">
                                                                            <div class="input-group-addon">
                                                                                <span class="input-group-text addon-type">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-danger remove-surcharge">Rimuovi</button>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr class="bg-white">
                                                                    <td colspan="3" class="bg-white text-right">
                                                                        <button type="button" class="btn btn-success add-surcharge pt-2">Aggiungi Sovrapprezzo</button>
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                        <small class="form-text text-muted">Sovrapprezzo da applicare al prezzo di acquisto del prodotto</small>
                                                        <script type="text/javascript">
                                                            $(document).on('click', '.add-surcharge', function() {
                                                                let table = $('#tableSurcharge');
                                                                let surchargeItem = $('.surcharge-item').first().clone();
                                                                let select = surchargeItem.find('.surcharge-type');
                                                                let input = surchargeItem.find('.surcharge-value');

                                                                select.value = '';
                                                                input.value = '';

                                                                $(select).on('click', function() {
                                                                    let value = $(this).val();
                                                                    let tr = $(this).closest('tr');
                                                                    let addon = tr.find('.addon-type');

                                                                    $(addon).text($(this).val() === 'percentuale' ? '%' : 'EUR');
                                                                });

                                                                $(table).find("tbody").append(surchargeItem);
                                                                $(surchargeItem).show();
                                                            });

                                                            $(document).on('click', '.remove-surcharge', function() {
                                                                let table = $('#tableSurcharge');
                                                                if ($(table).find('.surcharge-item').length > 1) {
                                                                    $(this).closest('.surcharge-item').remove();
                                                                } else {
                                                                    $(this).closest('.surcharge-item').find('.surcharge-type').val('');
                                                                    $(this).closest('.surcharge-item').find('.surcharge-value').val('');
                                                                }
                                                            });
                                                        </script>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="2">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="form-group width-70-percent mr-2">
                                                                <label for="config-name" style="padding-top: 8px;">Nome Configurazione</label>
                                                                <input type="text" id="config-name" name="config-name" class="form-control" data-key="config_name" value="">
                                                            </div>
                                                            <div class="form-group">
                                                                <button type="button" class="btn btn-primary" style="margin-top: 32px;" onclick="saveConfiguration();">
                                                                    <i class="icon icon-save mr-1"></i>
                                                                    <span>Salva Configurazione</span>
                                                                </button>
                                                                <br />
                                                                <small class="form-text text-muted text-danger">Salva la configurazione prima di importare il file CSV e dopo che hai impostato i parametri per le colonne e le categorie</small>
                                                            </div>
                                                        </div>
                                                        <script type="text/javascript">
                                                            function saveConfiguration() {
                                                                if (confirm("{l s='Salvare la configurazione?' mod='mpmassimport'}") == false)
                                                                {
                                                                    return false;
                                                                }

                                                                const headers = function() {
                                                                    let headers = [];
                                                                    const tr = $("#csvHeaders tbody tr");
                                                                    if (tr.length === 0) {
                                                                        return [];
                                                                    }

                                                                    $.each(tr, function() {
                                                                        let item = $(this);
                                                                        let title = item.find('td:nth-child(1)').text().trim();
                                                                        let select = item.find('td:nth-child(1) select').val();
                                                                        let pattern = item.find('td:nth-child(2) input').val();
                                                                        let skip = item.find('td:nth-child(2) input[type=checkbox]').is(':checked');
                                                                        headers.push({
                                                                            title: title,
                                                                            select: select,
                                                                            pattern: pattern,
                                                                            skip: skip
                                                                        });
                                                                    });

                                                                    return headers;
                                                                };

                                                                const categories = function() {
                                                                    let categories = [];
                                                                    const tr = $("#categoryMapping tbody tr");
                                                                    if (tr.length === 0) {
                                                                        return [];
                                                                    }

                                                                    $.each(tr, function() {
                                                                        let item = $(this);
                                                                        let category = item.find('td:nth-child(1)').text().trim();
                                                                        let select = item.find('td:nth-child(2) select').val();
                                                                        let surcharge = item.find('td:nth-child(3) input').val();
                                                                        let skip = item.find('td:nth-child(4) input[type=checkbox]').is(':checked');
                                                                        categories.push({
                                                                            category: category,
                                                                            select: select,
                                                                            surcharge: surcharge,
                                                                            skip: skip
                                                                        });
                                                                    });

                                                                    return categories;
                                                                };

                                                                const surcharge = function() {
                                                                    let surcharges = [];
                                                                    const tr = $("#tableSurcharge tbody tr");
                                                                    if (tr.length === 0) {
                                                                        return [];
                                                                    }

                                                                    $.each(tr, function() {
                                                                        let item = $(this);
                                                                        let type = item.find('.surcharge-type').val();
                                                                        let value = item.find('.surcharge-value').val();
                                                                        surcharges.push({
                                                                            type: type,
                                                                            value: value
                                                                        });
                                                                    });

                                                                    return surcharges;
                                                                };

                                                                let configData = {
                                                                    ajax: 1,
                                                                    action: 'saveConfiguration',
                                                                    name: document.getElementById('config-name').value,
                                                                    type: 'csv',
                                                                    config_name: document.getElementById('config-name').value,
                                                                    csv_divider: document.getElementById('csv_divider').value,
                                                                    category_divider: document.getElementById('category_divider').value,
                                                                    stock_min: document.querySelector('[data-key="stock_min"]').value,
                                                                    id_supplier: document.querySelector('[data-key="id_supplier"]').value,
                                                                    id_category_default: document.querySelector('[data-key="id_category_default"]').value,
                                                                    surcharge: JSON.stringify(surcharge()),
                                                                    headers: JSON.stringify(headers()),
                                                                    categories: JSON.stringify(categories())
                                                                };

                                                                fetch(
                                                                        '{$controller_name}&fetch=saveConfiguration',
                                                                        {
                                                                            method: 'POST',
                                                                            body: JSON.stringify(configData)
                                                                        })
                                                                    .then(response => response.json())
                                                                    .then(function(data) {
                                                                        setToast(data.title, data.message, data.type);
                                                                    });
                                                            }
                                                        </script>
                                                    </td>
                                                </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="width-70-percent pl-1 left-border">
                                <div class="form-group">
                                    <label for="csvFile">Seleziona file CSV</label>
                                    <input type="file" class="hidden" id="csvFile" name="csvFile" accept=".csv">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <span class="input-group-text" id="inputGroupFileAddon01">
                                                <i class="icon icon-upload"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" id="csvFileText" readonly>
                                        <div class="input-group-addon" id="addon-choose-file">
                                            <span>Scegli file</span>
                                        </div>
                                        <script type="text/javascript">
                                            document.addEventListener('DOMContentLoaded', function() {
                                                document.getElementById('csvFile').addEventListener('change', function(e) {
                                                    let files = e.target.files;
                                                    let file = files[0];
                                                    let fileName = file.name;
                                                    document.getElementById('csvFileText').value = fileName;
                                                });

                                                document.getElementById('csvFileText').addEventListener('click', function(e) {
                                                    e.stopImmediatePropagation();
                                                    document.getElementById('csvFile').click();
                                                });

                                                document.getElementById('addon-choose-file').addEventListener('click', function(e) {
                                                    e.stopImmediatePropagation();
                                                    document.getElementById('csvFile').click();
                                                });
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="from-group">
                                    <div id="dropZone" style="border: 2px dashed #ccc; min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                        Trascina qui il file CSV
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="urlvFile">Seleziona da URL</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <span class="input-group-text" id="urlFileAddonPrepend">
                                                        <i class="icon icon-globe"></i>
                                                    </span>
                                                </div>
                                                <input type="text" class="form-control" id="urlFileText">
                                            </div>
                                            <script type="text/javascript">
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    document.getElementById('urlFileAddonDownload').addEventListener('click', function(e) {
                                                        e.stopImmediatePropagation();
                                                        let url = document.getElementById('urlFileText').value;
                                                        let fileName = document.getElementById('fileName').value;
                                                        let fileExtractPath = document.getElementById('fileExtractPath').value;

                                                        if (!url) {
                                                            setToast('Attenzione', 'Inserisci un URL valido', 'warning');
                                                            return;
                                                        }

                                                        let data = {
                                                            ajax: true,
                                                            action: 'downloadUrl',
                                                            url: url,
                                                            fileName: fileName,
                                                            fileExtractPath: fileExtractPath
                                                        };

                                                        $.ajax({
                                                            url: '{$controller_name}',
                                                            type: 'POST',
                                                            data: data,
                                                            success: function(response) {
                                                                setToast(response.title, response.message, response.type);
                                                            },
                                                            error: function(response) {
                                                                setToast('Errore', 'Errore durante il download del file', 'error');
                                                            }
                                                        });
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="fileName">inserisci il nome del file da salvare</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <span class="input-group-text" id="fileNameAddonPrepend">
                                                        <i class="icon icon-chevron-right"></i>
                                                    </span>
                                                </div>
                                                <input class="form-control" type="text" name="fileName" id="fileName" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="fileName">Estrae in</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <span class="input-group-text" id="fileExtractPathAddonPrepend">
                                                        <i class="icon icon-chevron-right"></i>
                                                    </span>
                                                </div>
                                                <input class="form-control" type="text" name="fileExtractPath" id="fileExtractPath" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group d-flex">
                                            <button type="button" class="btn btn-primary mt-24-px" id="urlFileAddonDownload">Scarica</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12 overflow-y-auto max-height-400">
                                        <p class="title border-bottom">Anteprima</p>
                                        <table id="previewFile" class="table table-bordered no-space-split">
                                            <thead class="sticky">
                                                <!-- Intestazioni CSV dinamiche -->
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="alert alert-warning" role="alert">
                                                            Non ci sono dati da visualizzare
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END FILE INPUT _-->
                    </div>
                    <!-- END FILE INPUT ROW -->
                    <hr>
                </form>

                <div class="d-flex justify-content-between">
                    <div class="width-50-percent pr-1">
                        <p class="title border-bottom">Intestazione CSV</p>
                        <table id="csvHeaders" class="table table-bordered">
                            <thead class="sticky">
                                <tr>
                                    <th>Colonna CSV</th>
                                    <th>Associa a</th>
                                    <th>Pattern</th>
                                    <th>
                                        <input type="checkbox" class="checkAll"> <span>Non importare</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Intestazioni CSV dinamiche -->
                            </tbody>
                            <tfoot>
                                <!-- Footer CSV -->
                            </tfoot>
                        </table>
                    </div>

                    <div class="width-50-percent pl-1">
                        <p class="title border-bottom">Categorie</p>
                        <table id="categoryMapping" class="table table-full table-bordered">
                            <thead class="sticky">
                                <tr>
                                    <th>Categoria CSV</th>
                                    <th>Associa a Categoria PrestaShop</th>
                                    <th>Sovrapprezzo</th>
                                    <th>
                                        <input type="checkbox" class="checkAll"> <span>Non importare</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Categorie CSV dinamiche -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right">
                                        <button type="button" class="btn btn-primary" onclick="readCategoriesFromLines();">Leggi</button>
                                    </td>
                                </tr>
                        </table>
                        <script type="text/javascript">
                            async function createPrestashopCategories() {
                                const response = await fetch(
                                    '{$controller_name}&action=createSelectCategories',
                                    {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        }
                                    });

                                const data = await response.json();
                                return $(data.content);
                            }

                            async function readCategoriesFromLines() {
                                let prestashop_categories = await createPrestashopCategories();
                                let list_categories = [];
                                let index = -1;
                                let categoryTable = $('#categoryMapping');

                                $.each($('.csv-categories'), function(key, item) {
                                    let column = item.value;
                                    if (column == 'category') {

                                        let parent = $(item).closest('tr');
                                        let key = $(parent).data('key');
                                        index = key;

                                        return;
                                    }
                                });

                                if (index === -1) {
                                    setToast('Errore', 'Seleziona la colonna delle categorie', 'warning', 'error', 3000);
                                    return false;
                                }

                                let json_body = {
                                    index: index,
                                    category_divider: $('#category_divider').val()
                                };

                                const cvs_lines = await fetch(
                                    "{$controller_name}&fetch=getCategoriesFromCsv",
                                    {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify(json_body)
                                    });

                                csv_lines = await cvs_lines.json();

                                setToast(csv_lines.title, csv_lines.message, csv_lines.type, csv_lines.icon, csv_lines.delay);
                                if (csv_lines.error) {
                                    return false;
                                }

                                const categoryList = csv_lines.categories;
                                const categoryMapping = $('#categoryMapping tbody');

                                $.each(categoryList, function() {
                                    let category = this;
                                    let chosen = $(prestashop_categories).clone();
                                    let tr = $('<tr></tr>');
                                    let td_category = $("<td></td>").append(category);
                                    let td_select = $("<td></td>")
                                        .append(chosen)
                                        .append($("<small class='form-text text-muted'>Seleziona la categoria di Prestashop</small>"));
                                    let surcharge = $("<td></td>")
                                        .append($("<input type='text' class='form-control' name='surcharge' value=''>"))
                                        .append($("<small class='form-text text-muted'>Inserisci i sovrappprezzi separati da <strong>;</strong></small>"));
                                    let td_checkbox = $("<td class=\"fixed-width-sm text-center\"></td>").append($("<input type='checkbox' class='category-skip' name='category-skip' value='" + category + "'>"));
                                    tr.append(td_category);
                                    tr.append(td_select);
                                    tr.append(surcharge);
                                    tr.append(td_checkbox);
                                    categoryMapping.append(tr);

                                    let width = $(this).data('width') ? $(this).data('width') : '100%';
                                    let no_result = $(this).data('no-result') ? $(this).data('no-result') : 'Nessun risultato trovato';
                                    let search_contains = $(this).find('option').length > 10 ? true : false;
                                    $(chosen).chosen({
                                        no_results_text: no_result,
                                        width: width,
                                        search_contains: search_contains
                                    });
                                });
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>