var loader = $("<div style='color: #bdd748' class=\"d-flex justify-content-center\">\n" +
    "  <div class=\"spinner-border \" role=\"status\">\n" +
    "  </div>\n" +
    "</div>");

var typingTimer; //timer identifier
var doneTypingInterval = 700; //time in ms
var $input = $('#searchinput');

$input.on('keyup', function() {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(recherche, doneTypingInterval);
});



$('#searchinput').on('keydown', function() {
    recherche();
    $("#search-content").empty();
    //console.log(loader);
    $("#loading").append(loader);
    clearTimeout(typingTimer);
});

$('#searchinput').on('focus', function() {
    recherche();
});

function recherche() {
    $_data = { 'word': $("#searchinput").val() }
    var searchKey = $('#searchinput').val();
    if (searchKey.length > 0) {
        $('body').addClass("typed-search-box-shown");

        $('.typed-search-box').removeClass('d-none');
        $('.search-preloader').removeClass('d-none');
        $.ajax({
            type: "POST",
            url: pathMotRechercher,
            data: $_data,
            success: function($datareponse) {
                console.log($datareponse);
                $("#search-content").empty();

                var $datarep = $datareponse['Products'];
                var $datashop = $datareponse['Shops'];
                if ($datarep.length == 0 && $datashop.length == 0) {
                    $('#search-content').html(null);
                    $('.search-preloader').addClass('d-none');
                    $("#loading").empty();
                    $('.typed-search-box .search-nothing')
                        .removeClass('d-none')
                        .html("<p style='color: #f73715!important;text-align: center;margin-top: 5%;font-weight: bold'>Désolé, rien n’a été trouvé pour <strong>" + searchKey + "</strong></p>");

                } else {
                    $('.typed-search-box .search-nothing').addClass('d-none').html(null);
                    $('.search-preloader').addClass('d-none');
                    var rowCat = "";
                    rowCat += "<div class='px-2 py-1 text-uppercase fs-10 text-right text-muted bg-soft-secondary'>Produits</div>";
                    for (i = 0; i < $datarep.length; i++) {
                        var PrixAchat = $datarep[i].Prix - ($datarep[i].Prix * $datarep[i].discount / 100);

                        rowCat += "<ul class='list-group list-group-raw'><li class='list-group-item' " + $datarep[i].id + " style=\"padding-left:15px;font-size: 12px;margin-top: 0%;padding-bottom: 3%;\"><a class='text-reset' href=" + pathRechercheByArticle.replace('id', $datarep[i].id) + "> <div class='d-flex search-product align-items-center'><div class='mr-3'>";
                        rowCat += "<img class='size-40px img-fit rounded' src= " + $datarep[i].banner + ">";
                        rowCat += "</div><div class='flex-grow-1 overflow--hidden minw-0'>";
                        rowCat += "<div class='product-name text-truncate fs-14 mb-5px'>" + $datarep[i].nom + "</div>";
                        rowCat += "<div class=''>"
                        if (PrixAchat != $datarep[i].Prix) {
                            rowCat += "<del class='opacity-60 fs-15'>" + $datarep[i].Prix + "<span>TND</span></del>";
                        }
                        rowCat += "<span class='fw-600 fs-16 text-primary'> " + PrixAchat + "<span>TND</span> </span>";
                        rowCat += "</div>";
                        rowCat += "</div>";
                        rowCat += "</div>";
                        rowCat += "</a> </class=></li></u>"
                    }

                    rowCat += "<div class=''>";
                    rowCat += "<div class='px-2 py-1 text-uppercase fs-10 text-right text-muted bg-soft-secondary'>Vendeurs</div>";


                    if ($datashop.length > 0) {
                        rowCat += "<ul class='list-group list-group-raw'>";

                        for (var k = 0; k < $datashop.length; k++) {
                            rowCat += "<li class='list-group-item'>";
                            rowCat += "<a class='text-reset' href='" + pathshop.replace('id', $datashop[k].id) + "'>";
                            rowCat += "<div class='d-flex search-product align-items-center'>";
                            rowCat += "<div class='mr-3'>";
                            rowCat += "<img class='size-40px img-fit rounded' src='" + $datashop[k].banner + "'>";
                            rowCat += "</div>";
                            rowCat += "<div class='flex-grow-1 overflow--hidden'>";
                            rowCat += "<div class='product-name text-truncate fs-14 mb-5px'>" + $datashop[k].nom + "</div>";
                            rowCat += "<div class='opacity-60'>" + $datashop[k].adress + "</div>";
                            rowCat += "</div>";
                            rowCat += "</div>";
                            rowCat += "</a>";
                            rowCat += "</li>";
                        }

                        rowCat += "</ul>";
                    }

                    rowCat += "</div>";
                }
                $("#loading").empty();
                $('#search-content').html(rowCat);

            }

        });
    } else {
        $('.typed-search-box').addClass('d-none');
        $('body').removeClass("typed-search-box-shown");
    }

}