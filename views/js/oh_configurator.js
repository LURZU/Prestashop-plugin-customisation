// Variable globale
var price_save;

$(document).ready(function () {


    // FIX : Mauvais ciblage sur le menu provoquait la disparition du panier
    // $('#module-oh_configurator-home .iqit-megamenu-container').remove();

    // Bon ciblage ajouté
    $('#module-oh_configurator-home #cbp-hrmenu').remove();


    $('.shapes').hide();

    $('.material input').change(function () {
        $(".shapes").hide();
        if ($(this).is(":checked")) {
            var id = $(this).prop('id').replace('input-', '');
            $("#shape-" + id).show();

            $("#shape-" + id+ ' input').first().click();
            $('#group_'+$(this).data('group')).val($(this).val());
        }
        recupprice();
    });

    $('.material input').first().click();

    var first_material = $('.material input').first().prop('id').substring(6);

    //function permettant de récupérer le prix d'un produit dans la base de donnée à l'aide AJAX
    function recupprice(){
        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            url: prestashop.urls.base_url + '?rand=' + new Date().getTime(),
            async: false,
            cache: false,
            dataType: 'json',
            data: {
                fc: 'module',
                module: 'oh_configurator',
                action: 'recupPrice',
                controller: 'cart',
                id_product: parseInt($('#product_page_product_id').val()),
                group: $('.group').serialize(),
                token: prestashop.static_token,
                ajax: 1,
                qty: 1
            },//error:function(e, xhr, opt){
             //alert("Error requesting " + opt.url + ": " + xhr.status + " " + xhr.statusText);
             //},
            success: function (jsonData) {
                if (jsonData.return == 'success' && jsonData.price) {
                  //Affiche le prix de l'élément selectionné
                    $('.price-container .price').text(jsonData.price);
                    //Sauvegarde le prix pour les changements de quantité
                    price_save = jsonData.price;

                }
            }
        });
    }

    recupprice();

