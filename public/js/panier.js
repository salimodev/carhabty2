function addPlus(element) {
    $prix = parseFloat($("#prix").find('strong').text());
    $quantity = parseInt($('#quantiteprod').val()) + 1;
    $total = parseFloat($prix.toFixed(2)) * $quantity;
    $('#chosen_price').html($total.toFixed(2));
    console.log($prix);
    console.log($quantity);
    console.log($total);
}

function addMunis(element) {
    $prix = parseFloat($("#prix").find('strong').text());
    $quantity = parseInt($('#quantiteprod').val()) - 1;
    $total = parseFloat($prix.toFixed(2)) * $quantity;
    $('#chosen_price').html($total.toFixed(2));
    console.log($prix);
    console.log($quantity);
    console.log($total);
}

function addToCart(element, $id) {

    var $color = $('input[name="color"]:checked').val();
    $qte = $('#quantiteprod').val();
    $size = $('input[name="attribute_id_1"]:checked').val();
    $('.c-preloader').show();
    console.log($size);


    console.log($color);
    $_data = {
        'id': $id,
        'qte': $qte,
        'color': $color,
        'size': $size
    }

    $.ajax({
        type: "POST",
        url: pathaddPanier,
        data: $_data,
        success: function($result) {
            console.log($result);
            $('.c-preloader').hide();
            $('#addToCart').modal("show");
            AIZ.plugins.notify('success', "Produit ajouter au panier avec success");
            var rowCat = "";
            var rowcat2 = "";
            $total = 0;
            $total = $result.length;
            $sumSansTax = 0;
            $amountdrop = 0;
            $result.reverse();
            if ($result.length == 0) {
                rowCat += "<div class=\"text-center p-3\" id=\"paniervide\">";
                rowCat += "<i class=\"las la-frown la-3x opacity-60 mb-3\"></i>";
                rowCat += "<h3 class=\"h6 fw-700\">Votre panier vide</h3>";
                rowCat += "</div>";
                $('#monpanierScroll').html(rowCat);
                $('#pagePanierBody').html(rowCat);
            } else {
                rowCat += "<div class=\"\" id=\"paniercontent\">";
                rowCat += "<div class=\"p-3 fs-15 fw-600 p-3 border-bottom\">Panier</div>";

                rowCat += "<ul class=\"h-250px overflow-auto c-scrollbar-light list-group list-group-flush\" id=\"PanierBody\">";
                for (i = 0; i < $result.length; i++) {
                    $sumSansTax += ($result[i]['produit'].prix * $result[i]['qte']);
                    $amountdrop = ($result[i]['produit'].prix * $result[i]['qte']);
                    rowCat += "<li class=\"list-group-item\" id=\"listPanier\">";
                    rowCat += "<span class=\"d-flex align-items-center\">";
                    rowCat += "<a href=\"#\" class=\"text-reset d-flex align-items-center flex-grow-1\">";
                    rowCat += "<img src=\"" + $result[i]['produit'].banner + "\" data-src=\"" + $result[i]['produit'].banner + "\" class=\"img-fit size-60px rounded lazyloaded\" alt=\"\"' onerror='this.src=\"assets/img/placeholder.png\"'>";
                    rowCat += "<span class=\"minw-0 pl-2 flex-grow-1\">";
                    rowCat += "<span class=\"fw-600 mb-1 text-truncate-2\">" + $result[i]['produit'].nom + "</span>";
                    rowCat += "<span class=\"\">" + $result[i]['qte'] + "x</span>";
                    rowCat += "<span class=\"\">" + $result[i]['produit'].prix.toFixed(2) + "TND</span>";
                    rowCat += "<span class=\"fw-600 fs-16 text-primary amountdrop\" id=\"amountdrop\">" + $amountdrop.toFixed(2) + "</span><span class=\"fw-600 fs-16 text-primary\">TND</span>";
                    rowCat += "</span>";
                    rowCat += "</a>";
                    rowCat += "<span class=''>";
                    rowCat += "<button class=\"btn btn-icon btn-sm btn-soft-primary btn-circle\" onclick=\"deleteFromCart($(this)," + $result[i]['produit'].id + ")\">";
                    rowCat += "<i class=\"las la-trash\"></i>";
                    rowCat += "</button>";
                    rowCat += "</span>";
                    rowCat += "</span>";
                    rowCat += "</li>";

                }
                rowCat += "</ul>";

                rowCat += "<div class=\"px-3 py-2 fs-15 border-top d-flex justify-content-between\">";
                rowCat += "<span class=\"opacity-60\">Subtotal</span>";
                rowCat += "<span class=\"fw-600\" id=\"subtotalDropDwn\">0<span>TND</span></span>";
                rowCat += "</div>";

                rowCat += "<div class=\"px-3 py-2 text-center border-top\">";
                rowCat += "<ul class=\"list-inline mb-0\">";
                rowCat += "<li class=\"list-inline-item\">";
                rowCat += "<a onclick=\"location.href='" + pathPanier + "'\"  class=\"btn btn-primary btn-sm\" style=\"color: white;\">Voir panier</a>";
                rowCat += "</li>";
                rowCat += "</ul>";
                rowCat += "</div>";
                rowCat += "</div>";
                rowCat += "</div>";
                console.log($sumSansTax);
                //$('#addToCart-modal-body').html(rowCat);
                document.getElementById('nbPanier').innerHTML = '<span>' + $total + '</span>';
                document.getElementById('nbPanierfooter').innerHTML = '<span>' + $total + '</span>';
                $('#monpanierScroll').html(rowCat);
                $('#pagePanierBody').html(rowCat);
                $('#subtotalDropDwn').html($sumSansTax.toFixed(2) + "<span>TND</span>");
                $('#monpanierScroll').click();
                update_SubTotal();
                // $('#PanierBody').html(rowcarte);
                rowcat2 += "<div class=\"modal-body px-4 py-5 c-scrollbar-light\" style=\"\">";

                rowcat2 += "<div class=\"text-center text-success mb-4\">";
                rowcat2 += "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"36\" height=\"36\" viewBox=\"0 0 36 36\">";
                rowcat2 += "<g id=\"Group_23957\" data-name=\"Group 23957\" transform=\"translate(-6269 7766)\">";
                rowcat2 += "<path id=\"Path_28713\" data-name=\"Path 28713\" d=\"M12.8,32.8a3.6,3.6,0,1,0,3.6,3.6A3.584,3.584,0,0,0,12.8,32.8ZM2,4V7.6H5.6l6.471,13.653-2.43,4.41A3.659,3.659,0,0,0,9.2,27.4,3.6,3.6,0,0,0,12.8,31H34.4V27.4H13.565a.446.446,0,0,1-.45-.45.428.428,0,0,1,.054-.216L14.78,23.8H28.19a3.612,3.612,0,0,0,3.15-1.854l6.435-11.682A1.74,1.74,0,0,0,38,9.4a1.8,1.8,0,0,0-1.8-1.8H9.587L7.877,4H2ZM30.8,32.8a3.6,3.6,0,1,0,3.6,3.6A3.584,3.584,0,0,0,30.8,32.8Z\" transform=\"translate(6267 -7770)\" fill=\"#85b567\"></path>";
                rowcat2 += "<rect id=\"Rectangle_18068\" data-name=\"Rectangle 18068\" width=\"9\" height=\"3\" rx=\"1.5\" transform=\"translate(6284.343 -7757.879) rotate(45)\" fill=\"#fff\"></rect>";
                rowcat2 += "<rect id=\"Rectangle_18069\" data-name=\"Rectangle 18069\" width=\"3\" height=\"13\" rx=\"1.5\" transform=\"translate(6295.657 -7760.707) rotate(45)\" fill=\"#fff\"></rect>";
                rowcat2 += "</g>";
                rowcat2 += "</svg>";
                rowcat2 += "<h3 class=\"fs-28 fw-500\">Article(s) ajouté à votre panier!</h3>";
                rowcat2 += "</div>";
                for (i = 0; i < $result.length; i++) {
                    $amountdrop = ($result[i]['produit'].prix * $result[i]['qte']);
                    rowcat2 += '<div class="media mb-1">';
                    rowcat2 += '<img src="' + $result[i]['produit'].banner + '" data-src="' + $result[i]['produit'].banner + '"  class="mr-4 size-90px img-fit rounded-0 ls-is-cached lazyloaded" alt="" onerror="this.src=\'assets/img/placeholder.png\'">';
                    rowcat2 += '<div class="media-body mt-2 text-left d-flex flex-column justify-content-between">';
                    rowcat2 += '<h6 class="fs-14 fw-700 text-truncate-2">' + $result[i]['produit'].nom + '</h6>';
                    rowcat2 += '<div class="row mt-2">';
                    rowcat2 += '<div class="col-sm-3 fs-14 fw-400 text-secondary">';
                    rowcat2 += '<div>Prix x Quantité</div>';
                    rowcat2 += '</div>';
                    rowcat2 += '<div class="col-sm-9 d-flex">';
                    rowcat2 += '<div class="fs-16 fw-700 text-primary">';
                    rowcat2 += '<strong>' + $result[i]['produit'].prix.toFixed(2) + '</strong><span class="fw-600 fs-16 text-primary">TND</span>' + ' x ' + $result[i]['qte'];
                    rowcat2 += '</div>';
                    // Add delete button with fixed margin
                    // rowcat2 += '<button class="btn btn-icon btn-sm btn-soft-primary btn-circle delete-item ml-3" onclick="deleteFromCart($(this), ' + $result[i]['produit'].id + ')">';
                    // rowcat2 += '<i class="las la-trash"></i>';
                    // rowcat2 += '</button>';
                    rowcat2 += '</div>';
                    rowcat2 += '</div>';
                    rowcat2 += '</div>';
                    rowcat2 += '</div>';
                }


                rowcat2 += '<!-- Back to shopping & Checkout buttons -->'
                rowcat2 += '<div class="row gutters-5">'
                rowcat2 += '<div class="col-sm-6">'
                rowcat2 += '<button class="btn btn-warning mb-3 mb-sm-0 btn-block rounded-0 text-white" onclick="hideaddToCart();" data-dismiss="modal">Retour aux achats</button>'
                rowcat2 += '</div>'
                rowcat2 += '<div class="col-sm-6">'
                rowcat2 += '<a onclick="location.href=\'' + pathPanier + '\'" class="btn btn-primary mb-3 mb-sm-0 btn-block rounded-0" style="color:#fff">Voir panier</a>';
                rowcat2 += '</div>'
                rowcat2 += '</div>'
                rowcat2 += '</div>'
                $('#addToCart-modal-body').html(rowcat2);
            }
        },
        //  beforeSend: function(){ $('#loaderAcc').show();},
        //  complete: function()  { $('#loaderAcc').hide(); }
    });
    // console.log($id);
}




