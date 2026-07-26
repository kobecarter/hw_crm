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

	$(".chosen-select").select2();


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
	

})(jQuery);