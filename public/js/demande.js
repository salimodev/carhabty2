


document.addEventListener('DOMContentLoaded', function() {
    const emailCheckbox = document.getElementById('choix_email');
    const compteCheckbox = document.getElementById('choix_compte');
    const emailInput = document.getElementById('emailrecv');

    // Quand on coche "email"
    emailCheckbox.addEventListener('change', function() {
        if (this.checked) {
            compteCheckbox.checked = false;
            emailInput.disabled = false;
        } else {
            emailInput.disabled = true;
        }
    });

    // Quand on coche "compte"
    compteCheckbox.addEventListener('change', function() {
        if (this.checked) {
            emailCheckbox.checked = false;
            emailInput.disabled = true;
        }
    });

    // Désactive le champ email par défaut
    emailInput.disabled = true;
}); 
	function piece() {
    // Récupération des valeurs pour vérification
    let marque = $('input[name="element_0"]').val().trim();
   let modele = $('input[name="element_1"]').val().trim();
    let chassis = $('input[name="element_2"]').val().trim();
    let NewPhotos2 = $('#srcLogo2').val();

	 if ($('#essence').is(':checked')){
         var carbu = 'essence'
  }else if ($('#diesel').is(':checked')){
         var carbu = 'diesel'
    }else if ($('#hybride').is(':checked')){
       var carbu = 'hybride'
    }else if ($('#electrique').is(':checked')){
       var carbu = 'electrique'
}else{
        var carbu = ''
	}

if ($('#Origine').is(':checked')){
         var etatmoteur = 'Origine'
    }else if ($('#Changé').is(':checked')){
         var etatmoteur = 'Changé'
     }else if ($('#autreetat').is(':checked')){
       var etatmoteur = 'autreetat'
	}else{
 var etatmoteur = ''
	}

    // Validation basique
   if (marque === ""){
         AIZ.plugins.notify('danger', "Merci de remplire le champ marque");
		 return;
   } else if(modele === "" ){
	    AIZ.plugins.notify('danger', "Merci de remplire le champ modèle");
		return;
 }else if (chassis === "" ){
	AIZ.plugins.notify('danger', "Merci de remplire tous le champ n° chassis");
	return;
   }else if (carbu === ""){
		AIZ.plugins.notify('danger', "Merci de choisi type du carburant");
		return;
   }else if(etatmoteur === ""){
		AIZ.plugins.notify('danger', "Merci de choisi l'etat du moteur");
     return;
   }

    // --- Gestion des sections ---
    $('.autoinfo').hide();     // Masquer la section véhicule
    $('.piece').show();             // Afficher la section des pièces demandées
    $('.delevryInfo').hide();
    $('.confirmationsection').hide();
    $('.payementsection').hide();

    // --- Gestion du stepper ---
    $('.auto').addClass('done').removeClass('active'); // Étape véhicule terminée
    $('.auto').children().addClass('text-success').removeClass('text-light');
    $('.auto').children().children().removeClass('opacity-50');

    $('.pieceauto').addClass('active'); // Étape suivante (pièces demandées)
    $('.pieceauto').children().addClass('text-primary');
    $('.pieceauto').children().children().removeClass('opacity-50');

    // --- Remonter la page ---
    $('html, body').animate({ scrollTop: 0 }, 'slow');
}

  

	$(".switch1:not([checked])").on('change', function () {
$(".switch1").not(this).prop("checked", false);

});

$(".switch:not([checked])").on('change', function () {
$(".switch").not(this).prop("checked", false);

});
 
 var piecesTemp = [];