$(window).on(function() {
    //Dès qu'on clique sur #b1, on applique hide() au titre


    //Dès qu'on clique sur #b1, on applique show() au titre
    $("#voir").click(function() {
        $(".paragHistorique_p2").show();
    });
    $("#voir").click(function() {
        $("#voir").hide();
    });
    $('.MyImgFirstPosition').addClass('Myimg');
    $('.MyLineFirstPosition').addClass('MyLine');

    $('.MyLineFirstPosition').removeClass('MyLineFirstPosition');

});


function showAddToCartModal(element, $id) {
    if (!$('#modal-size').hasClass('modal-lg')) {
        $('#modal-size').addClass('modal-lg');
    }
    $('#addToCart-modal-body').html(null);
    $('#addToCart').modal("show");
    $('.c-preloader').show();
    $_data = { 'id': $id },
        $.ajax({
            type: "POST",
            context: this,
            url: pathaddToCart,
            data: $_data,
            success: function($datareponse) {
                console.log($datareponse);

                $('.c-preloader').hide();
                var rowCat = "";
                rowCat += "<div class='modal-body p-4 c-scrollbar-light'>";
                rowCat += "<div class='row'>";
                rowCat += "<div class='col-lg-6'>";
                rowCat += "<div class='row gutters-10 flex-row-reverse'>";
                rowCat += "<div class='col'>";
                rowCat += "<div class='aiz-carousel product-gallery' data-nav-for='.product-gallery-thumb' data-fade='true' data-auto-height='true'>";

                for (i = 0; i < $datareponse.attach.length; i++) {
                    rowCat += "<div class='carousel-box img-zoom rounded'>";
                    rowCat += "<img class='img-fluid lazyload' src=" + $datareponse.attach[i].path + " data-src=" + $datareponse.attach[i].path + " ' onerror='this.src=\"assets/img/placeholder.png\"'>";
                    rowCat += "</div>";
                }

                rowCat += "</div>";
                rowCat += "</div>";
                rowCat += "<div class='col-auto w-90px'>";
                rowCat += "<div class='aiz-carousel carousel-thumb product-gallery-thumb' data-items='5' data-nav-for='.product-gallery' data-vertical='true' data-focus-select='true'>";
                for (i = 0; i < $datareponse.attach.length; i++) {
                    rowCat += "<div class='carousel-box c-pointer border p-1 rounded' data-variation=''>";
                    rowCat += "<img class='lazyload mw-100 size-50px mx-auto' src=" + $datareponse.attach[i].path + " data-src=" + $datareponse.attach[i].path + " ' onerror='this.src=\"assets/img/placeholder.png\"'>";
                    rowCat += "</div>";
                }

                rowCat += "</div>";
                rowCat += " </div>";

                rowCat += "</div>";
                rowCat += "</div>";
                rowCat += "<div class='col-lg-6'>";
                rowCat += "<div class='text-left'>";
                rowCat += "<h2 class='mb-2 fs-20 fw-600'>" + $datareponse.nom + "</h2>";
                rowCat += "<div class='row no-gutters mt-3'>";
                if ($datareponse.discount > 0) {
                    rowCat += "<div class='col-2'>";
                    rowCat += "<div class='opacity-50 mt-2'>Prix:</div>";
                    rowCat += "</div>";
                    rowCat += "<div class='col-10'>";
                    rowCat += "<div class='fs-20 opacity-60'>";
                    rowCat += "<del>" + $datareponse.Prix + "TND<span class='opacity-70'>/" + $datareponse.unit + "</span></del>";
                    rowCat += "</div>";
                    rowCat += "</div>";
                }
                rowCat += "</div>";
                rowCat += "<div class='row no-gutters mt-2'>";
                rowCat += "<div class='col-2'>"
                if ($datareponse.discount > 0) {
                    rowCat += "<div class='opacity-50'>Prix bas:</div>";
                } else {
                    rowCat += "<div class='opacity-50'>Prix</div>";
                }
                rowCat += "</div>";
                rowCat += "<div class='col-10'>";
                rowCat += "<div class='' id='prix'>";
                rowCat += "<strong class='h2 fw-600 text-primary'>" + $datareponse.PrixDicount + "TND</strong>";
                rowCat += "<span class='opacity-70'>/" + $datareponse.unit + "</span>";
                rowCat += "</div>";
                rowCat += "</div>";
                rowCat += "</div>";
                rowCat += "<hr>";
                rowCat += "<form id='option-choice-form'>";
                rowCat += "<input type='hidden' name='_token' value='CXBI6Ka44etUmalbC5ciORiRtd3c9Yy3g1Wjmd2X'>";
                rowCat += "<input type='hidden' name='id' id='colorLenght' value=" + $datareponse.color.length + ">";
                rowCat += "<input type='hidden' name='id' id='atrrValLenght' value=" + $datareponse.attrval.length + ">";
                if ($datareponse.color.length > 0) {
                    rowCat += "<div class='row no-gutters'>";
                    rowCat += "<div class='col-2'>";
                    rowCat += "<div class='opacity-50 mt-2'>Couleur:</div>";
                    rowCat += "</div>";
                    rowCat += "<div class='col-10'>";
                    rowCat += "<div class='aiz-radio-inline' id='couleurChoix'>";
                    for (i = 0; i < $datareponse.color.length; i++) {
                        rowCat += "<label class='aiz-megabox pl-0 mr-2' data-toggle='tooltip' data-title=" + $datareponse.color[i].nom + " data-original-title='' title=''>";
                        rowCat += "<input type='radio' name='color' id='colors' value=" + $datareponse.color[i].nom + " checked=''>";
                        rowCat += "<span class='aiz-megabox-elem rounded d-flex align-items-center justify-content-center p-1 mb-2'>";
                        rowCat += "<span class='size-30px d-inline-block rounded' style='background:" + $datareponse.color[i].code + "'></span>";
                        rowCat += "</span>";
                        rowCat += "</label>";

                    }

                    rowCat += "</div>";
                    rowCat += "</div>";
                    rowCat += "</div>";
                    rowCat += "</div>";

                    rowCat += "<hr>";
                }
                if ($datareponse.attributeId.length > 0) {
                    for (i = 0; i < $datareponse.attributeId.length; i++) {
                        rowCat += "<div class='row no-gutters'>";
                        rowCat += "<div class='col-2'>";

                        rowCat += "<div class='opacity-50 mt-2 '>" + $datareponse.attributeId[i].nom + ":</div>";

                        rowCat += "</div>";
                        rowCat += "<div class='col-10'>";
                        rowCat += "<div class='aiz-radio-inline'>";
                        if ($datareponse.taille.taille.length > 0) {
                            for (i = 0; i < $datareponse.taille.taille.length; i++) {
                                rowCat += "<label class='aiz-megabox pl-0 mr-2'>";
                                rowCat += "<input type='radio' id='size' name='attribute_id_1' value=" + $datareponse.taille.taille[i].value + " checked=''>";
                                rowCat += "<span class='aiz-megabox-elem rounded d-flex align-items-center justify-content-center py-2 px-3 mb-2'>" + $datareponse.taille.taille[i].value + "</span>";
                                rowCat += "</label>";
                            }
                        }

                        rowCat += "</div>";
                        rowCat += "</div>";
                        rowCat += "</div>";
                    }
                    rowCat += "<hr>";
                }
                rowCat += "<div class='row no-gutters'>";
                rowCat += "<div class='col-2'>";
                rowCat += "<div class='opacity-50 mt-2'>Quantité:</div>";
                rowCat += "</div>";
                rowCat += "<div class='col-10'>";
                rowCat += "<div class='product-quantity d-flex align-items-center'>";
                rowCat += "<div class='row no-gutters align-items-center aiz-plus-minus mr-3' style='width: 130px;'>";
                rowCat += "<button class='btn col-auto btn-icon btn-sm btn-circle btn-light' type='button' onclick='addMunis($(this))' data-type='minus' data-field='quantity' disabled=''>";
                rowCat += "<i class='las la-minus'></i>";
                rowCat += "</button>";
                rowCat += "<input type='text' name='quantity' id='quantiteprod' class='col border-0 text-center flex-grow-1 fs-16 input-number' placeholder='1' value='1' min='1' max=" + $datareponse.stock + " readonly=''>";
                if ($datareponse.stock == 1) {
                    rowCat += "<button class='btn  col-auto btn-icon btn-sm btn-circle btn-light' type='button' onclick='addPlus($(this))'  data-type='plus' data-field='quantity' disabled>";
                    rowCat += "<i class='las la-plus'></i>";
                    rowCat += "</button>";
                } else {
                    rowCat += "<button class='btn  col-auto btn-icon btn-sm btn-circle btn-light' type='button' onclick='addPlus($(this))'  data-type='plus' data-field='quantity'>";
                    rowCat += "<i class='las la-plus'></i>";
                    rowCat += "</button>";
                }

                rowCat += "</div>";
                rowCat += "<div class='avialable-amount opacity-60'>(<span id='available-quantity'>" + $datareponse.stock + "</span> disponible)</div>";
                rowCat += "</div>";
                rowCat += "</div>";
                rowCat += "</div>";
                rowCat += "<hr>";
                rowCat += "<div class='row no-gutters pb-3' id='chosen_price_div'>";
                rowCat += "<div class='col-2'>";
                rowCat += "<div class='opacity-50'>Total Prix:</div>";
                rowCat += "</div>";
                rowCat += "<div class='col-10'>";
                rowCat += "<div class='product-price'>";
                rowCat += "<strong id='chosen_price' class='h4 fw-600 text-primary'>" + $datareponse.PrixDicount + "</strong><strong class='h4 fw-600 text-primary'>TND</strong>";
                rowCat += "</div>";
                rowCat += "</div>";
                rowCat += "</div>";
                rowCat += "</form>";
                if ($datareponse.stock != null) {
                    rowCat += "<div class='mt-3'>";
                    rowCat += "<button type='button' class='btn btn-primary buy-now fw-600 add-to-cart' onclick='addToCart($(this)," + $datareponse.id + ")'>";
                    rowCat += "<i class='la la-shopping-cart'></i>";
                    rowCat += "<span class='d-none d-md-inline-block'> Ajouter au panier</span>";
                    rowCat += "</button>";
                    rowCat += "</div>";
                } else {
                    rowCat += "<button type='button' class='btn btn-secondary fw-600' disabled=''>";
                    rowCat += "<i class='la la-cart-arrow-down'></i> Out of Stock </button>";
                }


                rowCat += "</div>";
                rowCat += "</div>";
                rowCat += "</div>";
                rowCat += "</div>";
                $('#addToCart-modal-body').html(rowCat);
                AIZ.plugins.slickCarousel();
                AIZ.plugins.zoom();
                AIZ.extra.plusMinus();

            }

        });

}

