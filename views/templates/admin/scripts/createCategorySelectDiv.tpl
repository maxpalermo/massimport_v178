<script type="text/javascript">
    function createCategorySelectDiv() {
        let categories = {$categories};

        let html = null;

        html = '<div class="form-group text-center" id="setCategory">';
        html += '<label for="categorySelect">Seleziona Categoria</label>';
        html += '<select class="form-control" id="categorySelect">';
        html += '<option value="" disabled selected>Seleziona Categoria</option>';

        $.each(categories, function(index, category) {
            html += '<option value="' + category.id_category + '">' + category.name + '</option>';
        });

        html += '</select>';
        html += '<div class="btn-group mt-24-px" role="group" aria-label="Button group">';
        html += '<button class="btn btn-info-important" id="btn-setCategory">';
        html += '<i class="icon icon-check"></i>';
        html += '<span><i class="icon icon-check"></i></span>';
        html += '</button>';
        html += '<button class="btn btn-secondary-important" id="btn-closeCategory">';
        html += '<i class="icon icon-check"></i>';
        html += '<span><i class="icon icon-times"></i></span>';
        html += '</button>';
        html += '</div>';
        html += '</div>';

        let component = $(html);
        $(component).find("#categorySelect").chosen({
            width: "100%",
            no_results_text: "Nessun risultato per: ",
            placeholder_text_single: "Seleziona Categoria",
            search_contains: true,
            disable_search_threshold: 10,
            allow_single_deselect: true,
        });

        return component;
    }

    function createBadgeCategory(csvCategory, prestashopCategory) {
        let html = null;

        html = $("<div>")
            .addClass("panel-body dt-center")
            .append(
                $("<span>")
                .addClass("badge badge-info p-1")
                .text(csvCategory)
            )
            .append(
                $("<span>")
                .addClass("badge badge-success ml-1 mt-1 p-1")
                .text(prestashopCategory)
            );

        return html;
    }
</script>