function ajouterpiece() {
    let designation = $('#convert_text').val();
    let reference = $('#reference').val();
    let photo = $('#srcLogo').val();
    let observation = $('#observation').val();

    if (designation.trim() === "") {
        AIZ.plugins.notify('danger', "Champ désignation vide !");
        return;
    }

    if (observation.trim() === "") {
        AIZ.plugins.notify('danger', "Champ observation vide !");
        return;
    }

    let data = {
        designation: designation,
        reference: reference,
        photo: photo,
        observation: observation
    };

    $.ajax({
        type: "POST",
        url: pathaddpiece,
        data: data,
        success: function (result) {
            piecesTemp.push(result.id); 
            $('#new-address-modal').hide();
$('.modal-backdrop').remove();
$('body').removeClass('modal-open');
$('body').css('overflow', 'auto');
            AIZ.plugins.notify('success', "Pièce ajoutée avec succès !");

            // 🔹 Ajouter la nouvelle ligne dans DataTable directement
            tablePieces.row.add([               // ✅ id ajouté
                designation,
                reference || '-',
                photo
                    ? `<img src="${photo}" alt="photo" style="width:60px;height:60px;border-radius:6px;">`
                    : '—',
                observation,
             //  `<button class="btn btn-danger btn-sm"
        // onclick="supprimerPiece($(this), '${result.id}')"
        // style="padding: 0.416rem !important;
          //      width: calc(2.02rem + 2px);
           //     height: calc(2.02rem + 2px);">
   //  <i class="la la-trash"></i>
 //</button>`

            ]).draw(false);

            // 🔹 Réinitialiser le formulaire
            $('#convert_text').val('');
            $('#reference').val('');
            $('#srcLogo').val('');
            $('#observation').val('');
            $('#logoImg').hide();
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            AIZ.plugins.notify('danger', "Erreur lors de l’ajout de la pièce !");
        },
    });

}

// 🔹 Événement clic pour supprimer une pièce

function destinataire() {
    // Récupération des valeurs pour vérification
   
// Validation basique
 if (piecesTemp.length === 0) {
        AIZ.plugins.notify('danger', "Merci d'ajouter au moins une pièce avant de continuer !");
        return;
    }



    // --- Gestion des sections ---
    $('.autoinfo').hide();     // Masquer la section véhicule
    $('.piece').hide();             // Afficher la section des pièces demandées
    $('.destinataire').show();
    $('.confirmationsection').hide();
    $('.payementsection').hide();

    // --- Gestion du stepper ---
    $('.auto').addClass('done').removeClass('active'); // Étape véhicule terminée
    $('.auto').children().addClass('text-success').removeClass('text-light');
    $('.auto').children().children().removeClass('opacity-50');

     $('.pieceauto').addClass('done').removeClass('active'); // Étape véhicule terminée
    $('.pieceauto').children().addClass('text-success').removeClass('text-light');
    $('.pieceauto').children().children().removeClass('opacity-50');


    $('.dest').addClass('active'); // Étape suivante (pièces demandées)
    $('.dest').children().addClass('text-primary');
    $('.dest').children().children().removeClass('opacity-50');

    // --- Remonter la page ---
    $('html, body').animate({ scrollTop: 0 }, 'slow');
}


function reception() {
    // Récupération des valeurs pour vérification
   
let vendeurNeuf = $('input[name="vendeurneuf"]').is(':checked');
    let vendeurOccasion = $('input[name="vendeuroccasion"]').is(':checked');

	if (!vendeurNeuf && !vendeurOccasion) {
        AIZ.plugins.notify('danger', "Merci de sélectionner au moins un type de vendeur (neuf ou occasion).");
        return;
    }

    // Validation basique
   // if (marque === "" || modele === "" || chassis === "") {
      //   AIZ.plugins.notify('danger', "Merci de remplire tous les champs obligatoires");
      //  return;
   // }

    // --- Gestion des sections ---
    $('.autoinfo').hide();     // Masquer la section véhicule
    $('.piece').hide();             // Afficher la section des pièces demandées
    $('.destinataire').hide();
    $('.reception').show();
    $('.payementsection').hide();

    // --- Gestion du stepper ---
    $('.auto').addClass('done').removeClass('active'); // Étape véhicule terminée
    $('.auto').children().addClass('text-success').removeClass('text-light');
    $('.auto').children().children().removeClass('opacity-50');

     $('.pieceauto').addClass('done').removeClass('active'); // Étape véhicule terminée
    $('.pieceauto').children().addClass('text-success').removeClass('text-light');
    $('.pieceauto').children().children().removeClass('opacity-50');

     $('.dest').addClass('done').removeClass('active'); // Étape véhicule terminée
    $('.dest').children().addClass('text-success').removeClass('text-light');
    $('.dest').children().children().removeClass('opacity-50');

     $('.recep').addClass('active'); // Étape suivante (pièces demandées)
    $('.recep').children().addClass('text-primary');
    $('.recep').children().children().removeClass('opacity-50');

    // --- Remonter la page ---
    $('html, body').animate({ scrollTop: 0 }, 'slow');
}

