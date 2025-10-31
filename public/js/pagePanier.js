function checkLogin() {
    $.ajax({
        url: '/login_panier',
        type: 'GET',
        success: function() {
            // Reload the page after the user logs in successfully
            location.reload();
        },
        error: function() {
            $('#login-modal').modal('show');
        }
    });
}


function hideLogin() { $('#login-modal').modal('hide'); }

function Ajouteraupanier() {


    $('.c-preloader').show();
    $_data = {}
    $.ajax({
        type: "POST",
        url: $showjson_pan,
        data: $_data,
        success: function($panier) {
            $('.c-preloader').hide();
            console.log($panier);
            var rowCat = "";
            var rowCatVide = "";
            $sumavecTax = 0;
            $amount = 0;
            if ($panier.panier.length == 0) {
                rowCatVide += "<div class=\"col-xl-8 mx-auto\">";
                rowCatVide += "<div class=\"shadow-sm bg-white p-4 rounded\">";
                rowCatVide += "<div class=\"text-center p-3\">";
                rowCatVide += "<i class=\"las la-frown la-3x opacity-60 mb-3\"></i>";
                rowCatVide += "<h3 class=\"h4 fw-700\">Votre panier vide</h3>";
                rowCatVide += "</div>";
                rowCatVide += "</div>";
                rowCatVide += "</div>";
                $('#panierpg').html(rowCatVide);
            } else {


                for (i = 0; i < $panier.panier.length; i++) {
                    $sumavecTax += ($panier.panier[i]['produit'].prix * $panier.panier[i]['qte']) + $panier.panier[i]['produit'].tax;
                    $amount = parseFloat(($panier.panier[i]['produit'].prix * $panier.panier[i]['qte']) + $panier.panier[i]['produit'].tax).toFixed(2);
                    rowCat += "<li class='list-group-item px-0 px-lg-3 artice'>";
                    rowCat += "<div class='row gutters-5'>";
                    rowCat += "<div class='col-lg-5 d-flex'>";
                    rowCat += "<span class='mr-2 ml-0'>";
                    rowCat += "<img src=" + $panier.panier[i]['produit'].banner + " class='img-fit size-60px rounded' alt=" + $panier.panier[i]['produit'].nom + "' onerror='this.src=\"assets/img/placeholder.png\"'>";
                    rowCat += "</span>";
                    rowCat += "<span class='fs-14 opacity-60'>" + $panier.panier[i]['produit'].nom + "</span>";
                    rowCat += "</div>";

                    rowCat += "<div class='col-lg col-4 order-1 order-lg-0 my-3 my-lg-0'>";
                    rowCat += "<strong class='opacity-60 fs-12 d-block d-lg-none'>Prix</strong>";
                    rowCat += "<span class='fw-600 fs-16 prix'>" + $panier.panier[i]['produit'].prix + "</span>";
                    rowCat += "<span class='fw-600 fs-16'>TND</span>";
                    rowCat += "</div>";
                    rowCat += "<div class='col-lg col-4 order-2 order-lg-0 my-3 my-lg-0' id='tax'>";
                    rowCat += "<strong class='opacity-60 fs-12 d-block d-lg-none'>Tax</strong>";
                    rowCat += "<span class='fw-600 fs-16 tax'>" + $panier.panier[i]['produit'].tax + "</span>";
                    rowCat += "<span class='fw-600 fs-16'>TND</span>";
                    rowCat += "</div>";

                    rowCat += "<div class='col-lg col-6 order-4 order-lg-0'>";
                    rowCat += "<div class='row no-gutters align-items-center aiz-plus-minus mr-2 ml-0 button-container'>";
                    rowCat += "<button type='button'  class='btn col-auto btn-icon btn-sm btn-circle btn-light cart-qty-minus' type='button'  data-field='quantity[" + $panier.panier[i]['qte'] + "]'>";
                    rowCat += "<i class='las la-minus'></i>";
                    rowCat += "</button>";
                    rowCat += "<input type='text' id='quantiteprodd' name='quantity[" + $panier.panier[i]['qte'] + "]' data-stock=" + $panier.panier[i]['produit'].stock + " data-size=" + $panier.panier[i]['produit'].size + " data-id=" + $panier.panier[i]['produit'].id + " data-old=" + $panier.panier[i]['qte'] + " data-color=" + $panier.panier[i]['produit'].couleur + " class='col border-0 text-center flex-grow-1 fs-16 input-number qty' value=" + $panier.panier[i]['qte'] + " min='1' max=" + $panier.panier[i]['produit'].stock + " readonly=''>";
                    if ($panier.panier[i]['produit'].stock == 1) {
                        rowCat += "<button type='button'  class='btn col-auto btn-icon btn-sm btn-circle btn-light cart-qty-plus' type='button'  data-field='quantity[" + $panier.panier[i]['qte'] + "]' disabled>";
                        rowCat += "<i class='las la-plus'></i>";
                        rowCat += "</button>";
                    } else {
                        rowCat += "<button type='button'  class='btn col-auto btn-icon btn-sm btn-circle btn-light cart-qty-plus' type='button'  data-field='quantity[" + $panier.panier[i]['qte'] + "]'>";
                        rowCat += "<i class='las la-plus'></i>";
                        rowCat += "</button>";
                    }

                    rowCat += "</div>";
                    rowCat += "<div class='avialable-amount opacity-60' id='available-quantity'>(<span>" + $panier.panier[i]['produit'].stock + "</span> disponible)</div>";
                    rowCat += "</div>";
                    rowCat += "<div class=\"col-lg col-4 order-3 order-lg-0 my-3 my-lg-0\">";
                    rowCat += "<strong class=\"opacity-60 fs-12 d-block d-lg-none\">TOTAL</strong>";
                    rowCat += "<span class=\"fw-600 fs-16 text-primary amount\" id=\"amount\">" + $amount + "</span><span class=\"fw-600 fs-16 text-primary\">TND</span>";
                    rowCat += "</div>";
                    rowCat += "<div class=\"col-lg-auto col-6 order-5 order-lg-0 text-right\">";
                    rowCat += "<a href=\"javascript:void(0)\" onclick=\"deleteFromCart($(this),'" + $panier.panier[i]['produit'].id + "');\" class=\"btn btn-icon btn-sm btn-soft-primary btn-circle\">";
                    rowCat += "<i class=\"las la-trash\"></i>";
                    rowCat += "</a>";
                    rowCat += "</div>";
                    rowCat += "</div>";
                    rowCat += "</li>";
                    $('#pagePanierBody').html(rowCat);
                }

            }


            $('#subtotal').html(parseFloat($sumavecTax).toFixed(2) + "<span>TND</span>");

            update_amounts();
            var incrementQty;
            var decrementQty;
            var plusBtn = $(".cart-qty-plus");
            var minusBtn = $(".cart-qty-minus");
            var incrementQty = plusBtn.click(function() {
                var $qtedispo = $(this).parent(".button-container").find(".qty").attr('data-stock');
                var $dataval = $(this).parent(".button-container").find(".qty").attr('value');
                var $last = parseInt($(this)
                    .parent(".button-container")
                    .find(".qty").val()) + 1;
                if ($last <= $qtedispo) {
                    var $n = $(this)
                        .parent(".button-container")
                        .find(".qty");
                    $n.val(Number($n.val()) + 1);

                    $quantite = 1;
                    $id = $(this).parent(".button-container").find(".qty").attr('data-id');
                    $color = $(this).parent(".button-container").find(".qty").attr('data-color');
                    $size = $(this).parent(".button-container").find(".qty").attr('data-size');
                    console.log($quantite);
                    console.log($last);
                    console.log($color);
                    console.log($size);
                    console.log($qtedispo);
                    console.log($dataval);
                    if ($size == "null") {
                        $size = null;
                    }
                    if ($color == "null") {
                        $color = null;
                    }

                    update_amounts();
                    AddPanierAPartirInputNumberFromCard($id, $quantite, $color, $size);
                }

            });
            var decrementQty = minusBtn.click(function() {
                var $qtedispo = $(this).parent(".button-container").find(".qty").attr('data-stock');
                var $dataval = $(this).parent(".button-container").find(".qty").attr('value');
                var $last = parseInt($(this)
                    .parent(".button-container")
                    .find(".qty").val());
                var $n = $(this)
                    .parent(".button-container")
                    .find(".qty");
                var QtyVal = Number($n.val());
                if ($last > 1) {
                    if (QtyVal > 1) {
                        $n.val(QtyVal - 1);
                    }
                    $quantite = 1;
                    $id = $(this).parent(".button-container").find(".qty").attr('data-id');

                    console.log($last);
                    console.log(QtyVal);

                    update_amounts();
                    RemovePanierAPartirInputNumberFromCard($id, $quantite);
                }
            });

        }
    });
}

$(document).ready(function() {
    Ajouteraupanier();
});

function AddPanierAPartirInputNumberFromCard($id, $quantite, $color, $size) {

    $_data = {
        'id': $id,
        'qte': $quantite,
        'color': $color,
        'size': $size
    }
    $.ajax({
        type: "POST",
        url: pathaddPanier,
        data: $_data,
        success: function($datarep) {

        }
    });
}

function RemovePanierAPartirInputNumberFromCard($id, $quantite) {

    $_data = { 'id': $id, 'qte': $quantite }
    $.ajax({
        type: "POST",
        url: removePanierFromDatatable,
        data: $_data,
        success: function($datarep) {

        }
    });
}