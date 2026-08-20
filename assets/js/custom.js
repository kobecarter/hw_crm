/*
Author       : Dreamguys
Template Name: Kanakku - Bootstrap Admin Template
Version      : 1.0
*/

function switchUnitiesByLangauge(lang){
    if(lang){
        // Post data
        let order="langue="+lang
        // Correct with api and send post data
        $.post("components/com_devis/controleurs/router.php?task=switchUnitiesByLanguage", order, function (theResponse) {
            // received object data
            let data = JSON.parse(theResponse)
            // get keys from object data
            let keys = Object.keys(data)
            // Make for loop to keys
            keys.forEach((element, index) => {
                // Make for loop to select tags
                $("select[name='unite[]']").each(function(k){
                    let self = $("select[name='unite[]']:eq("+k+")")
                    let options = self.find('option')
                    options.each(function(i){
                        if(self.find('option:eq('+i+')').val() == element) {
                            self.find('option:eq('+i+')').text(data[element])
                        }
                    })
                    self.select2({
            			minimumResultsForSearch: -1,
            			width: '100%'
            		});
                })
            });
	    });
    }
}

(function ($) {
	"use strict";

	// Protégé (jamais appelé sur la page de login, qui n'a aucun .chosen-select) : select2 est
	// chargé depuis un CDN externe (includes/tpl/bottom-login.php) - une coupure réseau/un
	// bloqueur qui empêche ce script de charger (bien plus probable sur mobile que sur un poste de
	// dev stable) laissait $.fn.select2 indéfini, et l'appeler faisait planter tout le reste de
	// cette IIFE avant même d'atteindre le ajaxForm() du formulaire de connexion juste en dessous -
	// "Login" semblait alors ne plus rien faire au clic (le POST natif du <form> partait bien,
	// mais vers une page nue affichant juste "0"/"1"/"2", jamais interceptée en AJAX).
	if (typeof $.fn.select2 === 'function' && $(".chosen-select").length) {
		$(".chosen-select").select2();
	}


	$('form#loginForm').ajaxForm({

		beforeSubmit: function () {
			//chargement
			$("#loginForm .loading").show();
		},
		success: function (theResponse) {

			$("#loginForm .loading").hide();
			if (parseInt(theResponse) === 1) {
				$('#loginForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> Vous êtes connecté.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				setTimeout(function () {
					document.location = "index.php?option=com_dashboard";
				}, 1500)
			}
			else if (parseInt(theResponse) === 2) {
				$('#loginForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Login ou mot de passe incorrecte <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			}
			else if (parseInt(theResponse) === 0) {
				$('#loginForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez entrer votre login et mot de passe<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			}
			else {
				$('#loginForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			}
		},
		// Sans ce callback, une requête qui échoue (mauvaise origine/hôte, coupure réseau...)
		// échouait en silence : le bouton "Login" restait bloqué sur son état de chargement, sans
		// aucun message - impossible à distinguer d'un clic qui "ne fait rien".
		error: function () {
			$("#loginForm .loading").hide();
			$('#loginForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Impossible de contacter le serveur. Vérifiez votre connexion et réessayez.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
		}
	});


	$('form#eloginForm').ajaxForm({

		beforeSubmit: function () {
			//chargement
			$("#eloginForm .loading").show();
		},
		success: function (theResponse) {
			console.log(theResponse)
			$("#eloginForm .loading").hide();
			if (parseInt(theResponse) === 1) {
				$('#eloginForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> Vous êtes connecté.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				setTimeout(function () {
					document.location = "index.php?option=com_dashboard";
				}, 1500)
			}
			else if (parseInt(theResponse) === 2) {
				$('#eloginForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> E-mail ou mot de passe incorrecte <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			}
			else if (parseInt(theResponse) === 3) {
				$('#eloginForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Le compte a été désactivé à cause de quelque chose <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			}
			else if (parseInt(theResponse) === 0) {
				$('#eloginForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez entrer votre e-mail et mot de passe<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			}
			else {
				$('#eloginForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			}
		},
		error: function () {
			$("#eloginForm .loading").hide();
			$('#eloginForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Impossible de contacter le serveur. Vérifiez votre connexion et réessayez.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
		}
	});

	// Déconnexion
	$('.logout').click(function (event) {
		event.preventDefault();
		var order = '';
		$.post("components/com_login/controleurs/router.php?task=logout", order, function (theResponse) {
			if (parseInt(theResponse) === 1) {
				document.location.reload();
			}
		});
	});

	// Déconnexion
	$('.elogout').click(function (event) {
		event.preventDefault();
		var order = '';
		$.post("components/com_elogin/controleurs/router.php?task=logout", order, function (theResponse) {
			if (parseInt(theResponse) === 1) {
				location.href = "index.php?option=com_elogin";
			}
		});
	});

	// language swicher
	$(".switch-lang").click(function () {
		var order = 'lang=' + $(this).attr('data-lang');
		$.post("components/com_config/controleurs/router.php?task=switchLang", order, function (theResponse) {
			console.log(theResponse)
			if (parseInt(theResponse) === 1) {
				document.location.reload();
			}
		});
	})

	// company swicher
	$(".switch-agence").click(function () {
		var order = 'agence=' + $(this).attr('data-agence');
		$.post("components/com_config/controleurs/router.php?task=switchAgence", order, function (theResponse) {
			if (parseInt(theResponse) === 1) {
				document.location.reload();
			}
		});
	})
	$(document).on('change','select[name="langue"]',function(){
	    let self= $(this)
	    let value = self.val();
	    switchUnitiesByLangauge(value)
	})

	$(document).on('click','.mark-as-read',function(){
		let self = $(this)
		let url = self.attr('data-url')
		let id = self.attr('data-id')
		let order = 'id=' + id;
		$.post("components/com_resourcehumaine/controleurs/router.php?task=markAsRead", order, function (theResponse) {
			if (parseInt(theResponse) === 1) {
				location.href = url
			}
		});
	})

	// Sous 992px, ".dropdown-menu.show" passe en position:fixed (cf. includes/tpl/top.php) pour se
	// positionner par rapport au vrai viewport plutôt qu'au déclencheur - mais .header a
	// backdrop-filter (glassmorphism), qui crée un nouveau "containing block" pour tout descendant
	// en position:fixed (piège CSS déjà rencontré ailleurs dans ce projet pour les modales, cf.
	// modern-theme.js/show.bs.modal, et pour la barre de recherche mobile) : le menu se
	// positionnait alors par rapport à .header et non au viewport, retombant hors écran malgré le
	// CSS "position:fixed;left:12px". Fix : sortir temporairement le menu ouvert vers <body> (hors
	// de tout ancêtre à risque), et le remettre à sa place normale une fois refermé pour que
	// Bootstrap le retrouve au prochain clic (son code interne le cherche comme enfant direct du
	// <li> qui porte data-toggle="dropdown"). "data-display=static" (ajouté sur ces mêmes
	// déclencheurs) est le complément indispensable : sans lui, Popper.js réinjecte son propre
	// positionnement en style inline et entre en conflit avec ce déplacement.
	$(document).on('show.bs.dropdown', '.user-menu > li.dropdown', function () {
		if (window.innerWidth > 767.98) return;
		var $li = $(this);
		var $menu = $li.children('.dropdown-menu');
		if (!$menu.length) return;
		$li.data('dropdownMenuMovedToBody', $menu);
		$(document.body).append($menu);
	});
	$(document).on('hidden.bs.dropdown', '.user-menu > li.dropdown', function () {
		var $li = $(this);
		var $menu = $li.data('dropdownMenuMovedToBody');
		if ($menu && $menu.parent().is('body')) {
			$li.append($menu);
			$li.removeData('dropdownMenuMovedToBody');
		}
	});


})(jQuery);