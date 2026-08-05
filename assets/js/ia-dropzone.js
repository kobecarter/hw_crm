// Bouton "Parcourir mes fichiers" visible dans chaque zone .ia-dropzone (com_charge, com_devis,
// com_facture, com_client, com_resourcehumaine, com_accounting/tva, com_rapprochement) :
// jusqu'ici, cliquer n'importe où dans la zone ouvrait déjà le sélecteur de fichiers (géré par
// chaque init*Dropzone() plus bas / par la page com_rapprochement elle-même), mais rien ne le
// signalait visuellement - juste un texte "...ou cliquez pour la sélectionner" au milieu d'un
// paragraphe. Enrichissement pur JS (aucune vue PHP à toucher, les 8 zones partagent déjà la même
// classe) : ajoute un vrai bouton après coup, tant qu'il n'existe pas déjà. stopPropagation()
// pour ne pas déclencher EN PLUS le clic sur la zone elle-même (ouvrirait le sélecteur deux fois).
(function ($) {
	if (!$) {
		return;
	}
	$(function () {
		$('.ia-dropzone').each(function () {
			var $zone = $(this);
			if ($zone.find('.ia-dropzone-browse-btn').length) {
				return;
			}
			var $input = $zone.find('input[type=file]').first();
			if (!$input.length) {
				return;
			}
			var $btn = $('<button type="button" class="ia-dropzone-browse-btn"><i class="fa fa-folder-open mr-1"></i>Parcourir mes fichiers</button>');
			$btn.on('click', function (e) {
				e.preventDefault();
				e.stopPropagation();
				$input.trigger('click');
			});
			$zone.find('.ia-dropzone-text').append($btn);
		});
	});
})(window.jQuery);

function suggestLangueFromPays(pays) {
    if (!pays) {
        return null;
    }
    var p = pays.toLowerCase();
    if (p.indexOf('maroc') !== -1 || p.indexOf('morocco') !== -1) {
        return 'fr';
    }
    if (p.indexOf('emira') !== -1 || p.indexOf('uae') !== -1 || p.indexOf('dubai') !== -1 || p.indexOf('abu dhabi') !== -1) {
        return 'en';
    }
    return null;
}

function applyLangueSuggestion(pays) {
    var langue = suggestLangueFromPays(pays);
    if (langue && $("select[name='langue']").length) {
        $("select[name='langue']").val(langue).trigger('change');
    }
}

// une seule ligne compacte avec juste les titres (pas de liens) : "Gestion des pages : (a, b, c)"
function buildPagesLine(pages) {
    var titres = (pages || [])
        .map(function (p) { return ($('<div>').text(p.titre || '').html() || '').trim(); })
        .filter(function (t) { return t !== ''; });
    if (!titres.length) {
        return '';
    }
    return 'Gestion des pages : (' + titres.join(', ') + ')';
}

// remplace la ligne "Gestion des pages : (...)" si elle existe déjà (évite qu'elle se duplique
// à chaque nouveau scan), sinon l'ajoute à la suite ; ne touche jamais au reste du contenu.
function mergePagesLine(currentDescription, pagesLine) {
    currentDescription = currentDescription || '';
    if (!pagesLine) {
        return currentDescription;
    }
    var regex = /Gestion des pages\s*:\s*\([^)]*\)/i;
    if (regex.test(currentDescription)) {
        return currentDescription.replace(regex, pagesLine);
    }
    return currentDescription ? (currentDescription + '<br><br>' + pagesLine) : pagesLine;
}

