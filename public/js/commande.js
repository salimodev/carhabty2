function addNewAddressLivraison() {

    $_data = {};

    var adresse = $('#adressesh').val();
    var pays = $('#pays').val();
    var ville = $('#attrvalue').val();
    var code = $('#codePostalsh').val();
    var numero = $('#telephonesh').val();




    $_data = {
        'adresse': adresse,
        'pays': pays,
        'ville': ville,
        'code': code,
        'numero': numero
    };


    if ($('#adressesh').val() != "" && $('#pays').val() != "" && $('#attrvalue').val() != "" && $('#codePostalsh').val() != "" && $('#telephonesh').val() != "") {


        $.ajax({
            type: "POST",
            url: pathadresse,
            data: $_data,
            success: function(response) {
                $('#new-address-modal').hide();

                AIZ.plugins.notify('success', "Adresse a étè ajouter avec succés!");


                location.reload();



            },
            beforeSend: function() { $('#loaderAcc1').show(); },
            complete: function() { $('#loaderAcc1').hide(); }
        })
    } else if ($('#adressesh').val() == "") {
        AIZ.plugins.notify('danger', "champ Address vide!");
    } else if ($('#pays').val() == "") {
        AIZ.plugins.notify('danger', "champ pays vide!");
    } else if ($('#attrvalue').val() == "") {
        AIZ.plugins.notify('danger', "champ ville vide!");
    } else if ($('#codePostalsh').val() == "") {
        AIZ.plugins.notify('danger', "champ code postal vide!");
    } else {
        AIZ.plugins.notify('danger', "champ téléphone vide!");
    }
}



function edit_address(element, $id) {

    $('.modal-body-adress').html(null);
    $('#modifier_adress_modal').modal("show");
    $('.c-preloader').show();
    $_data = { 'id': $id },
        $.ajax({
            type: "POST",
            url: pathModifAdress,
            data: $_data,
            success: function($resuladress) {
                console.log($resuladress);

                $('.c-preloader').hide();
                var rowCat = "";
                rowCat += "<div class=\"p-3\">";
                rowCat += "<div class=\"row\">";
                rowCat += "<div class=\"col-md-2\">";
                rowCat += "<label>Address</label>";
                rowCat += "</div>";
                rowCat += "<div class=\"col-md-10\">";
                rowCat += "<textarea class=\"form-control mb-3\" id=\"adresseshModif\" placeholder=\"Votre Address\" rows=\"2\" name=\"address\" required=\"\">" + $resuladress.adress + "</textarea>";
                rowCat += "</div>";
                rowCat += "</div>";
                rowCat += "<div class=\"row\">";
                rowCat += "<div class=\"col-md-2\">";
                rowCat += "<label>Pays</label>";
                rowCat += "</div>";
                rowCat += "<div class=\"col-md-10\">";
                rowCat += "<div class=\"mb-3\">";
                rowCat += "<select name=\"product_ids[]\" id=\"paysshModif\" class=\"form-control product_id aiz-selectpicker\" data-live-search=\"true\" data-selected-text-format=\"count\" required>";
                rowCat += "<option  id=\"paysshModif\" value=\" + $resuladress.pays.nom + \" selected>" + $resuladress.pays.nom + "</option>";
                rowCat += "</select>";
                rowCat += "</div>";
                rowCat += "</div>";
                rowCat += "</div>";

                rowCat += "<div class=\"row\">";
                rowCat += "<div class=\"col-md-2\">";
                rowCat += "<label>Ville</label>";
                rowCat += "</div>";
                rowCat += "<div class=\"col-md-10\">";
                rowCat += "<select name=\"product_ids[]\" id=\"villeshModif\" class=\"form-control product_id aiz-selectpicker\" data-live-search=\"true\" data-selected-text-format=\"count\" required>";

                rowCat += "<option  id=\"villeshModif\" value=" + $resuladress.villeid + " selected>" + $resuladress.ville.nom + "</option>";
                for (i = 0; i < $resuladress.ville.length; i++) {
                    rowCat += "<option data-id=" + $resuladress.ville.id + " value=" + $resuladress.ville.id + ">" + $resuladress.ville.nom + "</option>";
                }

                rowCat += "</select>";
                rowCat += "</div>";
                rowCat += "</div>";
                rowCat += "<br>";

                rowCat += "<div class=\"row\">";
                rowCat += "<div class=\"col-md-2\">";
                rowCat += "<label>Code Postal</label>";
                rowCat += "</div>";
                rowCat += "<div class=\"col-md-10\">";
                rowCat += "<input type=\"number\" id=\"codePostalshModif\" value=\"" + $resuladress.codePostal + "\" class=\"form-control mb-3\" placeholder=\"Votre code postal\" name=\"postal_code\"  required=\"\">";
                rowCat += "</div>";
                rowCat += "</div>";
                rowCat += "<div class=\"row\">";
                rowCat += "<div class=\"col-md-2\">";
                rowCat += "<label>Téléphone</label>";
                rowCat += "</div>";
                rowCat += "<div class=\"col-md-10\">";
                rowCat += "<input type=\"number\" class=\"form-control mb-3\" id=\"telephoneshModif\" value=\"" + $resuladress.phone + "\" placeholder=\"Téléphone\" name=\"phone\"  required=\"\">";
                rowCat += "</div>";
                rowCat += "</div>";
                rowCat += "<div class=\"form-group text-right\">";
                rowCat += "<button type=\"button\" onclick=\"ModifierAddressseLivraison($(this)," + $resuladress.id + ");\" class=\"btn btn-sm btn-primary\">Modifier</button>";
                rowCat += "</div>";
                rowCat += "</div>";
                $('.modal-body-adress').html(rowCat);
                $('.aiz-selectpicker').selectpicker();

            }

        });

}