//// Sélection de médaille ////

    $(".input-shape").on('click', function () {
        $('#quantity_wanted').val(1);
        $("#recto-1").val('');
        $("#verso-1").val('');
        $("#recto-2").val('');
        $("#verso-2").val('');
        $("#recto-3").val('');
        $("#verso-3").val('');

        $(".input-shape").parent().find("path").attr("fill", "#7992c2");
        if ($(this).is(":checked")) {

            $(this).parent().find("path").attr("fill", "#2c52a0");
            var svg = $(this).parent().find(".shape-svg-container").html();
            if ($(this).data('size')) {
                $('.all-size .displaysize').hide();

                $.each($(this).data('size'), function (e, v) {
                    $('.all-size .size-'+e).show();
                    $('.size-'+e+' .svg-container').html(svg);
                });
            }
            $('.all-size .displaysize:visible input').first().click();

            if ($(this).data('colorattr')) {
                var colorAttr = $(this).data('colorattr');

                if (colorAttr.length <= 5) {
                    $('.color-container').addClass('config-space-around');
                } else {
                    $('.color-container').removeClass('config-space-around');
                }
            }
            $('.color-choice').hide();
            $.each(colorAttr, function (index, value) {
                $(".choice-" + value).show();
            });

            $(".opt-5 .svg-container").html(svg);
            $(".opt-5 .svg-container svg")
                .removeAttr("width")
                .removeAttr("height");
            $(".opt-5 .svg-container svg").removeClass("shape-img");

            $('.color-choice:visible input').first().click();

            if($(this).data('price')){
                $('.price-container .price').text($(this).data('price'));
            }
        }

        preview_name = $(this).prop("id").substring(6);
        $("#preview-recto").removeClass().addClass("preview-recto preview-" + preview_name);
        $("#preview-verso").removeClass().addClass("preview-verso preview-" + preview_name);


        if($('input[name=choice-size]:checked').length==1){
            $('input[name=choice-size]:checked').click();
        }else{
            $('input[name=choice-size]').first().click();
        }
        if($('input[name=choice-color]:checked').length==1){
            $('input[name=choice-color]:checked').click();
        }else{
            $('input[name=choice-color]').first().click();
        }
        $('#product_page_product_id').val($(this).val());
        recupprice();
    });

    $(".opt-3 .color-choice input").on('click', function () {
        if ($(this).is(":checked")) {
            //$(".configurator-options .opt-5 svg").css("fill", $(this).data("color"));
            $(".configurator-options .opt-5 path").css("fill", $(this).data("color"));
            $('#group_'+$(this).data('group')).val($(this).val());
        }
    });

    $('#inner-wrapper').append('<div class="calltoaction-container"><div class ="coltitle"><h2 class="title">DÉCOUVREZ TOUT L’UNIVERS * POUR VOTRE CHIEN ET VOTRE CHAT SUR LA BOUTIQUE À PIERROT</h2><p class="subtitle">* alimentation, jouets, accessoires, laisses, sac de transport…</p></div><div class="colbutton"><a target="_blank" href="https://www.laboutiqueapierrot.com/">La boutique</a></div><div class="colimg"><img class="" src="https://www.laboutiqueapierrot.com/img/cms/laboutiqueapierrot-image-chat-chien-medaille.png"></div></div>');

    $('#shape-' + first_material + ' input').first().click();

    $('label#patte').on('click', function() {
      $(".preview-recto").hide();
      $(".preview-verso").show();
       $(".preview-recto span").html('');
       $('.preview-recto').css('font-size',20);
       $('#input-verso').first().click()
    });

    $(".size input").on('click', function () {
        $('#quantity_wanted').val(1)
        if ($(this).is(":checked")) {
            var configline = $('.shape input:checked').data('size');
            var configforline = configline[$(this).val()];
            $("#choose-verso").hide();
            $("#choose-recto").hide();
            $("#view-verso").hide();
            $("#viwe-recto").hide();
            if(configforline.ligne_1){
                $(".opt-4 .size-not-selected").css("display", "none");
                $(".opt-4 .no-text").css("display", "none");

                if(configforline.ligne_1.recto){
                    $("#choose-recto").show();
                    $(".recto").show();
                    $(".recto-inputs").show();
                    $(".recto-1").show();
                    $("#recto-1")
                        .prop("maxlength", configforline.ligne_1.recto)
                        .prop("placeholder", "Votre texte ici ("+configforline.ligne_1.recto+" caractères max.)");
                }else{
                    $(".recto-1").hide();
                }
                if(configforline.ligne_1.verso){
                    $("#choose-verso").show();
                    $(".verso").show();
                    $(".verso-1").show();
                    $("#verso-1")
                        .prop("maxlength", configforline.ligne_1.verso)
                        .prop("placeholder", "Votre texte ici ("+configforline.ligne_1.verso+" caractères max.)");
                }else{
                    $("#verso").hide();
                }
                $('.opt-4 .recto-verso input').first().click();
                //$('.opt-5 .recto-verso input').first().click();
            }else{
                $(".recto-1").hide();
                $("#recto-1").val('');
                $(".verso-1").hide();
                $("#verso-1").val('');
            }
            if(configforline.ligne_2){

              var htmlverso = $('#verso-1').val();
              var htmlrecto = $('#recto-1').val();

              if($('#verso-2').val()!=''){
              htmlverso = htmlverso + '<br />' + $('#verso-2').val();
              }
              if($('#verso-3').val()!=''){
                  htmlverso = htmlverso + '<br />' + $('#verso-3').val();
              }

               if($('#recto-2').val()!=''){
                   htmlrecto = htmlrecto + '<br />' + $('#recto-2').val();
               }
               if($('#recto-3').val()!='') {
                 htmlrecto = htmlrecto + '<br />' + $('#recto-3').val();
               }

                if(configforline.ligne_2.recto){
                    $(".recto-2").show();
                    $("#recto-2")
                        .prop("maxlength", configforline.ligne_2.recto)
                        .prop("placeholder", "Votre texte ici ("+configforline.ligne_2.recto+" caractères max.)");
                }else{
                    $(".recto-2").hide();
                }
                if(configforline.ligne_2.verso){
                    $(".verso-2").show();
                    $("#verso-2")
                        .prop("maxlength", configforline.ligne_2.verso)
                        .prop("placeholder", "Votre texte ici ("+configforline.ligne_2.verso+" caractères max.)");
                }else{
                    $(".verso-2").hide();
                }
            }else{
                $(".recto-2").hide();
                $("#recto-2").val('');
                $(".verso-2").hide();
                $("#verso-2").val('');
                var htmlrecto = $('#recto-1').val();
                var htmlverso = $('#verso-1').val();
            }
            if(configforline.ligne_3){
                if(configforline.ligne_3.recto){
                    $(".recto-3").show();
                    $("#recto-3")
                        .prop("maxlength", configforline.ligne_3.recto)
                        .prop("placeholder", "Votre texte ici ("+configforline.ligne_3.recto+" caractères max.)");
                }else{
                    $(".recto-3").hide();
                }
                if(configforline.ligne_3.verso){
                    $(".verso-3").show();
                    $("#verso-3")
                        .prop("maxlength", configforline.ligne_3.verso)
                        .prop("placeholder", "Votre texte ici ("+configforline.ligne_3.verso+" caractères max.)");
                }else{
                    $(".verso-3").hide();
                }
            }else{
                $(".recto-3").hide();
                  $("#recto-3").val('');
                $(".verso-3").hide();
                $("#verso-3").val('');
            }

            $(".preview-recto span").html(htmlrecto);
            $('.preview-recto').css('font-size',20);

            $(".preview-verso span").html(htmlverso);
            $('.preview-verso').css('font-size',20);


            if(!configforline.ligne_1 && !configforline.ligne_2){
                $(".opt-4 .no-text").css("display", "flex");
            }
            var size = $(this).data('size');

            $('.opt-5 .svg-container').removeClass('size-Petite').removeClass('size-Moyenne').removeClass('size-Grande');
            $('.opt-5 .svg-container').addClass('size-'+size);
            $('.opt-5 input').first().click();

            if(configforline.top || configforline.left || configforline.width || configforline.height) {
                $('.opt-5 .preview-recto, .opt-5 .preview-verso').css('transform', 'none');
            }
            if(configforline.top){
                $('.opt-5 .preview-recto, .opt-5 .preview-verso').css('top', configforline.top);
            }
            if(configforline.left){
                $('.opt-5 .preview-recto, .opt-5 .preview-verso').css('left', configforline.left);
            }
            if(configforline.width){
                $('.opt-5 .preview-recto, .opt-5 .preview-verso').css('width', configforline.width);
            }
            if(configforline.height){
                $('.opt-5 .preview-recto, .opt-5 .preview-verso').css('height', configforline.height);
            }
            $('.preview-recto, .preview-verso').css('font-size',20);
            ajusttaille('recto');
            ajusttaille('verso');
            $('#group_'+$(this).data('group')).val($(this).val());
            recupprice();
        }
        recupprice();
    });

    $('input[name=choice-size]:checked').click();

    $('.opt-4 .recto-verso input').on('click', function(){
        var id = $(this).prop('id').replace('input-', '');
        $('.recto-inputs').hide();
        $('.verso-inputs').hide();
        $('.'+id+'-inputs').show();
    });
    $('.opt-5 .recto-verso input').on('click', function(){
        var id = $(this).prop('id').replace('-view', '');
        $('#'+id).click();
    });

    $("#input-recto-view").on('click', function () {
        if ($(this).is(":checked")) {
            $(".preview-recto").show();
            $(".preview-verso").hide();

        }
    });
    $("#input-recto").on('click', function () {
        if ($(this).is(":checked")) {
            $(".preview-recto").show();
            $(".preview-verso").hide();
        }
    });
    $("#input-verso-view").on('click', function () {
        if ($(this).is(":checked")) {
            $(".preview-recto").hide();
            $(".preview-verso").show();
        }
    });
    $("#input-verso").on('click', function () {
        if ($(this).is(":checked")) {
            $(".preview-recto").hide();
            $(".preview-verso").show();

        }
    });

    $("#recto-1, #recto-2, #recto-3").on('keyup input', function () {
        var html = $('#recto-1').val();

        if($('#recto-2').val()!=''){
            html = html + '<br />' + $('#recto-2').val();
        }
          if($('#recto-3').val()!='') {
            html = html + '<br />' + $('#recto-3').val();
          }

        $(".preview-recto span").html(html);
        $('.preview-recto').css('font-size',20);
    });
    $("#verso-1, #verso-2, #verso-3").on('keyup input', function () {
        var html = $('#verso-1').val();
        if($('#verso-2').val()!=''){
            html = html + '<br />' + $('#verso-2').val();
        }
        if($('#verso-3').val()!=''){
            html = html + '<br />' + $('#verso-3').val();
        }
        $(".preview-verso span").html(html);
        $('.preview-verso').css('font-size',20);
    });


    function toNumberString(num) {
      if (Number.isInteger(num)) {
        return num + ".0"
      } else {

        return num.toString();
      }
    }

    //Détecte le change de l'input quantité et actualise le prix sur l'affichage en effectuant le calcul du nombre de médaille multiplié par leur prix
    $('#quantity_wanted').on('keyup input', function() {
      recupprice();
      var str = price_save;

      var tab = str.replace('€', '');
      tab = tab.replace(' ', '');
      tab = tab.replace(',', '.');
      let multiply = $('#quantity_wanted').val();
      let calcul = parseFloat(tab) * multiply;
      calcul= calcul.toFixed(2);
      calcul = toNumberString(calcul);
      calcul = calcul.replace('.', ',');
      calcul += " €";
      $('.price-container .price').text(calcul);
    })

    $('.btn.bootstrap-touchspin-up').on('click', function() {
      recupprice();
      var str = price_save;
      var tab = str.replace('€', '');
      tab = tab.replace(' ', '');
      tab = tab.replace(',', '.');
      let multiply = $('#quantity_wanted').val();
      let calcul = parseFloat(tab) * multiply;
      calcul = calcul.toFixed(2);
      calcul = toNumberString(calcul);
      calcul = calcul.replace('.', ',');
      calcul += " €";
      $('.price-container .price').text(calcul);
    })

    $('.btn.bootstrap-touchspin-down').on('click', function() {
      recupprice();
      var str = price_save;
      var tab = str.replace('€', '');
      tab = tab.replace(' ', '');
      tab = tab.replace(',', '.');
      let multiply = $('#quantity_wanted').val();
      let calcul = parseFloat(tab) * multiply;
      calcul = calcul.toFixed(2);
      calcul = toNumberString(calcul);
      calcul = calcul.replace('.', ',');
      calcul += " €";
      $('.price-container .price').text(calcul);
    })

    function ajusttaille(select){

        if(size='Petite') {
            $('.preview-' + select).css('font-size', "18px");
        }

        if(size='Grande') {
            $('.preview-' + select).css('font-size', "19px");
        }
    }

    $('button.add-custom').on('click', function(){

        if($('#recto-1').val()==''){
            return false;
        }

       $.ajax({
           type: 'POST',
           headers: {"cache-control": "no-cache"},
           url: prestashop.urls.base_url + '?rand=' + new Date().getTime(),
           async: false,
           cache: false,
           dataType: 'json',
           data: {
               fc: 'module',
               module: 'oh_configurator',
               action: 'addCart',
               controller: 'cart',
               id_product: parseInt($('#product_page_product_id').val()),
               group: $('.group').serialize(),
               token: prestashop.static_token,
               ajax: 1,
               recto_1: $('#recto-1').val(),
               recto_2: $('#recto-2').val(),
               recto_3: $('#recto-3').val(),
               verso_1: $('#verso-1').val(),
               verso_2: $('#verso-2').val(),
               verso_3: $('#verso-3').val(),
               sizefont: $('.preview-recto span').css('font-size'),
               qty: 1
           },
           success: function (jsonData) {
               if (jsonData.return == 'success' && jsonData.id_custom) {
                   $('#product_customization_id').val(jsonData.id_custom);
                   $('.add-to-cart').click();
               }
           }
       });
        return false;
    });
});