function initIaDropzone(options) {
    var zone = $(options.zoneSelector);
    var fileInput = zone.find('input[type=file]');

    zone.on('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        zone.addClass('ia-dropzone-active');
    });
    zone.on('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();
        zone.removeClass('ia-dropzone-active');
    });
    zone.on('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        zone.removeClass('ia-dropzone-active');
        var files = e.originalEvent.dataTransfer.files;
        if (files.length) {
            uploadIaFile(files[0]);
        }
    });
    zone.on('click', function (e) {
        // Garde essentielle : fileInput.trigger('click') appelle en interne le .click() natif de
        // l'input (jQuery), qui émet un vrai évènement DOM bubbling - lequel remonte jusqu'à cette
        // même zone et redéclenche ce handler, qui re-trigger l'input, etc. Boucle infinie
        // (RangeError: Maximum call stack size exceeded) dès que le clic initial vient de l'input
        // lui-même plutôt que d'ailleurs dans la zone (typiquement via .ia-dropzone-browse-btn,
        // dont le bouton appelle aussi fileInput.trigger('click')).
        if (e.target === fileInput[0]) {
            return;
        }
        fileInput.trigger('click');
    });
    fileInput.on('change', function () {
        if (this.files.length) {
            uploadIaFile(this.files[0]);
        }
    });

    function uploadIaFile(file) {
        if (!/\.pdf$/i.test(file.name)) {
            alert("Seuls les fichiers PDF sont acceptés.");
            return;
        }
        var formData = new FormData();
        formData.append('presentation[]', file);
        zone.addClass('ia-dropzone-loading');
        zone.find('.ia-dropzone-text').html('<i class="fas fa-spinner fa-spin"></i> Analyse du fichier en cours...');
        $.ajax({
            url: 'components/com_ia/controleurs/router.php?task=extractPresentation&context=' + options.context,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                zone.removeClass('ia-dropzone-loading');
                if (response.success == 1) {
                    zone.find('.ia-dropzone-text').html('<i class="fas fa-check-circle text-success"></i> Fichier analysé : ' + file.name + '<br><small>Vérifiez les champs pré-remplis avant de valider.</small>');
                    options.onExtracted(response);
                } else {
                    zone.find('.ia-dropzone-text').html('<i class="fas fa-file-pdf"></i> Glissez-déposez une présentation (PDF) ici, ou cliquez pour la sélectionner');
                    alert("Erreur lors de l'analyse du fichier : " + response.message);
                }
            },
            error: function () {
                zone.removeClass('ia-dropzone-loading');
                zone.find('.ia-dropzone-text').html('<i class="fas fa-file-pdf"></i> Glissez-déposez une présentation (PDF) ici, ou cliquez pour la sélectionner');
                alert("Erreur réseau lors de l'envoi du fichier.");
            }
        });
    }
}

// Pré-remplissage d'une fiche employé depuis une CIN (photo) ou un CV (PDF) : contrairement à
// initIaDropzone() ci-dessus (présentations commerciales, PDF uniquement), accepte aussi les
// images puisqu'une carte CIN est le plus souvent envoyée sous forme de photo.
function initEmployeeIaDropzone(options) {
    var zone = $(options.zoneSelector);
    var fileInput = zone.find('input[type=file]');
    var texteInitial = zone.find('.ia-dropzone-text').html();

    zone.on('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        zone.addClass('ia-dropzone-active');
    });
    zone.on('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();
        zone.removeClass('ia-dropzone-active');
    });
    zone.on('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        zone.removeClass('ia-dropzone-active');
        var files = e.originalEvent.dataTransfer.files;
        if (files.length) {
            uploadEmployeeDoc(files[0]);
        }
    });
    zone.on('click', function (e) {
        // Garde essentielle : fileInput.trigger('click') appelle en interne le .click() natif de
        // l'input (jQuery), qui émet un vrai évènement DOM bubbling - lequel remonte jusqu'à cette
        // même zone et redéclenche ce handler, qui re-trigger l'input, etc. Boucle infinie
        // (RangeError: Maximum call stack size exceeded) dès que le clic initial vient de l'input
        // lui-même plutôt que d'ailleurs dans la zone (typiquement via .ia-dropzone-browse-btn,
        // dont le bouton appelle aussi fileInput.trigger('click')).
        if (e.target === fileInput[0]) {
            return;
        }
        fileInput.trigger('click');
    });
    fileInput.on('change', function () {
        if (this.files.length) {
            uploadEmployeeDoc(this.files[0]);
        }
    });

    function uploadEmployeeDoc(file) {
        if (!/\.(pdf|jpe?g|png|webp)$/i.test(file.name)) {
            alert("Formats acceptés : PDF, JPG, PNG, WEBP.");
            return;
        }
        var formData = new FormData();
        formData.append('document', file);
        zone.addClass('ia-dropzone-loading');
        zone.find('.ia-dropzone-text').html('<i class="fas fa-spinner fa-spin"></i> Analyse du document en cours...');
        $.ajax({
            url: 'components/com_ia/controleurs/router.php?task=extractEmployeeDocument',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                zone.removeClass('ia-dropzone-loading');
                if (response.success == 1) {
                    zone.find('.ia-dropzone-text').html('<i class="fas fa-check-circle text-success"></i> Document analysé : ' + file.name + '<br><small>Vérifiez les champs pré-remplis avant de valider.</small>');
                    options.onExtracted(response.extracted);
                } else {
                    zone.find('.ia-dropzone-text').html(texteInitial);
                    alert("Erreur lors de l'analyse du document : " + response.message);
                }
            },
            error: function () {
                zone.removeClass('ia-dropzone-loading');
                zone.find('.ia-dropzone-text').html(texteInitial);
                alert("Erreur réseau lors de l'envoi du fichier.");
            }
        });
    }
}