function ModifierAddressseLivraison(element, $id) {
    $_data = {};

    var adresse = $('#adresseshModif').val();
    var pays = $('#paysshModif').val();
    var ville = $('#villeshModif').val();
    var code = $('#codePostalshModif').val();
    var numero = $('#telephoneshModif').val();




    $_data = {
        'id': $id,
        'adresse': adresse,
        'pays': pays,
        'ville': ville,
        'code': code,
        'numero': numero
    };


    if ($('#adresseshModif').val() != "" && $('#paysshModif').val() != "" && $('#villeshModif').val() != "" && $('#codePostalshModif').val() != "" && $('#telephoneshModif').val() != "") {
        $('.c-preloader').show();
        $.ajax({
            type: "POST",
            url: pathModifadresseLiver,
            data: $_data,
            success: function(response) {
                $('#modifier_adress_modal').hide();
                $('.c-preloader').hide();
                AIZ.plugins.notify('success', "Votre Adresse a étè Modifier avec succés!");

                location.reload();

            },
            // beforeSend: function(){ $('#loaderAcc').show();},
            // complete: function()  { $('#loaderAcc').hide(); }
        })
    } else if ($('#adresseshModif').val() == "") {
        AIZ.plugins.notify('danger', "champ Address vide!");
    } else if ($('#paysshModif').val() == "") {
        AIZ.plugins.notify('danger', "champ pays vide!");
    } else if ($('#villeshModif').val() == "") {
        AIZ.plugins.notify('danger', "champ ville vide!");
    } else if ($('#codePostalshModif').val() == "") {
        AIZ.plugins.notify('danger', "champ code postal vide!");
    } else {
        AIZ.plugins.notify('danger', "champ téléphone vide!");
    }
}

function supprimerAdress(element, $id) {

    $_data = { 'id': $id },
        Swal.fire({
            title: 'êtes-vous sûr de vouloir supprimer cette adresse ',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#f62718',
            confirmButtonText: 'Supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: "POST",
                    url: pathSuppAdress,
                    data: $_data,
                    success: function(response) {
                            element.parent().parent().parent().css('display', 'none');
                            AIZ.plugins.notify('success', "Adresse supprimer");


                            //   location.reload();
                        }
                        //    beforeSend: function(){ $('#loaderAcc').show();},
                        //    complete: function()  { $('#loaderAcc').hide(); }
                });
            }
        })
}



