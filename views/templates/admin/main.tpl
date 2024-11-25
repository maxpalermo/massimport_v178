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

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<style>
    .nav-item {
        display: inline-block;
        padding: 0.5rem 1rem;
        font-size: 1rem;
        font-weight: 400;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        user-select: none;
        border: 1px solid transparent;
        border-radius: 0.25rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .nav-item:hover {
        color: #0056b3;
        text-decoration: none;
        background-color: #e9ecef;
        border-color: #dae0e5;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Importa Prodotti</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="#">Importa da XLSX</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Importa da FTP</a>
            </li>
            <li class="nav-item">
                <select class="form-control" id="fornitoreSelect">
                    <option value="" disabled selected>Seleziona Fornitore</option>
                    <option value="fornitore1">Fornitore 1</option>
                    <option value="fornitore2">Fornitore 2</option>
                    <option value="fornitore3">Fornitore 3</option>
                </select>
            </li>
        </ul>
    </div>
</nav>

<div class="card mt-4">
    <div class="card-header">
        <h3 class="title">Elenco Prodotti Importati</h3>
    </div>
    <div class="card-body">
        <table id="prodottiTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome Prodotto</th>
                    <th>Fornitore</th>
                    <th>Prezzo</th>
                    <th>Quantità</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$prodotti item=prodotto}
                    <tr>
                        <td>{$prodotto.id}</td>
                        <td>{$prodotto.nome}</td>
                        <td>{$prodotto.fornitore}</td>
                        <td>{$prodotto.prezzo}</td>
                        <td>{$prodotto.quantita}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>

{literal}
    <script>
        $(document).ready(function() {
            $('#prodottiTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Italian.json"
                }
            });
        });
    </script>
{/literal}