function envoyerdemande() {
    // --- Récupération des infos véhicule ---
    let marque = $('input[name="element_0"]').val().trim();
    let modele = $('input[name="element_1"]').val().trim();
    let numerochassis = $('input[name="element_2"]').val().trim();
    let NewPhotos2 = $('#srcLogo2').val();

    // --- Carburant ---
    let carburant = '';
    if ($('#essence').is(':checked')) carburant = 'essence';
    else if ($('#diesel').is(':checked')) carburant = 'diesel';
    else if ($('#hybride').is(':checked')) carburant = 'hybride';
    else if ($('#electrique').is(':checked')) carburant = 'electrique';

    // --- État moteur ---
    let etatmoteur = '';
    if ($('#Origine').is(':checked')) etatmoteur = 'Origine';
    else if ($('#Changé').is(':checked')) etatmoteur = 'Changé';
    else if ($('#autreetat').is(':checked')) etatmoteur = 'autreetat';

    // --- Réception des offres ---
    let choixEmail = $('#choix_email').is(':checked');
    let choixCompte = $('#choix_compte').is(':checked');
    let email = $('#emailrecv').val().trim();
    let user_id = $('#user_id').val();

    // --- Vérifications ---
    if (!choixEmail && !choixCompte) {
        AIZ.plugins.notify('danger', "Veuillez choisir au moins une option pour recevoir les offres.");
        return false;
    }

    if (choixEmail) {
        if (email === "") {
            AIZ.plugins.notify('danger', "Veuillez saisir votre adresse e-mail.");
            $('#emailrecv').focus();
            return false;
        }

        let regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regexEmail.test(email)) {
            AIZ.plugins.notify('danger', "Adresse e-mail invalide !");
            $('#emailrecv').focus();
            return false;
        }
    }

    if (choixCompte && !user_id) {
        AIZ.plugins.notify('danger', "Vous devez avoir un compte pour recevoir les offres ici.");
        return false;
    }

    // --- Vendeur / zone ---
    let zone = $('#zone').val();
    let vendNeuf = $('#vendNeuf').is(':checked') ? 1 : 0;
    let vendOcc = $('#vendOcc').is(':checked') ? 1 : 0;

    // --- Vérifier les pièces ---
    if (piecesTemp.length === 0) {
        AIZ.plugins.notify('danger', "Merci d’ajouter au moins une pièce avant d’envoyer la demande !");
        return false;
    }

    // --- Préparer les données à envoyer ---
    let data = {
        marque: marque,
        modele: modele,
        numerochassis: numerochassis,
        NewPhotos2: NewPhotos2,
        carburant: carburant,
        etatmoteur: etatmoteur,
        pieces: piecesTemp,
        vendNeuf: vendNeuf,
        vendOcc: vendOcc,
        zone: zone,
        email: email,
        user_id: user_id
    };

    // ✅ --- Envoi AJAX ---
    $.ajax({
        type: "POST",
        url: pathenvoyerdemande,
        data: data,
        beforeSend: function () {
            $('#loaderAcc1').show();
        },
        success: function (response) {
            AIZ.plugins.notify('success', "Votre demande a été envoyée avec succès !");

            // --- Masquer les anciennes sections ---
            $('.autoinfo, .piece, .destinataire, .reception').hide();
            $('.recep').addClass('done').removeClass('active');
            $('.recep').children().addClass('text-success').removeClass('text-light');
            $('.recep').children().children().removeClass('opacity-50');
			$('.confirmationsection').show();

            // --- Stepper ---
            $('.confirm').addClass('done').removeClass('active');
            $('.confirm').children().addClass('text-success').removeClass('text-light');
            $('.confirm').children().children().removeClass('opacity-50');

            // --- Construction du HTML ---
            let html = "";
            html += "<div class='container text-left'>";
            html += "<div class='row'>";
            html += "<div class='col-xl-8 mx-auto'>";
            html += "<div class='card shadow-sm border-0 rounded'>";
            html += "<div class='card-body'>";
            html += "<div class='text-center py-4 mb-4'>";
            html += "<i class='la la-check-circle la-3x text-success mb-3' style='font-size: 78px;'></i>";
            html += "<h1 class='h3 mb-3 fw-600'>Merci pour votre demande !</h1>";
            html += "<h2 class='h5'>Code de demande : <span class='fw-700 text-primary'>" + response.code + "</span></h2>";
            html += "<p class='opacity-70 font-italic'>Un e-mail de confirmation a été envoyé à <strong>" + response.reception + "</strong>.</p>";
            html += "<p class='opacity-70 font-italic'>Votre demande sera vérifiée et publiée par l’administrateur dans un délai maximum de 24 heures.</p>";
            
            html += "</div>";

           html += "<div class='mb-4'>";
html += "<h5 class='fw-600 mb-3 fs-17 pb-2'>Récapitulatif de la demande</h5>";
html += "<div class='row'>"; // ✅ deux colonnes côte à côte

// --- Première colonne ---
html += "<div class='col-md-6'>";
html += "<table class='table'>";
html += "<tbody>";
html += "<tr><td class='fw-600'>Marque :</td><td>" + response.marque + "</td></tr>";
html += "<tr><td class='fw-600'>Modèle :</td><td>" + response.modele + "</td></tr>";
html += "<tr><td class='fw-600'>Zone :</td><td>" + response.zone + "</td></tr>";
html += "<tr><td class='fw-600'>Statut :</td><td>" + response.statut + "</td></tr>";
html += "</tbody>";
html += "</table>";
html += "</div>";

// --- Deuxième colonne ---
html += "<div class='col-md-6'>";
html += "<table class='table'>";
html += "<tbody>";
html += "<tr><td class='fw-600'>N° Châssis :</td><td>" + response.chassis + "</td></tr>";
html += "<tr><td class='fw-600'>Énergie / Carburant :</td><td>" + response.energie + "</td></tr>";
html += "<tr><td class='fw-600'>État du moteur :</td><td>" + response.etatmoteur + "</td></tr>";
if (response.grise) {
    html += "<tr><td class='fw-600'>Photo carte grise :</td><td><img src='" + response.grise + "' alt='carte grise' style='width:222px;height:135px;border-radius:6px;object-fit:cover;'></td></tr>";
} else {
    html += "<tr><td class='fw-600'>Photo carte grise :</td><td>-</td></tr>";
}
html += "</tbody>";
html += "</table>";
html += "</div>";

html += "</div>"; // fin row
html += "</div>"; // fin bloc

            // --- Détails des pièces ---
            html += "<div>";
            html += "<h5 class='fw-600 mb-3 fs-17 pb-2'>Pièces demandées</h5>";
            html += "<table class='table table-responsive-md'>";
            html += "<thead><tr><th>#</th><th>Désignation</th><th>Référence</th><th>Photo</th><th>Observation</th></tr></thead>";
            html += "<tbody>";

for (let i = 0; i < response.pieces.length; i++) {
    const p = response.pieces[i];
    html += "<tr>";
    html += "<td>" + (i + 1) + "</td>";
    html += "<td>" + (p.designation ?? '-') + "</td>";
    html += "<td>" + (p.reference ?? '-') + "</td>";
   let photoPiece = "-";
if (p.photo && typeof p.photo === "string" && p.photo.trim() !== "") {
    photoPiece = "<img src='" + p.photo + "' alt='photo' style='width:60px;height:60px;border-radius:6px;object-fit:cover;'>";
}
html += "<td>" + photoPiece + "</td>";

    html += "<td>" + (p.observation ?? '-') + "</td>";
    html += "</tr>";
}

            html += "</tbody>";
            html += "</table>";
            html += "</div></div></div></div></div>";

            // ✅ Afficher dans le DOM
            document.getElementById('confirmation').innerHTML = html;

            $('html, body').animate({ scrollTop: 0 }, 'slow');
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            AIZ.plugins.notify('danger', "Erreur lors de l’envoi de la demande !");
        },
        complete: function () {
            $('#loaderAcc1').hide();
        }
    });
}