function adressdelv() {
    var checkedval = $('input[name="address_id"]:checked').val();
    var ville = $('input[name="address_id"]:checked').parent().parent().find('.ville').html();
    var totalfac = $('#panierPath').val();
    var totalNbproduit = $('#paniertot').val();
    console.log(totalNbproduit);

    // Get the frais value of the selected address
    var initialFrais = parseFloat($('input[name="address_id"]:checked').parent().parent().find('.fraislivr').val()).toFixed(2);

    // save it in the localStorage
    localStorage.setItem('frais', initialFrais);
    console.log(initialFrais);

    // listen for change event on the radio inputs
    $('input[name="address_id"]').on('change', function() {
        // get the new frais value from the selected address
        var newFrais = parseFloat($(this).parent().parent().find('.fraislivr').val()).toFixed(2);
        // update the frais value in the localStorage
        console.log(newFrais);
        localStorage.setItem('frais', newFrais);
        // update the frais value on the page
        updateFrais();
    });

    if (checkedval != null && totalfac != 0) {
        console.log(checkedval);
        console.log(ville);
        $('.adresssection').hide();
        $('.delevryInfo').show();
        $('.confirmationsection').hide();
        $('.payementsection').hide();
        $('.shippinfo').removeClass('active');
        $('.shippinfo').addClass('done');
        $('.shippinfo').children().addClass('text-success');
        $('.shippinfo').children().removeClass('text-primary');
        $('.delvyinfo').addClass('active');
        $('.delvyinfo').children().addClass('text-primary');
        $('.delvyinfo').children().children().removeClass('opacity-50');
    } else if (checkedval == null) {
        AIZ.plugins.notify('danger', "Merci de choisir votre adresse de livraison");
    } else {
        AIZ.plugins.notify('warning', "Une erreur s'est produite, veuillez réessayer plus tard.");
    }
}

function updateFrais() {
    var frais = localStorage.getItem('frais');
    if (!isNaN(parseFloat(frais)) && isFinite(frais)) {
        $('.frais').html(parseFloat(frais).toFixed(2) + "<span>TND</span>");

        // Update the total amount
        var subtotal = parseFloat($('.subtotal').text());
        var tax = parseFloat($('.tax').text());
        var fraisLivraison = parseFloat(frais);
        var total = subtotal + tax + fraisLivraison;
        $('.totalachat').html(total.toFixed(2));
    } else {
        console.error("Invalid frais value in localStorage");
    }
}

function passToPayement() {

    $('.adresssection').hide();
    $('.delevryInfo').hide();
    $('.confirmationsection').hide();
    $('.payementsection').show();
    $('.shippinfo').removeClass('active');
    $('.shippinfo').addClass('done');
    $('.shippinfo').children().addClass('text-success');
    $('.shippinfo').children().removeClass('text-primary');
    $('.shippinfo').removeClass('active');
    $('.delvyinfo').addClass('done');
    $('.delvyinfo').children().addClass('text-success');
    $('.delvyinfo').children().removeClass('text-primary');

    $('.payementinfo').addClass('active');
    $('.payementinfo').children().addClass('text-primary');
    $('.payementinfo').children().children().removeClass('opacity-50');

}