// Pré-remplissage d'une déclaration de TVA depuis le document envoyé par le comptable (PDF le
// plus souvent, parfois une photo/scan). Contrairement aux dropzones ci-dessus, transmet aussi
// le fichier brut à onExtracted() : l'appelant s'en sert pour l'attacher automatiquement au
// vrai champ d'upload du formulaire (le document déposé devient la pièce jointe enregistrée),
// évitant à l'utilisateur de le sélectionner une seconde fois.
function initTvaIaDropzone(options) {
    var zone = $(options.zoneSelector);
    var fileInput = zone.find('input[type=file]');
    var texteInitial = zone.find('.ia-dropzone-text').html();

    zone.on('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        zone.addClass('ia-dropzone-active');
    });
    zone.on('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();
        zone.removeClass('ia-dropzone-active');
    });
    zone.on('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        zone.removeClass('ia-dropzone-active');
        var files = e.originalEvent.dataTransfer.files;
        if (files.length) {
            uploadTvaDoc(files[0]);
        }
    });
    zone.on('click', function (e) {
        // Garde essentielle : fileInput.trigger('click') appelle en interne le .click() natif de
        // l'input (jQuery), qui émet un vrai évènement DOM bubbling - lequel remonte jusqu'à cette
        // même zone et redéclenche ce handler, qui re-trigger l'input, etc. Boucle infinie
        // (RangeError: Maximum call stack size exceeded) dès que le clic initial vient de l'input
        // lui-même plutôt que d'ailleurs dans la zone (typiquement via .ia-dropzone-browse-btn,
        // dont le bouton appelle aussi fileInput.trigger('click')).
        if (e.target === fileInput[0]) {
            return;
        }
        fileInput.trigger('click');
    });
    fileInput.on('change', function () {
        if (this.files.length) {
            uploadTvaDoc(this.files[0]);
        }
    });

    function uploadTvaDoc(file) {
        if (!/\.(pdf|jpe?g|png|webp)$/i.test(file.name)) {
            alert("Formats acceptés : PDF, JPG, PNG, WEBP.");
            return;
        }
        var formData = new FormData();
        formData.append('document', file);
        zone.addClass('ia-dropzone-loading');
        zone.find('.ia-dropzone-text').html('<i class="fas fa-spinner fa-spin"></i> Lecture de la déclaration en cours...');
        $.ajax({
            url: 'components/com_ia/controleurs/router.php?task=extractTvaDeclaration',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                zone.removeClass('ia-dropzone-loading');
                if (response.success == 1) {
                    zone.find('.ia-dropzone-text').html('<i class="fas fa-check-circle text-success"></i> Déclaration analysée : ' + file.name + '<br><small>Vérifiez les champs pré-remplis avant de valider.</small>');
                    options.onExtracted(response.extracted, file);
                } else {
                    zone.find('.ia-dropzone-text').html(texteInitial);
                    alert("Erreur lors de l'analyse du document : " + response.message);
                }
            },
            error: function () {
                zone.removeClass('ia-dropzone-loading');
                zone.find('.ia-dropzone-text').html(texteInitial);
                alert("Erreur réseau lors de l'envoi du fichier.");
            }
        });
    }
}

