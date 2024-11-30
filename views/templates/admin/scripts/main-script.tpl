<script type="text/javascript">
    let configuration_values = {
        csv_divider: '{$configuration.csv_divider}',
        category_divider: '{$configuration.category_divider}',
        id_supplier: '{$configuration.id_supplier}',
        id_category_default: '{$configuration.id_category_default}',
        stock_min: '{$configuration.stock_min}'
    };

    let csv_lines = [];
    let csvFile = {
        header: [],
        body: []
    };

    async function getHeaders(header) {
        let delimiter = $('#divider').val();
        let headers = await fetch(
            "{$controller_name}&action=getHeaders", 
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ delimiter: delimiter, header: header })
            });

        const data = await headers.json();
        return data;
    }

    function setToast(title, message, type, icon, delay) {
        createToast(title, message, type, icon, delay)
    }

    async function getProductColumns() {
        const data = await fetch(
            "{$controller_name}&action=getProductColumnsSelect", 
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
        const json = await data.json();
        const select = $(json.select);

        return select;
    }

    async function parseFile(formData) {
        $("#inputGroupFileAddon01").find('i').removeClass('icon-upload').addClass('icon-spinner');

        const response = await fetch(
            "{$controller_name}&fetch=parseFile",
            {
                method: 'POST',
                body: formData
            });

        const data = await response.json();

        csvFile = {
            header: data.header,
            body: data.body
        };

        readerPreview();

        $("#inputGroupFileAddon01").find('i').removeClass('icon-spinner').addClass('icon-upload');

        setToast(data.title, data.message, data.type, data.icon, data.delay);
    }


    async function readerPreview(file) {
        let headersTable = $('#csvHeaders tbody');
        let previewTable = $('#previewFile');
        let previewHead = previewTable.find('thead');
        let previewBody = previewTable.find('tbody');
        let select = await getProductColumns();

        headersTable.empty();
        previewHead.empty();
        previewBody.empty();
        let trHead = $('<tr></tr>');

        $.each(csvFile.header, function(key, value) {
            let tr = $('<tr></tr>').attr("data-key", key);
            let td_head = $("<td></td>").append(value);
            let td_select = $("<td></td>").append($(select).clone());
            let td_input_regex = $("<td></td>").append($("<input type='text' class='form-control' placeholder='Pattern'>"));
            let td_skip_import = $("<td class=\"fixed-width-sm text-center\"></td>").append($("<input type='checkbox' class='skip-import' name='skip-import' value='" + value + "'>"));
            tr.append(td_head);
            tr.append(td_select);
            tr.append(td_input_regex);
            tr.append(td_skip_import);

            headersTable.append(tr);
            trHead.append({literal}`<th>${value}</th>`{/literal});
        });

        $(previewHead).empty().append(trHead);

        let index_line = 0;
        $.each(csvFile.body, function(key, value) {
            let columns = value;
            let tr = $('<tr></tr>');
            $.each(columns, function(key_column, column) {
                tr.append({literal}`<td>${column}</td>`{/literal});
            });
            previewBody.append(tr);
            index_line++;
            if (index_line > 50) {
                return false;
            }
        });

        $(".chosen").chosen({
            no_results_text: "Nessun risultato trovato",
            width: "100%",
            search_contains: true
        });
    }

    function extractRegExPattern(pattern, string) {
        let regEx = new RegExp(pattern, 'g');

        let match = regEx.exec(string);
        if (match) {
            return match[1];
        }

        return string;
    }

    document.addEventListener("DOMContentLoaded", function() {
        $("select[name=prestashop_categories]").on("change", function() {
            let checkbox = $(this).closest('tr').find('input[type=checkbox]');
            checkbox.prop('checked', false);
        });

        $('#saveConfiguration').on('click', function() {
            saveConfiguration();
        });

        $(".checkAll").on("click", function() {
            let table = $(this).closest('table');
            table.find('tbody input[type=checkbox]').attr('checked', $(this).is(':checked'));
        });

        $("#readCategories").on("click", function() {
            readCategoriesFromLines();
        });

        $('#csvFile').on('change', function(e) {
            e.preventDefault();
            let form = document.getElementById('csvImportForm');
            let formData = new FormData();
            formData.append('csvFile', $('#csvFile')[0].files[0]);
            formData.append('csv_divider', $('#csv_divider').val());
            formData.append('category_divider', $('#category_divider').val());
            formData.append('id_supplier', $('#id_supplier').val());
            formData.append('id_category_default', $('#id_category_default').val());
            formData.append('stock_min', $('#stock_min').val());
            formData.append('surcharge', JSON.stringify($('#tableSurcharge').serializeArray()));

            parseFile(formData);
        });

        $('#importCsvButton').on('click', function() {
            var form = $('#csvImportForm')[0];
            var formData = new FormData(form);
            $.ajax({
                url: 'index.php?controller=AdminMassImportProducts&ajax=1&action=importCsv',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    setToast(response.title, response.message, response.type, response.icon, response.delay);
                }
            });
        });

        var dropZone = document.getElementById('dropZone');

        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.style.borderColor = '#000';
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.style.borderColor = '#ccc';
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.style.borderColor = '#ccc';
            let files = e.dataTransfer.files;
            if (files.length > 0) {
                let file = files[0];
                $('#csvFile')[0].files = files;
                $('#csvFile').trigger('change');
            }
        });
    });
</script>