function cloudDinaryimageCINrecto() {
        $('#cloudinaryPreloader').show();
  cloudinary.openUploadWidget({
    cloudName: 'b-ja',
    uploadPreset: 'aladdineshop',
    folder: 'ml_default',
    theme: 'minimal',
    maxFileSize: 3000000, // 3 Mo
    sources: ['local', 'url'],
    clientAllowedFormats: ['png', 'jpeg'],
    resourceType: 'image',
    buttonCaption: 'Télécharger image'
  }, 
  (error, result) => {
       $('#cloudinaryPreloader').hide();
    if (!error && result && result.event === "success") {
      console.log('Upload réussi : ', result.info.secure_url);
      document.getElementById("srcLogo2").value = result.info.secure_url;
      document.getElementById("logoImg2").src = result.info.secure_url;
      document.getElementById("NewPhotos2").style.display = 'block';
    }
  });
}



 function sort_brands(el){
        $('#sort_brands').submit();
    }



document.getElementById('click_to_convert').addEventListener('click', function(e) {
    e.preventDefault();

    if (!('webkitSpeechRecognition' in window)) {
        alert("Votre navigateur ne supporte pas la reconnaissance vocale");
        return;
    }

    const recognition = new webkitSpeechRecognition();
    recognition.interimResults = true;

    // 🔥 Choix de la langue selon le menu déroulant
    const selectedLang = document.getElementById('language_select').value;
    recognition.lang = selectedLang;

    recognition.addEventListener('result', (e) => {
        const transcript = Array.from(e.results)
            .map(result => result[0])
            .map(result => result.transcript)
            .join('');

        document.getElementById('convert_text').value = transcript;
    });

    recognition.start();
});


function cloudDinaryCreationLogomarque() {
      $('#cloudinaryPreloader').show();
  cloudinary.openUploadWidget({
    cloudName: 'b-ja',
    uploadPreset: 'aladdineshop',
    folder: 'ml_default',
    theme: 'minimal',
    maxFileSize: 3000000, // 3 Mo
    sources: ['local', 'url'],
    clientAllowedFormats: ['png', 'jpeg'],
    resourceType: 'image',
    buttonCaption: 'Télécharger image'
  }, 
  (error, result) => {
      $('#cloudinaryPreloader').hide();
    if (!error && result && result.event === "success") {
      console.log('Upload réussi : ', result.info.secure_url);
      document.getElementById("srcLogo").value = result.info.secure_url;
      document.getElementById("logoImg").src = result.info.secure_url;
      document.getElementById("NewPhotos").style.display = 'block';
    }
  });
}

var tablePieces = $('#tab').DataTable({
    responsive: true, // Rend le tableau adaptatif sur mobile
    language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
    },
    
});