function ProdDanspanier() {

    $_data = {}
    $('.c-preloader').show();
    $.ajax({
        type: "POST",
        url: $showjson_pan,
        data: $_data,
        success: function($panier) {
            $('.c-preloader').hide();
            console.log($panier);
            var rowCat = "";



            rowCat += "<table class=\"table\">";
            rowCat += "<thead>";
            rowCat += "<tr>";
            rowCat += "<th class=\"product-name\">Product</th>";
            rowCat += "<th class=\"product-total text-right\">TOTAL</th>";
            rowCat += "</tr>";
            rowCat += "</thead>";
            rowCat += "<tbody>";
            for (i = 0; i < $panier.panier.length; i++) {
                rowCat += "<tr class=\"cart_item\">";
                rowCat += "<td class=\"product-name\">" + $panier.panier[i]['produit'].nom + "<strong class=\"product-xxx\">×</strong>";
                rowCat += "<strong class=\"product-quantity\">" + $panier.panier[i]['qte'] + "</strong>";
                rowCat += "</td>";
                rowCat += "<td class=\"product-total text-right\">";
                rowCat += "<input class=\"pl-4 prixprod\" type=\"hidden\" value=" + $panier.panier[i]['produit'].prix + ">";
                rowCat += "<span class=\"pl-4 prixtotprod\">0TND</span>";
                rowCat += "</td>";
                rowCat += "</tr>";
            }
            rowCat += "</tbody>";
            rowCat += "</table>";

            rowCat += "<table class=\"table\">";

            rowCat += "<tfoot>";
            rowCat += "<tr class=\"cart-subtotal\">";
            rowCat += "<th>Subtotal</th>";
            rowCat += "<td class=\"text-right\">";
            rowCat += "<span class=\"fw-600 subtotal\">0TND</span>";
            rowCat += "</td>";
            rowCat += "</tr>";

            rowCat += "<tr class=\"cart-shipping\">";
            rowCat += "<th>tax</th>";
            rowCat += "<td class=\"text-right\">";
            rowCat += "<span class=\"font-italic tax\">0TND</span>";
            rowCat += "</td>";
            rowCat += "</tr>";

            rowCat += "<tr class=\"cart-shipping\">";
            rowCat += "<th>Frais de livraison</th>";
            rowCat += "<td class=\"text-right\">";
            rowCat += "<span class=\"font-italic frais\">0TND</span>";
            rowCat += "</td>";
            rowCat += "</tr>";




            rowCat += "<tr class=\"cart-total\">";
            rowCat += "<th><span class=\"strong-600\">TOTAL</span></th>";
            rowCat += "<td class=\"text-right\">";
            rowCat += "<strong><span class=\"totalachat\">0</span><span>TND</span></strong>";
            rowCat += "</td>";
            rowCat += "</tr>";
            rowCat += "</tfoot>";
            rowCat += "</table>";
            document.getElementById('table-body').innerHTML = rowCat;
            $('.cart_item').each(function() {
                $qteDrop = $(this).find('.product-quantity').html();
                $prixuhtDrop = $(this).find('.prixprod').val();
                $CalculerPrixuhtnetDrop = parseFloat((($qteDrop * $prixuhtDrop)));
                $(this).find('.prixtotprod').html($CalculerPrixuhtnetDrop.toFixed(2));
                console.log($qteDrop);
                console.log($prixuhtDrop);
                console.log($CalculerPrixuhtnetDrop);



            });
            $subtotalDrop = 0;
            $('.cart_item').each(function() {
                $subtotalDrop = $subtotalDrop + parseFloat($(this).find('.prixtotprod').html());
                $('.subtotal').html($subtotalDrop.toFixed(2) + "<span>TND</span>");


            });
            var $frais = localStorage.getItem('frais');
            console.log($frais);
            if (!isNaN(parseFloat($frais)) && isFinite($frais)) {
                // if $frais is a number, then round it to 2 decimal places
                $('.frais').html(parseFloat($frais).toFixed(2) + "<span>TND</span>");
            } else {
                // if $frais is not a number, display an error message or take other appropriate action
                console.error("$frais is not a number");
            }

            $tax = parseFloat($('.tax').html());
            $frais = parseFloat($('.frais').html());
            $totalachat = parseFloat($subtotalDrop) + parseFloat($frais) + parseFloat($tax);
            $('.totalachat').html($totalachat.toFixed(2));
        }
    });
}
$(document).ready(function() {
    var initialFrais = getInitialFraisValue();
    localStorage.setItem('frais', initialFrais);
    ProdDanspanier();
});

$('input[name="address_id"]').on('change', function() {
    var newFrais = parseFloat($(this).parent().parent().find('.fraislivr').val()).toFixed(2);
    localStorage.setItem('frais', newFrais);
    updateFrais();
});

function getInitialFraisValue() {
    var selectedAddressFrais = parseFloat($('input[name="address_id"]:checked').parent().parent().find('.fraislivr').val());
    if (!isNaN(selectedAddressFrais)) {
        return selectedAddressFrais.toFixed(2);
    }
    // Set a default value if no address is selected or the frais value is invalid
    return '0.00';
}










