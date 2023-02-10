<section id="configuration-options">
    <form method="post" action="" class="form-horizontal clearfix" id="configurator-form" enctype="multipart/form-data">
        <div class="material">
            <h2><strong>Choix des matériaux</strong></h2>
            <div class="material-choice">
                {foreach from=$allMaterial item=$material}
                    <div class="items-center">
                        <input type="checkbox" data-name="{$material.name}" name="{$material.id_attribute}" id="{$material.id_attribute}">
                        <label>{$material.name}</label>
                    </div>
                {/foreach}
            </div>
        </div>
        <br />
        <section class="product-list">

        </section>
        <section class="all-product-options">
        </section>
        <textarea id="detail" style="display:none;">{$data}</textarea>
    </form>
</section>

<script type="text/javascript">
    var allMaterial = [];
    {foreach from=$allMaterial item=$material}
        allMaterial['{$material.id_attribute}'] = {$material.products|json_encode};
    {/foreach}

    $(document).ready(function() {
        // Pour chaque checkbox de matériaux
        $(".material-choice input").on('change', function() {
            // Si elle est cochée
            if ($(this).is(":checked")) {
                // On récupère le nom de la checkbox
                var name = $(this).data('name');
                var id_material = $(this).prop('name');

                var options = '';
                $.each(allMaterial[id_material], function(index, value) {
                    options += '<option value="' + value.id_product + '">' + value.name + '</option>';
                });

                // On ajoute à la div contenant la liste des choix de matériaux le select correspondant au matériaux
                // Afin de pouvoir choisir les produits en fonction du matériaux
                // On y insère le nom pour le reconnaître
                $('.product-list').append(
                    '<div class="material-product-select" id="' + id_material + '-select">' +
                        '<input class="input-material-name" type="hidden" name="material[]" value="' + id_material + '">' +
                        '<h2 class="product-select-name"><span>' + name + '</span></h2>' +
                        '<div class="text-popin">' +
                            '<h3>Texte popin</h3>' +
                            '<textarea name="textpopin_' + id_material + '"></textarea>' +
                        '</div>' +
                        '<div class="product-select">' +
                            '<h3>Sélectionnez vos produits (4 max)</h3>' +
                            '<select id="allProducts" multiple name="' + id_material + '_products[]" class="allProducts" max="4">' +
                                options +
                            '</select>' +
                            '<div class="add-products add-products-' + id_material + '">' +
                                'Ajouter' +
                            '</div>' +
                        '</div>' +
                        '<br /><br />' +
                        '<div id="' + id_material + '-product-options" class="product-options">' +
                            '<div class="all-' + id_material +'-products">' +
                                '<h3>Produits sélectionnés</h3>' +
                                '<div class="selected-product">' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );
            };

            // Si elle est décochée
            if (!$(this).is(":checked")) {
                // On supprime la div contenant la liste des choix de matériaux correspondant au matériaux décoché
                id_material = $(this).prop('name');
                $('#' + id_material + '-select').remove();
            };
        });

        $(document).on('click', '.add-products', function() {
            var select = $(this).parents('.material-product-select').find('select');
            var products = select.find('option:selected');
            var id_material = $(this).parents('.material-product-select').find('.input-material-name').val();

            var selected_product = '';
            // Pour chaque produit sélectionné
            $.each(products, function(index, product) {
                var id_product = $(this).val();
                // Créer les sections pour chaque produit sélectionné, puis les div avec les options disponibles
                selected_product +=
                '<div class="product-options">' +
                    '<div class="product-name">' +
                        '<h3>' + allMaterial[id_material][id_product].name + '</h3>' +
                    '</div>' +
                        '<div class="product-attributes">' +
                        '<div class="items-center">' +
                        '<span class="attribute-title">Nom à afficher => &nbsp;&nbsp;</span>' +
                        '<input type="text" style="width:200px" name="' + id_material + '_' + allMaterial[id_material][id_product].id_product + '_' + id_product + '_name"  id="' + id_material + '_' + allMaterial[id_material][id_product].id_product + '_' + id_product + '_name" placeholder="' + allMaterial[id_material][id_product].name + '"/>' +
                        '</div><br />' +
                        '<div class="items-center">' +
                            '<span class="attribute-title">Image (SVG uniquement) => &nbsp;&nbsp;</span>' +
                            '<input type="file" name="' + id_material + '_' + allMaterial[id_material][id_product].id_product + '_' + id_product + '_image"  id="' + id_material + '_' + allMaterial[id_material][id_product].id_product + '_' + id_product + '_image" accept="image/svg+xml" />' +
                        '</div><br />';

                    // On vérifie si le produit a des options disponibles
                    if (allMaterial[id_material][id_product].sizes) {
                       /* allMaterial[id_material][id_product].sizes.sort(function(a, b) {
                            return a.id_attribute - b.id_attribute;
                        });*/
                        selected_product +=
                            '<div class="items-center">' +
                                '<span class="attribute-title">Tailles => &nbsp;&nbsp;</span>';
                        $.each(allMaterial[id_material][id_product].sizes, function(index, attribute) {
                                selected_product +=
                                '<div class="product-option-attribute">' +
                                    '<input type="checkbox" value="' + attribute.id_attribute + '" name="' + id_material + '_' + allMaterial[id_material][id_product].id_product + '_taille[]" id="' + id_material + '_' + allMaterial[id_material][id_product].id_product + '_' + attribute.id_attribute + '">' +
                                    '<label>&nbsp;&nbsp;' + attribute.name + ' &nbsp;&nbsp;&nbsp;&nbsp;</label>' +
                                    '<br />Position Top&nbsp;&nbsp;<input type="number" style="width:80px;" name="' + id_material + '_' + allMaterial[id_material][id_product].id_product + '_' + attribute.id_attribute  + '_top" id="' + id_material + '_' + allMaterial[id_material][id_product].id_product + '_' + attribute.id_attribute + '_top" />' +
                                    '<br />Position Left&nbsp;&nbsp;<input type="number" style="width:80px;" name="' + id_material + '_' + allMaterial[id_material][id_product].id_product + '_' + attribute.id_attribute  + '_left" id="' + id_material + '_' + allMaterial[id_material][id_product].id_product + '_' + attribute.id_attribute + '_left" />' +
                                    '<br />Hauteur max&nbsp;&nbsp;<input type="number" style="width:80px;" name="' + id_material + '_' + allMaterial[id_material][id_product].id_product + '_' + attribute.id_attribute  + '_height" id="' + id_material + '_' + allMaterial[id_material][id_product].id_product + '_' + attribute.id_attribute + '_height" />' +
                                    '<br />Largeur max&nbsp;&nbsp;<input type="number" style="width:80px;" name="' + id_material + '_' + allMaterial[id_material][id_product].id_product + '_' + attribute.id_attribute  + '_width" id="' + id_material + '_' + allMaterial[id_material][id_product].id_product + '_' + attribute.id_attribute + '_width" />' +
                                '</div>';
                        });
                        selected_product +=
                            '</div>' +
                            '<br />' +
                            '<div class="items-center">' +
                                '<span class="attribute-title">Couleurs => &nbsp;&nbsp;</span>';

                        // On créer des inputs pour chaque couleur si déjà attribuées
                        $.each(allMaterial[id_material][id_product].colors, function(index, attribute) {
                            selected_product +=
                            '<div class="items-center product-option-attribute">' +
                                '<input type="checkbox" value="' + attribute.id_attribute + '" name="' + id_material + '_' + allMaterial[id_material][id_product].id_product + '_couleurAttr[]" id="' + id_material + '_' + allMaterial[id_material][id_product].id_product + '_' + attribute.id_attribute + '">' +
                                '<label>&nbsp;&nbsp;' + attribute.name + ' &nbsp;&nbsp;&nbsp;&nbsp;</label>' +
                            '</div>';

                        });

                    };

                    selected_product +=
                        '</div>' +
                        '<br />' +
                        '<div class="recto-verso-attribute">' +
                            '<span class="attribute-title">Nombre de caractères par ligne / par taille</span><br />';

                    // Pour chaque taille, on a un input pour mettre une ligne ou deux
                    // Pour chaque ligne, on a un input pour mettre un recto et / ou un verso
                    // Pour chaque recto et verso, on a un input pour mettre le nombre de caractères par ligne
                    $.each(allMaterial[id_material][id_product].sizes, function(index, attribute) {
                      selected_product +=
                      '<div class="items-center product-option-recto-verso">' +
                          '<span class="attribute-title">' + attribute.name + ' => &nbsp;&nbsp;</span><br />' +
                          '<div class="recto-verso-lines">' +
                              // Ligne 1
                              '<div class="items-center recto-verso-line">' +
                                  '<input class="checkbox-line" type="checkbox" name="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line1" id="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line1">' +
                                  '<label>&nbsp; Ligne 1 => &nbsp;&nbsp;</label>' +

                                  // Recto
                                  '<label>&nbsp; Recto &nbsp;&nbsp;&nbsp;&nbsp;</label>' +
                                  '<input type="number" name="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line1_recto_nbCaractere" id="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line1_recto_nbCaractere">' +
                                  '<span class="recto-verso-separator"> | </span>' +

                                  // Verso
                                  '<label>&nbsp; Verso &nbsp;&nbsp;&nbsp;&nbsp;</label>' +
                                  '<input type="number" name="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line1_verso_nbCaractere" id="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line1_verso_nbCaractere">' +
                              '</div>' +

                              // Ligne 2
                              '<div class="items-center recto-verso-line">' +
                                  '<input class="checkbox-line" type="checkbox" name="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line2" id="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line2">' +
                                  '<label>&nbsp; Ligne 2 => &nbsp;&nbsp;</label>' +

                                  // Recto
                                  '<label>&nbsp; Recto &nbsp;&nbsp;&nbsp;&nbsp;</label>' +
                                  '<input type="number" name="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line2_recto_nbCaractere" id="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line2_recto_nbCaractere">' +
                                  '<span class="recto-verso-separator"> | </span>' +

                                  // Verso
                                  '<label>&nbsp; Verso &nbsp;&nbsp;&nbsp;&nbsp;</label>' +
                                  '<input type="number" name="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line2_verso_nbCaractere" id="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line2_verso_nbCaractere">' +
                              '</div>' +

                              // Ligne 3
                              '<div class="items-center recto-verso-line">' +
                                  '<input class="checkbox-line" type="checkbox" name="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line3" id="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line3">' +
                                  '<label>&nbsp; Ligne 3 => &nbsp;&nbsp;</label>' +

                                  // Recto
                                  '<label>&nbsp; Recto &nbsp;&nbsp;&nbsp;&nbsp;</label>' +
                                  '<input type="number" name="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line3_recto_nbCaractere" id="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line3_recto_nbCaractere">' +
                                  '<span class="recto-verso-separator"> | </span>' +

                                  // Verso
                                  '<label>&nbsp; Verso &nbsp;&nbsp;&nbsp;&nbsp;</label>' +
                                  '<input type="number" name="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line3_verso_nbCaractere" id="' + id_material + "_" + allMaterial[id_material][id_product].id_product + "_" + attribute.id_attribute + '_line3_verso_nbCaractere">' +
                              '</div>' +
                          '</div>' +
                      '</div>';
                    });

                selected_product +=
                        '</div>' +
                    '</div>' +
                '</div>';
            });

            // On affiche toutes les options précédemment créées pour chaque produit
            $(this).parents('.material-product-select').find('.product-options').find(".selected-product").html(
                selected_product
            );
            return true;
        });

        if ($("#detail").val() != "") {
            checkdetail($("#detail").val());
        }
    });

    function checkdetail(data) {
        var json = JSON.parse(data);

        $.each(json, function (i, material) {
            var id_material = i;

            if (material) {
                $('#' + id_material).click();
                $('textarea[name=textpopin_'+id_material+']').text(material.popin);
            }
            $.each(material.products, function (i, product) {
                var id_product = product.id_product;
                $('#allProducts option[value="' + id_product + '"]').prop('selected', true);
            });
            $('.add-products-'+id_material).click();

            $.each(material.products, function (i, product) {
                var id_product = product.id_product;
                $.each(product, function (k, attribute) {
                    if( k == 'name'){
                        $('#' + id_material + "_" + id_product + "_" + id_product + "_name").val(attribute);
                    }
                    if (k == "taille") {
                        $.each(attribute, function (l, value) {
                            $('#' + id_material + "_" + id_product + "_" + l).prop("checked", 'checked');
                            $.each(value, function (ln, val) {
                                if(ln=="top"){
                                    $('#' + id_material + "_" + id_product + "_" + l + "_top").val(val);
                                }
                                if(ln=="left"){
                                    $('#' + id_material + "_" + id_product + "_" + l + "_left").val(val);
                                }
                                if(ln=="height"){
                                    $('#' + id_material + "_" + id_product + "_" + l + "_height").val(val);
                                }
                                if(ln=="width"){
                                    $('#' + id_material + "_" + id_product + "_" + l + "_width").val(val);
                                }
                                if(ln=="ligne_1"){
                                    $('#' + id_material + "_" + id_product + "_" + l + "_line1").prop("checked", 'checked');
                                    if(val.recto!=''){
                                        $('#' + id_material + "_" + id_product + "_" + l + "_line1_recto_nbCaractere").val(val.recto);
                                    }
                                    if(val.verso!=''){
                                        $('#' + id_material + "_" + id_product + "_" + l + "_line1_verso_nbCaractere").val(val.verso);
                                    }
                                }
                                if(ln=="ligne_2"){
                                    $('#' + id_material + "_" + id_product + "_" + l + "_line2").prop("checked", 'checked');
                                    if(val.recto!=''){
                                        $('#' + id_material + "_" + id_product + "_" + l + "_line2_recto_nbCaractere").val(val.recto);
                                    }
                                    if(val.verso!=''){
                                        $('#' + id_material + "_" + id_product + "_" + l + "_line2_verso_nbCaractere").val(val.verso);
                                    }
                                }
                                if(ln=="ligne_3"){
                                    $('#' + id_material + "_" + id_product + "_" + l + "_line3").prop("checked", 'checked');
                                    if(val.recto!=''){
                                        $('#' + id_material + "_" + id_product + "_" + l + "_line3_recto_nbCaractere").val(val.recto);
                                    }
                                    if(val.verso!=''){
                                        $('#' + id_material + "_" + id_product + "_" + l + "_line3_verso_nbCaractere").val(val.verso);
                                    }
                                }
                            });
                        });
                    }

                    if (k == "couleurAttr" && k != false) {
                        $.each(attribute, function (m, value) {
                            $('#' + id_material + "_" + id_product + "_" + value).prop("checked", true);
                        });
                    }

                    if (k == "couleurConf" && k != false) {
                        $.each(attribute, function (n, value) {
                            $('#' + id_material + "_" + id_product + "_" + value).prop("checked", true);
                        });
                    }
                });
            });
        });
    };
</script>