// Dropzone universelle de la page Charges : accepte n'importe quel justificatif (bulletin de
// paie, déclaration CNSS/TVA, bilan, taxe professionnelle, facture...). Même mécanique que
// initTvaIaDropzone() (fichier brut renvoyé à onExtracted() pour devenir directement la pièce
// jointe du formulaire) ; le type de document est détecté côté IA
// (components/com_ia/controleurs/router.php::extractChargeDocument()), qui renvoie aussi le
// rapprochement employé (bulletin de paie) et une éventuelle alerte de doublon (CNSS/TVA/
// bilan/taxe professionnelle) : onExtracted(extracted, file, employeMatch, doublon).
function initChargeDocumentDropzone(options) {
    var zone = $(options.zoneSelector);
    var fileInput = zone.find('input[type=file]');
    var texteInitial = zone.find('.ia-dropzone-text').html();

    zone.on('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        zone.addClass('ia-dropzone-active');
    });
    zone.on('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();
        zone.removeClass('ia-dropzone-active');
    });
    zone.on('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        zone.removeClass('ia-dropzone-active');
        var files = e.originalEvent.dataTransfer.files;
        if (files.length) {
            uploadChargeDoc(files[0]);
        }
    });
    zone.on('click', function (e) {
        // Garde essentielle : fileInput.trigger('click') appelle en interne le .click() natif de
        // l'input (jQuery), qui émet un vrai évènement DOM bubbling - lequel remonte jusqu'à cette
        // même zone et redéclenche ce handler, qui re-trigger l'input, etc. Boucle infinie
        // (RangeError: Maximum call stack size exceeded) dès que le clic initial vient de l'input
        // lui-même plutôt que d'ailleurs dans la zone (typiquement via .ia-dropzone-browse-btn,
        // dont le bouton appelle aussi fileInput.trigger('click')).
        if (e.target === fileInput[0]) {
            return;
        }
        fileInput.trigger('click');
    });
    fileInput.on('change', function () {
        if (this.files.length) {
            uploadChargeDoc(this.files[0]);
        }
    });

    zone.data('resetDropzone', function () {
        zone.find('.ia-dropzone-text').html(texteInitial);
        fileInput.val('');
    });

    function uploadChargeDoc(file) {
        if (!/\.(pdf|jpe?g|png|webp)$/i.test(file.name)) {
            alert("Formats acceptés : PDF, JPG, PNG, WEBP.");
            return;
        }
        var formData = new FormData();
        formData.append('document', file);
        zone.addClass('ia-dropzone-loading');
        zone.find('.ia-dropzone-text').html('<i class="fas fa-spinner fa-spin"></i> Lecture du document en cours...');
        $.ajax({
            url: 'components/com_ia/controleurs/router.php?task=extractChargeDocument',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                zone.removeClass('ia-dropzone-loading');
                if (response.success == 1) {
                    zone.find('.ia-dropzone-text').html('<i class="fas fa-check-circle text-success"></i> Document analysé : ' + file.name + '<br><small>Vérifiez les champs pré-remplis avant de valider.</small>');
                    options.onExtracted(response.extracted, file, response.employe_match, response.doublon);
                } else {
                    zone.find('.ia-dropzone-text').html(texteInitial);
                    alert("Erreur lors de l'analyse du document : " + response.message);
                }
            },
            error: function () {
                zone.removeClass('ia-dropzone-loading');
                zone.find('.ia-dropzone-text').html(texteInitial);
                alert("Erreur réseau lors de l'envoi du fichier.");
            }
        });
    }
}
