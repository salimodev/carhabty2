function filterCatParent($id) {

    $_data = { 'id': $id },
        $.ajax({
            type: "POST",
            url: pathFilterCategParent,
            data: $_data,
            success: function($result) {
                    console.log($result);

                }
                // beforeSend: function(){ $('#loaderAcc').show();},
                // complete: function()  { $('#loaderAcc').hide(); }
        });
}