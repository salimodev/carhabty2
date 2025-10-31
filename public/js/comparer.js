function addToCompare(element, $id) {
    $total = parseInt($("#totalCOMPARE").html()) + 1;
    $("#totalCOMPARE").html($total);
    $_data = {
            'id': $id
        },
        $.ajax({
            type: "POST",
            url: pathComparer,
            data: $_data,
            success: function() {

                    AIZ.plugins.notify('success', "Produit ajouter au liste de comparaison");
                }
                //  beforeSend: function(){ $('#loaderAcc').show();},
                //  complete: function()  { $('#loaderAcc').hide(); }
        });

}