function deleteFromCart(element, $id) {


    element.parent().parent().parent().css('display', 'none');
    $_data = {
            'id': $id
        },
        $.ajax({
            type: "POST",
            url: pathdeletePanier,
            data: $_data,
            success: function($result) {
                AIZ.plugins.notify('success', "Produit supprimer avec success");
                console.log($result);
                $total = 0;
                $total = $result.length;
                var rowCat = "";
                var paniercard = "";
                var paniercardvide = "";
                $sumSansTax = 0;
                $sumavecTax = 0;
                $amount = 0;
                $amountdrop = 0;
                $result.reverse();
                if ($result.length == 0) {
                    rowCat += "<div class=\"text-center p-3\" id=\"paniervide\">";
                    rowCat += "<i class=\"las la-frown la-3x opacity-60 mb-3\"></i>";
                    rowCat += "<h3 class=\"h6 fw-700\">Votre panier vide</h3>";
                    rowCat += "</div>";
                    $('#monpanierScroll').html(rowCat);
                } else {
                    rowCat += "<div class=\"\" id=\"paniercontent\">";
                    rowCat += "<div class=\"p-3 fs-15 fw-600 p-3 border-bottom\">Panier</div>";

                    rowCat += "<ul class=\"h-250px overflow-auto c-scrollbar-light list-group list-group-flush\" id=\"PanierBody\">";
                    for (i = 0; i < $result.length; i++) {
                        $sumSansTax += ($result[i]['produit'].prix * $result[i]['qte']);
                        $amountdrop = ($result[i]['produit'].prix * $result[i]['qte']);
                        rowCat += "<li class=\"list-group-item\" id=\"listPanier\">";
                        rowCat += "<span class=\"d-flex align-items-center\">";
                        rowCat += "<a href=\"#\" class=\"text-reset d-flex align-items-center flex-grow-1\">";
                        rowCat += "<img src=\"" + $result[i]['produit'].banner + "\" data-src=\"" + $result[i]['produit'].banner + "\" class=\"img-fit size-60px rounded lazyloaded\" alt=\"Analog Black Dial Men's watch-32-BK-CK\"' onerror='this.src=\"assets/img/placeholder.png\"'>";
                        rowCat += "<span class=\"minw-0 pl-2 flex-grow-1\">";
                        rowCat += "<span class=\"fw-600 mb-1 text-truncate-2\">" + $result[i]['produit'].nom + "</span>";
                        rowCat += "<span class=\"\">" + $result[i]['qte'] + "x</span>";
                        rowCat += "<span class=\"\">" + $result[i]['produit'].prix.toFixed(2) + "TND</span>";
                        rowCat += "<span class=\"fw-600 fs-16 text-primary amountdrop\" id=\"amountdrop\">" + $amountdrop.toFixed(2) + "</span><span class=\"fw-600 fs-16 text-primary\">TND</span>";
                        rowCat += "</span>";
                        rowCat += "</a>";
                        rowCat += "<span class=''>";
                        rowCat += "<button class=\"btn btn-icon btn-sm btn-soft-primary btn-circle\" onclick=\"deleteFromCart($(this)," + $result[i]['produit'].id + ")\">";
                        rowCat += "<i class=\"las la-trash\"></i>";
                        rowCat += "</button>";
                        rowCat += "</span>";
                        rowCat += "</span>";
                        rowCat += "</li>";



                    }
                    rowCat += "</ul>";

                    rowCat += "<div class=\"px-3 py-2 fs-15 border-top d-flex justify-content-between\">";
                    rowCat += "<span class=\"opacity-60\">Subtotal</span>";
                    rowCat += "<span class=\"fw-600\" id=\"subtotalDropDwn\">0<span>TND</span></span>";
                    rowCat += "</div>";

                    rowCat += "<div class=\"px-3 py-2 text-center border-top\">";
                    rowCat += "<ul class=\"list-inline mb-0\">";
                    rowCat += "<li class=\"list-inline-item\">";
                    rowCat += "<a onclick=\"location.href='" + pathPanier + "'\"  class=\"btn btn-primary btn-sm\" style=\"color: white;\">Voir panier</a>";
                    rowCat += "</li>";
                    rowCat += "</ul>";
                    rowCat += "</div>";
                    rowCat += "</div>";
                    rowCat += "</div>";


                }

                if ($result.length == 0) {
                    paniercardvide += "<div class=\"col-xl-8 mx-auto\">";
                    paniercardvide += "<div class=\"shadow-sm bg-white p-4 rounded\">";
                    paniercardvide += "<div class=\"text-center p-3\">";
                    paniercardvide += "<i class=\"las la-frown la-3x opacity-60 mb-3\"></i>";
                    paniercardvide += "<h3 class=\"h4 fw-700\">Votre panier vide</h3>";
                    paniercardvide += "</div>";
                    paniercardvide += "</div>";
                    paniercardvide += "</div>";
                    $('#panierpg').html(paniercardvide);
                } else {


                    for (i = 0; i < $result.length; i++) {
                        $sumavecTax += ($result[i]['produit'].prix * $result[i]['qte']) + $result[i]['produit'].tax;
                        $amount = ($result[i]['produit'].prix * $result[i]['qte']) + $result[i]['produit'].tax;
                        paniercard += "<li class=\"list-group-item px-0 px-lg-3 artice\">";
                        paniercard += "<div class=\"row gutters-5\">";
                        paniercard += "<div class=\"col-lg-5 d-flex\">";
                        paniercard += "<span class=\"mr-2 ml-0\">";
                        paniercard += "<img src=\"" + $result[i]['produit'].banner + "\" class=\"img-fit size-60px rounded\" alt=\"" + $result[i]['produit'].nom + "' onerror='this.src=\"assets/img/placeholder.png\"'>";
                        paniercard += "</span>";
                        paniercard += "<span class=\"fs-14 opacity-60\">" + $result[i]['produit'].nom + "</span>";
                        paniercard += "</div>";

                        paniercard += "<div class=\"col-lg col-4 order-1 order-lg-0 my-3 my-lg-0\">";
                        paniercard += "<strong class=\"opacity-60 fs-12 d-block d-lg-none\">Prix</strong>";
                        paniercard += "<span class=\"fw-600 fs-16 prix\">" + $result[i]['produit'].prix + "</span>";
                        paniercard += "<span class=\"fw-600 fs-16\">TND</span>";
                        paniercard += "</div>";
                        paniercard += "<div class=\"col-lg col-4 order-2 order-lg-0 my-3 my-lg-0\" id=\"tax\">";
                        paniercard += "<strong class=\"opacity-60 fs-12 d-block d-lg-none\">Tax</strong>";
                        paniercard += "<span class=\"fw-600 fs-16 tax\">" + $result[i]['produit'].tax + "</span>";
                        paniercard += "<span class=\"fw-600 fs-16\">TND</span>";
                        paniercard += "</div>";

                        paniercard += "<div class=\"col-lg col-6 order-4 order-lg-0\">";
                        paniercard += "<div class=\"row no-gutters align-items-center aiz-plus-minus mr-2 ml-0 button-container\">";
                        paniercard += "<button type=\"button\"  class=\"btn col-auto btn-icon btn-sm btn-circle btn-light cart-qty-minus\" type=\"button\"  data-field=\"quantity[" + $result[i]['qte'] + "]\">";
                        paniercard += "<i class=\"las la-minus\"></i>";
                        paniercard += "</button>";
                        paniercard += "<input type=\"text\" name=\"quantity[" + $result[i]['qte'] + "]\" data-size=\"" + $result[i]['produit'].size + "\" data-id=\"" + $result[i]['produit'].id + "\" data-old=\"" + $result[i]['qte'] + "\" data-color=\"" + $result[i]['produit'].couleur + "\" class=\"col border-0 text-center flex-grow-1 fs-16 input-number qty\" placeholder=\"1\" value=\"" + $result[i]['qte'] + "\" min=\"1\" max=\"" + $result[i]['produit'].stock + "\" readonly=\"\">";
                        paniercard += "<button type=\"button\"  class=\"btn col-auto btn-icon btn-sm btn-circle btn-light cart-qty-plus\" type=\"button\"  data-field=\"quantity[" + $result[i]['qte'] + "]\">";
                        paniercard += "<i class=\"las la-plus\"></i>";
                        paniercard += "</button>";
                        paniercard += "</div>";
                        paniercard += "</div>";
                        paniercard += "<div class=\"col-lg col-4 order-3 order-lg-0 my-3 my-lg-0\">";
                        paniercard += "<strong class=\"opacity-60 fs-12 d-block d-lg-none\">TOTAL</strong>";
                        paniercard += "<span class=\"fw-600 fs-16 text-primary amount\" id=\"amount\">" + $amount.toFixed(2) + "</span><span class=\"fw-600 fs-16 text-primary\">TND</span>";
                        paniercard += "</div>";
                        paniercard += "<div class=\"col-lg-auto col-6 order-5 order-lg-0 text-right\">";
                        paniercard += "<a href=\"javascript:void(0)\" onclick=\"deleteFromCart($(this),'" + $result[i]['produit'].id + "');\" class=\"btn btn-icon btn-sm btn-soft-primary btn-circle\">";
                        paniercard += "<i class=\"las la-trash\"></i>";
                        paniercard += "</a>";
                        paniercard += "</div>";
                        paniercard += "</div>";
                        paniercard += "</li>";
                        $('#pagePanierBody').html(paniercard);
                    }

                }


                $('#subtotal').html($sumavecTax.toFixed(2) + "<span>TND</span>");

                console.log($sumSansTax);
                //$('#addToCart-modal-body').html(rowCat);
                document.getElementById('monpanierScroll').innerHTML = rowCat;
                document.getElementById('nbPanier').innerHTML = '<span>' + $total + '</span>';
                document.getElementById('nbPanierfooter').innerHTML = '<span>' + $total + '</span>';
                $('#subtotalDropDwn').html($sumSansTax.toFixed(2) + "<span>TND</span>");


                update_amounts();
                var incrementQty;
                var decrementQty;
                var plusBtn = $(".cart-qty-plus");
                var minusBtn = $(".cart-qty-minus");
                var incrementQty = plusBtn.click(function() {


                    var $n = $(this)
                        .parent(".button-container")
                        .find(".qty");
                    $n.val(Number($n.val()) + 1);
                    $quantite = 1;
                    if ($n == 1) {
                        $(".cart-qty-plus").prop("disabled", true);
                    }

                    $id = $(this).parent(".button-container").find(".qty").attr('data-id');
                    $color = $(this).parent(".button-container").find(".qty").attr('data-color');
                    $size = $(this).parent(".button-container").find(".qty").attr('data-size');
                    console.log($quantite);
                    console.log($id);
                    console.log($color);
                    console.log($size);
                    if ($size == "null") {
                        $size = null;
                    }
                    if ($color == "null") {
                        $color = null;
                    }

                    update_amounts();
                    AddPanierAPartirInputNumberFromCard($id, $quantite, $color, $size);
                });
                var decrementQty = minusBtn.click(function() {
                    var $n = $(this)
                        .parent(".button-container")
                        .find(".qty");
                    var QtyVal = Number($n.val());
                    if (QtyVal > 0) {
                        $n.val(QtyVal - 1);
                    }
                    $quantite = 1;
                    $id = $(this).parent(".button-container").find(".qty").attr('data-id');

                    console.log($quantite);
                    console.log($id);

                    update_amounts();
                    RemovePanierAPartirInputNumberFromCard($id, $quantite);
                });


                //  location.reload();
            },
            // beforeSend: function(){ $('#loaderAcc').show();},
            //  complete: function()  { $('#loaderAcc').hide(); }
        });

}




