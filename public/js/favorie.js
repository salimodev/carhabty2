function addToWishList() {
    AIZ.plugins.notify('warning', "S'il vous plait connectez-vous avec un compte utilisateur");
}

function addFavorie($id) {

    $_data = {
            'id': $id
        },
        $.ajax({
            type: "POST",
            url: pathaddToFavorie,
            data: $_data,
            success: function($result) {
                console.log($result);
                $total = parseInt($("#totalFav").html()) + 1;
                $("#totalFav").html($total);
                AIZ.plugins.notify('success', "Produit ajouter au liste de souhaits");
                $("." + $id).empty();
                $("." + $id).html("<a href=\"javascript:void(0)\" onclick=\"removeFromWishlist(" + $id + ");\" data-toggle=\"tooltip\" style=\"background: var(--primary);color: #fff;\" data-placement=\"left\" tabindex=\"0\"><i class=\"la la-heart-o\"></i></a>\n");

                // location.reload();
            },
            // beforeSend: function() { $('#loaderAcc1').show(); },
            //complete: function() { $('#loaderAcc1').hide(); }
        });

}

function removeFromWishlist($id) {

    $_data = { 'id': $id },
        $.ajax({
            type: "POST",
            url: pathRemoveFromFavorie,
            data: $_data,
            success: function($result) {
                    console.log($result);
                    $total = parseInt($("#totalFav").html()) - 1;
                    $("#totalFav").html($total);
                    AIZ.plugins.notify('success', "Produit supprimer de liste de souhaits");
                    $("." + $id).empty();
                    $("." + $id).html("<a href=\"javascript:void(0)\"  onclick=\"addFavorie(" + $id + ");\" data-toggle=\"tooltip\"   data-placement=\"left\" tabindex=\"0\"><i class=\"la la-heart-o\"></i></a>\n");

                }
                // beforeSend: function(){ $('#loaderAcc').show();},
                // complete: function()  { $('#loaderAcc').hide(); }
        });
}