function commande() {



    var $adressShopping = $('input[name="address_id"]:checked').val();
    var $frais = parseFloat($('.frais').html());
    var $payment = $('input[name="payment_option"]:checked').val();
    var $ville = $('input[name="address_id"]:checked').parent().parent().find('.ville').html();
    var $totalNbproduit = $('#paniertot').val();
    console.log($adressShopping);


    $_data = {
        'adressShopping': $adressShopping,
        'frais': $frais,
        'payment': $payment,
        'ville': $ville,
        'nbItems': $totalNbproduit,
    }
    if ($('#radiolivraison').is(':checked')) {

        $.ajax({
            type: "POST",
            url: pathcommande,
            data: $_data,
            success: function($result) {
                console.log($result);
                $('.adresssection').hide();
                $('.delevryInfo').hide();
                $('.payementsection').hide();
                $('.confirmationsection').show();
                $('.shippinfo').removeClass('active');
                $('.shippinfo').addClass('done');
                $('.shippinfo').children().addClass('text-success');
                $('.shippinfo').children().removeClass('text-primary');
                $('.shippinfo').removeClass('active');
                $('.delvyinfo').addClass('done');
                $('.delvyinfo').children().addClass('text-success');
                $('.delvyinfo').children().removeClass('text-primary');

                $('.payementinfo').removeClass('active');
                $('.payementinfo').addClass('done');
                $('.payementinfo').children().addClass('text-success');
                $('.payementinfo').children().removeClass('text-primary');
                $('.payementinfo').removeClass('active');

                $('.confiramtion').addClass('active');
                $('.confiramtion').children().addClass('text-primary');
                $('.confiramtion').children().children().removeClass('opacity-50');
                AIZ.plugins.notify('success', "Votre commande a étè créer avec succés!");
                var rowCat = "";
                var panierVide = "";
                $total = $result.length;
                var date = new Date();
                var $nbPanier = 0;
                for (i = 0; i < $result.cmd.length; i++) {
                    rowCat += "<div class=\"container text-left\">";
                    rowCat += "<div class=\"row\">";
                    rowCat += "<div class=\"col-xl-8 mx-auto\">";
                    rowCat += "<div class=\"card shadow-sm border-0 rounded\">";
                    rowCat += "<div class=\"card-body\">";
                    rowCat += "<div class=\"text-center py-4 mb-4\">";
                    rowCat += "<i class=\"la la-check-circle la-3x text-success mb-3\"></i>";
                    rowCat += "<h1 class=\"h3 mb-3 fw-600\">Nous vous remercions de votre commande!</h1>";
                    rowCat += "<h2 class=\"h5\">Code de commande: <span class=\"fw-700 text-primary\">" + $result.cmd[i].code + "</span></h2>";
                    rowCat += "<p class=\"opacity-70 font-italic\">Une copie ou le récapitulatif de votre commande a été envoyé à " + $result.cmd[i].email + "</p>";
                    rowCat += "</div>";
                    rowCat += "<div class=\"mb-4\">";
                    rowCat += "<h5 class=\"fw-600 mb-3 fs-17 pb-2\">Récapitulatif de la commande</h5>";
                    rowCat += "<div class=\"row\">";
                    rowCat += "<div class=\"col-md-6\">";
                    rowCat += "<table class=\"table\">";
                    rowCat += "<tbody><tr>";
                    rowCat += "<td class=\"w-50 fw-600\">Code de commande:</td>";
                    rowCat += "<td>" + $result.cmd[i].code + "</td>";
                    rowCat += "</tr>";
                    rowCat += "<tr>";
                    rowCat += "<td class=\"w-50 fw-600\">Nom:</td>";
                    rowCat += "<td>" + $result.cmd[i].nom + "</td>";
                    rowCat += "</tr>";
                    rowCat += "<tr>";
                    rowCat += "<td class=\"w-50 fw-600\">Email:</td>";
                    rowCat += "<td>" + $result.cmd[i].email + "</td>";
                    rowCat += "</tr>";
                    rowCat += "<tr>";
                    rowCat += "<td class=\"w-50 fw-600\">Adresse de livraison:</td>";
                    rowCat += "<td>" + $result.cmd[i].adress + ", " + $result.cmd[i].ville + ", " + $result.cmd[i].pays + "</td>";
                    rowCat += "</tr>";
                    rowCat += "</tbody></table>";
                    rowCat += "</div>";
                    rowCat += "<div class=\"col-md-6\">";
                    rowCat += "<table class=\"table\">";
                    rowCat += "<tbody>";
                    rowCat += "<tr>";
                    rowCat += "<td class=\"w-50 fw-600\">Statut de la commande:</td>";
                    rowCat += "<td>" + $result.cmd[i].statut + "</td>";
                    rowCat += "</tr>";
                    rowCat += "<tr>";
                    rowCat += "<td class=\"w-50 fw-600\">Montant total de la commande:</td>";
                    rowCat += "<td>" + $result.cmd[i].total + "TND</td>";
                    rowCat += "</tr>";

                    rowCat += "<tr>";
                    rowCat += "<td class=\"w-50 fw-600\">Mode de paiement:</td>";
                    rowCat += "<td>Paiement à la livraison</td>";
                    rowCat += "</tr>";
                    rowCat += "</tbody></table>";
                    rowCat += "</div>";
                    rowCat += "</div>";
                    rowCat += "</div>";
                    rowCat += "<div>";
                    rowCat += "<h5 class=\"fw-600 mb-3 fs-17 pb-2\">Détails de la commande</h5>";
                    rowCat += "<div>";
                    rowCat += "<table class=\"table table-responsive-md\">";
                    rowCat += "<thead>";
                    rowCat += "<tr>";
                    rowCat += "<th>#</th>";
                    rowCat += "<th width=\"30%\">Produit</th>";
                    rowCat += "<th>Couleur</th>";
                    rowCat += "<th>Taille</th>";
                    rowCat += "<th>Quantite</th>";
                    rowCat += "<th>Prix</th>";
                    rowCat += "<th class=\"text-right\">TOTAL</th>";
                    rowCat += "</tr>";
                    rowCat += "</thead>";
                    rowCat += "<tbody>";
                    //  for (i = 0; i < $result.cmd[i].produit.length; i++) {
                    rowCat += "<tr>";
                    rowCat += "<td>" + $result.cmd[i].produit[0].id + "</td>";
                    rowCat += "<td>";
                    rowCat += "<a href=\"#\" class=\"text-reset\"> " + $result.cmd[i].produit[0].nom + " </a>";
                    rowCat += "</td>";
                    if ($result.cmd[i].produit[0].couleur != null) {
                        rowCat += "<td>" + $result.cmd[i].produit[0].couleur + "</td>";
                    } else {
                        rowCat += "<td></td>";
                    }
                    if ($result.cmd[i].produit[0].taille != null) {
                        rowCat += "<td>" + $result.cmd[i].produit[0].taille + "</td>";
                    } else {
                        rowCat += "<td></td>";
                    }
                    rowCat += "<td>" + $result.cmd[i].produit[0].qte + "</td>";
                    rowCat += "<td>" + $result.cmd[i].produit[0].prix + "TND</td>";
                    rowCat += "<td class=\"text-right\">" + $result.cmd[i].produit[0].prix * $result.cmd[i].produit[0].qte + "TND</td>";
                    rowCat += "</tr>";
                    //   }
                    rowCat += "</tbody>";
                    rowCat += "</table>";
                    rowCat += "</div>";
                    rowCat += "<div class=\"row\">";
                    rowCat += "<div class=\"col-xl-5 col-md-6 ml-auto mr-0\">";
                    rowCat += "<table class=\"table \">";
                    rowCat += "<tbody>";
                    rowCat += "<tr>";
                    rowCat += "<th>Subtotal</th>";
                    rowCat += "<td class=\"text-right\">";
                    rowCat += "<span class=\"fw-600\">" + $result.cmd[i].subTotal + "TND</span>";
                    rowCat += "</td>";
                    rowCat += "</tr>";
                    rowCat += "<tr>";
                    rowCat += "<th>Frais de livraison</th>";
                    rowCat += "<td class=\"text-right\">";
                    rowCat += "<span class=\"font-italic\">" + $result.cmd[i].frais + "TND</span>";
                    rowCat += "</td>";
                    rowCat += "</tr>";
                    rowCat += "<tr>";
                    rowCat += "<th>tax</th>";
                    rowCat += "<td class=\"text-right\">";
                    rowCat += "<span class=\"font-italic\">0 TND</span>";
                    rowCat += "</td>";
                    rowCat += "</tr>";
                    rowCat += "<tr>";
                    rowCat += "<th><span class=\"fw-600\">TOTAL</span></th>";
                    rowCat += "<td class=\"text-right\">";
                    rowCat += "<strong><span>" + $result.cmd[i].total + "TND</span></strong>";
                    rowCat += "</td>";
                    rowCat += "</tr>";
                    rowCat += "</tbody>";
                    rowCat += "</table>";
                    rowCat += "</div>";
                    rowCat += "</div>";
                    rowCat += "</div>";
                    rowCat += "</div>";
                    rowCat += "</div>";
                    rowCat += "</div>";
                    rowCat += "</div>";
                    rowCat += "</div>";
                }
                panierVide += "<div class=\"text-center p-3\" id=\"paniervide\">";
                panierVide += "<i class=\"las la-frown la-3x opacity-60 mb-3\"></i>";
                panierVide += "<h3 class=\"h6 fw-700\">Votre panier vide</h3>";
                panierVide += "</div>";
                document.getElementById('monpanierScroll').innerHTML = panierVide;
                document.getElementById('confirmation').innerHTML = rowCat;
                document.getElementById('nbPanier').innerHTML = '<span>' + $nbPanier + '</span>';
                document.getElementById('nbPanierfooter').innerHTML = '<span>' + $nbPanier + '</span>';
            },
            beforeSend: function() { $('#loaderAcc1').show(); },
            complete: function() { $('#loaderAcc1').hide(); }
        })
    } else {
        AIZ.plugins.notify('danger', "merci d'accepter nos Terms et comditions!");
    }

}