$(document).ready(function() {
    update_SubTotal();
    update_amounts();
    Ajouteraupanierhider();
});

function update_SubTotal() {
    $('.Drop').each(function() {
        $qteDrop = $(this).find('.qteDrop').html();
        $prixuhtDrop = $(this).find('.prixDrop').html();
        $CalculerPrixuhtnetDrop = parseFloat((($qteDrop * $prixuhtDrop)));
        $(this).find('#amountdrop').html($CalculerPrixuhtnetDrop.toFixed(2));


    });
    $subtotalDrop = 0;
    $('.Drop').each(function() {
        $subtotalDrop = $subtotalDrop + parseFloat($(this).find('#amountdrop').html());
        $('#subtotalDropDwn').html($subtotalDrop.toFixed(2) + "<span>TND</span>");

    });
}


function update_amounts() {
    $('.artice').each(function() {
        $qte = $(this).find('.qty').val();
        $prixuht = $(this).find('.prix').html();
        $tax = $(this).find('.tax').html();
        $CalculerPrixuhtnet = parseFloat((($qte * $prixuht))) + parseFloat($tax);
        $(this).find('.amount').html($CalculerPrixuhtnet.toFixed(2));

    });
    $subtotal = 0;
    $('.artice').each(function() {
        $subtotal = $subtotal + parseFloat($(this).find('.amount').html());
        $('#subtotal').html($subtotal.toFixed(2) + "<span>TND</span>");
    });
}
var incrementQty;
var decrementQty;
var plusBtn = $(".cart-qty-plus");
var minusBtn = $(".cart-qty-minus");
var incrementQty = plusBtn.click(function() {


    var $n = $(this)
        .parent(".button-container")
        .find(".qty");
    $n.val(Number($n.val()) + 1);
    $quantite = 1;

    $id = $(this).parent(".button-container").find(".qty").attr('data-id');
    $color = $(this).parent(".button-container").find(".qty").attr('data-color');
    $size = $(this).parent(".button-container").find(".qty").attr('data-size');
    console.log($quantite);
    console.log($id);
    console.log($color);
    console.log($size);


    update_amounts();
    AddPanierAPartirInputNumberFromCard($id, $quantite, $color, $size);
});
var decrementQty = minusBtn.click(function() {
    var $n = $(this)
        .parent(".button-container")
        .find(".qty");
    var QtyVal = Number($n.val());
    if (QtyVal > 0) {
        $n.val(QtyVal - 1);
    }
    $quantite = 1;
    $id = $(this).parent(".button-container").find(".qty").attr('data-id');

    console.log($quantite);
    console.log($id);

    update_amounts();
    RemovePanierAPartirInputNumberFromCard($id, $quantite);
});

