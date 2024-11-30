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

<style>
    li.nav-item a.nav-link.active {
        background-color: #3a8eaf !important;
        color: #fff !important;
    }

    .mr-2 {
        margin-right: 0.5rem;
    }

    .navbar-nav a {
        text-decoration: none !important;
    }

    .navbar-nav li.btn {
        margin-right: 0.5rem;
        background-color: #3a8eaf;
        color: #fff !important;

    }

    .navbar-nav li.btn a {
        color: #fff !important;

    }

    .navbar-nav li.btn:hover {
        background-color: #54abce;
        color: #313131;
    }
</style>
{include file="./modals/modal-csv.tpl"}
{include file="./modals/modal-magnify.tpl"}

<div class="panel">
    <div class="page-bar" id="navbarNav">
        <div class="btn-toolbar">
            <ul class="nav nav-pills pull-right collapse navbar-collapse">
                <li>
                    <a class="toolbar-btn btn btn-primary" href="#" data-toggle="modal" data-target="#csvImportModal">
                        <i class="icon icon-file-o"></i>
                        <span>Importa da CSV</span>
                    </a>
                </li>
                <li>
                    <a class="toolbar-btn btn btn-primary" href="#">
                        <i class="icon icon-table"></i>
                        <span>Importa da XLSX</span>
                    </a>
                </li>
                <li>
                    <a class="toolbar-btn btn btn-primary" href="#">
                        <i class="icon icon-download"></i>
                        <span>Importa da FTP</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="panel panel-default mt-4">
    <div class="panel-heading overflow-none min-height-72">
        <p class="title pull-left">
            <i class="icon icon-list mr-2"></i>
            Elenco Prodotti da importare
        </p>

        <div class="panel-body float-right">
            <div class="form-group fixed-width-xxl">
                <div class="input-group">
                    <input type="file" id="import-file" value="" accept=".csv,.xlsx" style="display: none;">
                    <input type="text" class="form-control" id="search" placeholder="Seleziona..." readonly>
                    <div class="input-group-addon" id="click-upload-file" style="cursor: pointer;">
                        <i class="icon icon-search"></i>
                    </div>
                    <script type="text/javascript">
                        document.addEventListener("DOMContentLoaded", function() {
                            $('#click-upload-file').on('click', function() {
                                console.log("CLICK FILE");
                                $('#import-file').click();
                            });

                            $('#import-file').on('change', function(e) {
                                if ($("#templateSelect").val() == null) {
                                    setToast("Attenzione", "Seleziona un template prima di procedere", "warning");
                                    document.getElementById('import-file').value = "";
                                    $("#search").val("Seleziona...");
                                    return false;
                                }

                                var file = e.target.files[0];
                                if (file) {
                                    $("#search").val(file.name);
                                    $("#click-upload-file").find("i").removeClass("icon icon-search").addClass("icon icon-spinner icon-spin");
                                    let data = new FormData();
                                    data.append('file-upload', file);
                                    data.append('template', $('#templateSelect').val());
                                    data.append('action', 'importFromFile');
                                    data.append('ajax', 1);

                                    $.ajax({
                                        url: '{$controller_name}',
                                        type: 'POST',
                                        data: data,
                                        contentType: false,
                                        processData: false,
                                        success: function(response) {
                                            console.log(response);
                                            setToast(response.title, response.message, response.type);
                                        },
                                        error: function(response) {
                                            console.log(response);
                                            setToast(response.title, response.message, response.type);
                                        },
                                        complete: function() {
                                            $("#click-upload-file").find("i").removeClass("icon icon-spinner icon-spin").addClass("icon icon-search");
                                            dataTable.ajax.reload();
                                        }
                                    });
                                } else {
                                    $("#search").val("Seleziona...");
                                }
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
        <div class="panel-body float-right">
            <div class="form-group fixed-width-xxl">
                <div class="input-group">
                    <select class="form-control" id="templateSelect">
                        <option value="" disabled selected>Seleziona Template</option>
                        {foreach $templates as $item}
                            <option value="{$item.id}">{$item.name}</option>
                        {/foreach}
                    </select>
                    <div class="input-group-addon" title="{l s='modifica' mod='mpmassimport'}">
                        <a class="toolbar-btn" href="javascript:void(0);" onclick="editTemplate();">
                            <i class="icon icon-pencil"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <table id="tableProdotti" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>
                        <div class="form-check">
                            <input id="checkAll" class="form-check-input" type="checkbox" name="checkAll" value="true" title="Seleziona tutto">
                        </div>
                        <script type="text/javascript">
                            document.getElementById('checkAll').addEventListener('change', function() {
                                var checked = this.checked;
                                var tr = $('#tableProdotti tbody tr');

                                $.each(tr, function(row) {
                                    let checkbox = $(this).find('input[type="checkbox"]');
                                    checkbox.prop('checked', checked);
                                });
                            });
                        </script>
                    </th>
                    <th>ID</th>
                    <th>Nome Prodotto</th>
                    <th>Fornitore</th>
                    <th>Prezzo Originale</th>
                    <th>Sovrapprezzo</th>
                    <th>Prezzo Finale</th>
                    <th>Quantità</th>
                    <th>Categoria</th>
                    <th>Immagine</th>
                </tr>
            </thead>
            <tbody>
                <!-- Content will be loaded by DataTable -->
            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Nome Prodotto</th>
                    <th>Fornitore</th>
                    <th>Prezzo Originale</th>
                    <th>Sovrapprezzo</th>
                    <th>Prezzo Finale</th>
                    <th>Quantità</th>
                    <th>Categoria</th>
                    <th></th>
                </tr>
                <tr>
                    <th class="dt-center" colspan="10">
                        <div class="btn-group" role="group" aria-label="Button group">
                            <button type="button" class="btn btn-info-important" id="startImport" onclick="startImport(false);" title="Importa i selezionati">
                                <i class="icon icon-download"></i>
                            </button>
                            <button type="button" class="btn btn-danger-important" id="startImportAll" onclick="startImport(true);" title="Importa tutti i prodotti">
                                <i class="icon icon-download"></i>
                            </button>
                            <button type="button" class="btn btn-default" id="deleteSelected" title="Elimina i selezionati">
                                <i class="icon icon-trash"></i>
                            </button>
                            <button type="button" class="btn btn-default" id="resetSearch" title="Reset">
                                <i class="icon icon-refresh"></i>
                            </button>
                        </div>
                    </th>
                </tr>
        </table>
    </div>
</div>

{include file="{$MODULE_VIEWS}templates/admin/scripts/main-script.tpl"}
{include file="{$MODULE_VIEWS}templates/admin/scripts/createCategorySelectDiv.tpl"}

<script>
    let dataTable = null;
    let magnifyModal = null;
    let setCategory = null;

    function magnifyImage(img) {

        if (!magnifyModal) {
            magnifyModal = document.getElementById("myMagnifyModal");
        }

        let modalImg = document.getElementById("img01");
        let captionText = document.getElementById("caption");

        modalImg.src = img.src;
        captionText.innerHTML = img.alt;
        $(magnifyModal).modal("show");

        $(magnifyModal).on('hidden.bs.modal', function() {
            modalImg.src = "/img/p/404.gif";
        });

        let span = document.getElementsByClassName("close")[0];
        span.onclick = function() {
            $(magnifyModal).modal("hide");
        }
    }

    $(document).ready(function() {
        $("#tableProdotti tbody").on("click", "tr td:nth-child(8)", function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            if ($(this).find("#setCategory").length > 0) {
                return false;
            }

            let select = createCategorySelectDiv();

            let cell = this;
            let id_category = parseInt($(this).data('category'));
            let content = $(this).html();
            let csvCategory = $(content).find("span.badge-info").text();

            $(this).empty().append($(select).show());
            $(select).val(id_category);
            $(select).trigger("chosen:updated");

            $("#categorySelect").on("change", function() {
                let category = $(this).val();
                if (category == null) {
                    return false;
                }

                select.find("#categorySelect").val(category);
            });

            $("#btn-setCategory").on("click", function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                console.log("SELECT", $(select).val());
                let id_category = $(select).val();
                let category_name = $(select).find("option:selected").text();
                let newBadge = createBadgeCategory(csvCategory, category_name);

                $(cell).attr("data-category", id_category);
                $(select).remove();
                $(cell).empty().append(newBadge);
            });

            $("#btn-closeCategory").on("click", function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                console.log("CLOSE", content);
                $(select).remove();
                $(cell).empty().append(content);
            });
        });

        $(document).on('click', '#tableProdotti tbody tr td:last-child', function() {
            if (magnifyModal == null) {
                magnifyModal = document.getElementById('myMagnifyModal');
            }

            if (magnifyModal._isShown) {
                return;
            }

            let imgSrc = $(this).find('img').attr('src');
            if (imgSrc) {
                magnifyImage({ src: imgSrc, alt: 'Product Image' });
            }
        });

        dataTable = $('#tableProdotti').DataTable({
            "language": {
                "url": "/modules/mpmassimport/views/js/datatables-it.json"
            },
            "order": [
                [0, "desc"]
            ],
            "columnDefs": [{
                    "targets": [0],
                    "visible": true,
                    "searchable": false,
                    "orderable": false,
                    "width": "48px",
                    "className": "dt-center fixed-width-xs",
                    "name": "checkbox",
                    "data": "checkbox"
                },
                {
                    "targets": [1],
                    "visible": false,
                    "searchable": false,
                    "name": "id_mpmassimport_product",
                    "data": "id_mpmassimport_product"
                },
                {
                    "targets": [2],
                    "width": "30%",
                    "name": "name",
                    "data": "name"
                },
                {
                    "targets": [3],
                    "width": "20%",
                    "name": "id_supplier",
                    "data": "id_supplier",
                    "className": "dt-left"
                },
                {
                    "targets": [4],
                    "width": "10%",
                    "name": "price_original",
                    "data": "price_original"
                },
                {
                    "targets": [5],
                    "width": "10%",
                    "name": "surcharge",
                    "data": "surcharge"
                },
                {
                    "targets": [6],
                    "width": "10%",
                    "name": "price",
                    "data": "price"
                },
                {
                    "targets": [7],
                    "width": "10%",
                    "name": "quantity",
                    "data": "quantity"
                },
                {
                    "targets": [8],
                    "width": "10%",
                    "name": "id_category",
                    "data": "id_category"
                },
                {
                    "targets": [9],
                    "searchable": false,
                    "orderable": false,
                    "width": "10%",
                    "name": "images",
                    "data": "images"
                },
            ],
            "buttons": [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "scrollX": false,
            "scrollY": false,
            "scrollCollapse": false,
            "dom_": 'Blfrtip',
            "lengthChange": true,
            "lengthMenu": [10, 25, 50, 100, 200, { label: 'Tutti', value: -1 }],
            initComplete: function() {
                this.api().columns().every(function() {
                    var column = this;
                    if (column.index() == 0) {
                        column.width('48px');
                        return;
                    }
                    if (column.index() == 9) {
                        column.width('72px');
                        return;
                    }
                    var input = document.createElement("input");
                    $(input).addClass("form-control input-search");
                    $(input).appendTo($(column.footer()).empty())
                        .on('keyup', function() {
                            column.search(this.value).draw();
                        });
                });
            },
            rowCallback: function(row, data) {
                $('td:eq(0)', row)
                    .css({ width: "48px", "text-align": "center" });

                $('td:eq(2)', row)
                    .removeClass("dt-type-numeric")
                    .html(data.id_supplier == 0 ? '--' : data.supplier_name);

                $('td:eq(3)', row)
                    .html(toCurrency(data.price_original));

                $('td:eq(4)', row)
                    .addClass('dt-type-numeric')
                    .html(function() {
                        let json = JSON.parse(data.surcharge);
                        let ul = $('<ul>').addClass('list-unstyled');
                        $.each(json, function(key, value) {
                            if (value.type == 'percentuale') {
                                $(ul).append($('<li>').html(value.value + '%'));
                            } else {
                                $(ul).append($('<li>').html(toCurrency(value.value)));
                            }
                        });

                        return ul;
                    });

                $('td:eq(5)', row)
                    .html(toCurrency(data.price));

                $('td:eq(6)', row)
                    .html(function() {
                        if (data.quantity == 0) {
                            return $('<span>').addClass('badge badge-danger').text('Non disponibile');
                        } else if (data.quantity < 5) {
                            return $('<span>').addClass('badge badge-warning').text(data.quantity);
                        } else {
                            return $('<span>').addClass('badge badge-success').text(data.quantity);
                        }
                    });

                $('td:eq(7)', row)
                    .attr('data-category', data.id_category)
                    .empty()
                    .append(
                        $("<div>")
                        .addClass("panel-body dt-center")
                        .append($("<span>").addClass("badge badge-info").text(data.category))
                        .append($("<span>").addClass("badge badge-success ml-1 mt-1").html(data.category_name))
                    )
                    .on("hover", function() {
                        $(this).css("cursor", "pointer");
                    });

                $('td:eq(8)', row)
                    .empty()
                    .append(
                        $("<img>")
                        .addClass("img-thumbnail")
                        .attr("src", data.images)
                    );
            },
            ajax: {
                url: '{$controller_name}',
                type: 'POST',
                data: {
                    action: 'getTableProducts',
                    ajax: 1
                }
            },
            processing: true,
            serverSide: true
        });
    });

    function toCurrency(value) {
        return Number(value).toLocaleString('it-IT', { style: 'currency', currency: 'EUR' });
    }

    async function startImport(all = false) {
        let products = null;
        let total_products = null;

        if (all == true) {
            response = await fetch(
                '{$controller_name}&action=getAllProducts',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
            json = await response.json();

            products = json.products;
            total_products = products.length;

        } else {
            selectedRows = $("#tableProdotti tbody tr").find("input[type='checkbox']:checked");
            if (selectedRows.length == 0) {
                setToast("Attenzione", "Seleziona almeno un prodotto da importare", "warning");
                return false;
            }

            products = selectedRows.map(function() {
                return $(this).val();
            }).get()

            total_products = products.length;
        }

        if (confirm("Importare gli elementi selezionati?\nRighe selezionate: " + total_products) == false) {
            return false
        }

        importPrestashop(products);
    }

    async function importPrestashop(data) {
        let chunk = data.splice(0, 50);

        let response = await fetch(
            '{$controller_name}&action=importPrestashopCatalog',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    products: chunk
                })
            });

        let result = await response.json();

        if (data.length > 0) {
            importPrestashop(data);
        } else {
            setToast(result.title, result.message, result.type);
            dataTable.ajax.reload();
        }
    }
</script>