function Ajouteraupanierhider() {


    $('.c-preloader').show();
    $_data = {}
    $.ajax({
        type: "POST",
        url: showjson_pan,
        data: $_data,
        success: function($result) {
            $('.c-preloader').hide();
            console.log($result);
            var rowCat = "";
            $total = 0;
            $total = $result.panier.length;
            $sumSansTax = 0;
            $amountdrop = 0;
            // $result.reverse();
            if ($total == 0) {
                rowCat += "<div class=\"text-center p-3\" id=\"paniervide\">";
                rowCat += "<i class=\"las la-frown la-3x opacity-60 mb-3\"></i>";
                rowCat += "<h3 class=\"h6 fw-700\">Votre panier vide</h3>";
                rowCat += "</div>";
                $('#monpanierScroll').html(rowCat);
            } else {
                rowCat += "<div class=\"\" id=\"paniercontent\">";
                rowCat += "<div class=\"p-3 fs-15 fw-600 p-3 border-bottom\">Panier</div>";

                rowCat += "<ul class=\"h-250px overflow-auto c-scrollbar-light list-group list-group-flush\" id=\"PanierBody\">";
                for (i = 0; i < $result.panier.length; i++) {
                    $sumSansTax += ($result.panier[i]['produit'].prix * $result.panier[i]['qte']);
                    $amountdrop = ($result.panier[i]['produit'].prix * $result.panier[i]['qte']);
                    rowCat += "<li class=\"list-group-item\" id=\"listPanier\">";
                    rowCat += "<span class=\"d-flex align-items-center\">";
                    rowCat += "<a href=\"#\" class=\"text-reset d-flex align-items-center flex-grow-1\">";
                    rowCat += "<img src=\"" + $result.panier[i]['produit'].banner + "\" data-src=\"" + $result.panier[i]['produit'].banner + "\" class=\"img-fit size-60px rounded lazyloaded\" alt=\"\"' onerror='this.src=\"assets/img/placeholder.png\"'>";
                    rowCat += "<span class=\"minw-0 pl-2 flex-grow-1\">";
                    rowCat += "<span class=\"fw-600 mb-1 text-truncate-2\">" + $result.panier[i]['produit'].nom + "</span>";
                    rowCat += "<span class=\"\">" + $result.panier[i]['qte'] + "x</span>";
                    rowCat += "<span class=\"\">" + $result.panier[i]['produit'].prix.toFixed(2) + "TND</span>";
                    rowCat += "<span class=\"fw-600 fs-16 text-primary amountdrop\" id=\"amountdrop\">" + $amountdrop.toFixed(2) + "</span><span class=\"fw-600 fs-16 text-primary\">TND</span>";
                    rowCat += "</span>";
                    rowCat += "</a>";
                    rowCat += "<span class=''>";
                    rowCat += "<button class=\"btn btn-icon btn-sm btn-soft-primary btn-circle\" onclick=\"deleteFromCart($(this)," + $result.panier[i]['produit'].id + ")\">";
                    rowCat += "<i class=\"las la-trash\"></i>";
                    rowCat += "</button>";
                    rowCat += "</span>";
                    rowCat += "</span>";
                    rowCat += "</li>";

                }
                rowCat += "</ul>";

                rowCat += "<div class=\"px-3 py-2 fs-15 border-top d-flex justify-content-between\">";
                rowCat += "<span class=\"opacity-60\">Subtotal</span>";
                rowCat += "<span class=\"fw-600\" id=\"subtotalDropDwn\">0<span>TND</span></span>";
                rowCat += "</div>";

                rowCat += "<div class=\"px-3 py-2 text-center border-top\">";
                rowCat += "<ul class=\"list-inline mb-0\">";
                rowCat += "<li class=\"list-inline-item\">";
                rowCat += "<a onclick=\"location.href='" + pathPanier + "'\"  class=\"btn btn-primary btn-sm\" style=\"color: white;\">Voir panier</a>";
                rowCat += "</li>";
                rowCat += "</ul>";
                rowCat += "</div>";
                rowCat += "</div>";
                rowCat += "</div>";
                console.log($sumSansTax);
                //$('#addToCart-modal-body').html(rowCat);lenghtpanier
                $('#panierPath').val($total);
                document.getElementById('nbPanier').innerHTML = '<span>' + $total + '</span>';
                document.getElementById('nbPanierfooter').innerHTML = '<span>' + $total + '</span>';
                document.getElementById('monpanierScroll').innerHTML = rowCat;
                $('#subtotalDropDwn').html($sumSansTax.toFixed(2) + "<span>TND</span>");
                $('#monpanierScroll').click();
                update_SubTotal();
                // $('#PanierBody').html(rowcarte);
            }

        }



    });
}