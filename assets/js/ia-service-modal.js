function openIaServiceModal(options) {
    var modal = $(options.modalSelector);
    var order = 'titre=' + encodeURIComponent(options.prefillTitre || '')
        + '&prix=' + encodeURIComponent(options.prefillPrix || '')
        + '&unite=' + encodeURIComponent(options.prefillUnite || '')
        + '&description=' + encodeURIComponent(options.prefillDescription || '');

    $.get("components/com_service/controleurs/router.php?task=addServiceForm&" + order, function (theResponse) {
        modal.find('.modal-body').html(theResponse);
        modal.modal('show');
        modal.find('select').select2();

        $(document).off('iaServiceCreated.iaServiceModal').on('iaServiceCreated.iaServiceModal', function (e, newServiceId) {
            refreshServiceSelects(newServiceId, options.targetRow);
            modal.modal('hide');
            if (typeof options.onCreated === 'function') {
                options.onCreated(newServiceId);
            }
        });
    });
}

// icône "IA" présente sur chaque ligne réelle du tableau de prestations (devis/facture),
// disponible à tout moment (avant ou après validation), pas seulement dans le panneau
// de vérification initial : permet de rediscuter/modifier le service déjà choisi sur cette ligne.
$(document).on('click', '.ask-ai-item-row', function () {
    var row = $(this).closest('tr');
    var existing = row.next('.ask-ai-item-chat-row');
    if (existing.length) {
        existing.toggle();
        return;
    }

    var idService = row.find('.service-select').val();
    if (!idService) {
        alert("Sélectionnez d'abord un service sur cette ligne pour pouvoir en discuter avec l'IA.");
        return;
    }
    var titre = row.find('.service-select option:selected').text();
    var nbCols = row.find('> td').length;

    var chatRow = $(
        '<tr class="ask-ai-item-chat-row"><td colspan="' + nbCols + '">' +
        '<div class="form-group mb-1"><input type="text" class="form-control form-control-sm ask-ai-item-question" placeholder="Demandez par ex. : rédige une description pour ce service, ou : scanne https://exemple.com et liste les pages"></div>' +
        '<button type="button" class="btn btn-sm btn-primary ask-ai-item-send">Envoyer</button>' +
        '<div class="ask-ai-item-result mt-2"></div>' +
        '</td></tr>'
    );
    chatRow.data('id-service', idService);
    chatRow.data('titre', titre);
    row.after(chatRow);
});

$(document).on('click', '.ask-ai-item-send', function () {
    var $btn = $(this);
    var chatRow = $btn.closest('tr');
    var question = chatRow.find('.ask-ai-item-question').val().trim();
    if (!question) {
        return;
    }
    var idService = chatRow.data('id-service');
    var titre = chatRow.data('titre');
    $btn.prop('disabled', true).text('Analyse en cours...');

    $.post("components/com_ia/controleurs/router.php?task=chatServiceAssistant", {
        id_service: idService,
        titre: titre,
        description: '',
        message: question
    }, function (response) {
        $btn.prop('disabled', false).text('Envoyer');
        var result = chatRow.find('.ask-ai-item-result');
        if (!response.success) {
            result.html('<div class="alert alert-danger">' + response.message + '</div>');
            return;
        }
        if (response.intent === 'update_description') {
            result.html(
                '<div class="alert alert-info">Description proposée pour "' + titre + '" :</div>' +
                '<textarea class="form-control ask-ai-item-description" rows="4">' + response.proposed_description + '</textarea>' +
                '<label class="d-block mt-2"><input type="checkbox" class="ask-ai-item-update-original"> Mettre à jour aussi le service enregistré (sinon, ce changement ne vaut que pour ce devis/cette facture)</label>' +
                '<button type="button" class="btn btn-success mt-2 ask-ai-item-apply-description" data-id-service="' + idService + '">Appliquer</button>'
            );
        } else if (response.intent === 'scan_website') {
            var pages = response.pages || [];
            if (!pages.length) {
                result.html('<div class="alert alert-warning">Aucune page détectée sur ' + response.url + '.</div>');
                return;
            }
            // uniquement les titres des pages, en une ligne compacte, pas de liens ; fusionné
            // avec la description déjà présente sur cette ligne, jamais écrasée.
            var itemRow = chatRow.prev('tr');
            var pagesLine = buildPagesLine(pages);
            var currentItemDescription = itemRow.find('input[name="item_devis_service_description[]"]').val()
                || itemRow.find('input[name="item_facture_service_description[]"]').val()
                || '';
            var pagesText = mergePagesLine(currentItemDescription, pagesLine);
            result.html(
                '<div class="alert alert-info">Pages détectées sur ' + response.url + ' — texte proposé pour la description de "' + titre + '" :</div>' +
                '<textarea class="form-control ask-ai-item-description" rows="6">' + pagesText + '</textarea>' +
                '<label class="d-block mt-2"><input type="checkbox" class="ask-ai-item-update-original"> Mettre à jour aussi le service enregistré</label>' +
                '<button type="button" class="btn btn-success mt-2 ask-ai-item-apply-description" data-id-service="' + idService + '">Appliquer</button>'
            );
        } else if (response.intent === 'need_url') {
            result.html('<div class="alert alert-warning">Merci de préciser l\'URL du site à scanner dans votre message.</div>');
        } else {
            result.html('<div class="alert alert-warning">' + (response.message || "Je n'ai pas compris la demande.") + '</div>');
        }
    }).fail(function () {
        $btn.prop('disabled', false).text('Envoyer');
        chatRow.find('.ask-ai-item-result').html('<div class="alert alert-danger">Erreur réseau.</div>');
    });
});

$(document).on('click', '.ask-ai-item-apply-description', function () {
    var $btn = $(this);
    var idService = $btn.data('id-service');
    var resultContainer = $btn.closest('.ask-ai-item-result');
    var newDescription = resultContainer.find('.ask-ai-item-description').val();
    var chatRow = $btn.closest('.ask-ai-item-chat-row');
    var itemRow = chatRow.prev('tr');
    var updateOriginal = resultContainer.find('.ask-ai-item-update-original').is(':checked');

    // par défaut, ce changement ne vaut QUE pour cette ligne de ce devis/cette facture
    // (comme la fonction "Personnaliser" déjà existante) : le service enregistré, partagé
    // avec d'autres devis/factures, n'est PAS modifié sauf si la case est cochée explicitement.
    itemRow.find('input[name="item_devis_service_description[]"]').val(newDescription);
    itemRow.find('input[name="item_facture_service_description[]"]').val(newDescription);

    if (!updateOriginal) {
        $btn.prop('disabled', true).text('Appliqué à cette ligne ✓');
        return;
    }

    $btn.prop('disabled', true).text('Enregistrement...');
    $.post("components/com_service/controleurs/router.php?task=updateServiceDescriptionOnly", {
        id_service: idService,
        description: newDescription
    }, function (response) {
        if (response.success) {
            $btn.text('Appliqué à cette ligne + service enregistré ✓');
        } else {
            $btn.prop('disabled', false).text('Réessayer');
        }
    }, 'json');
});

function refreshServiceSelects(selectedServiceId, targetRow) {
    // toujours repartir de la base de données (pas d'une copie mise en cache côté client),
    // pour que les lignes déjà affichées voient aussi les services créés entre-temps.
    $.get("components/com_service/controleurs/router.php?task=getServiceOptions", function (optionsHtml) {
        $('.service-select').each(function () {
            var select = $(this);
            var isTarget = targetRow && select.closest('tr').is(targetRow);
            var currentVal = select.val();

            // select2 caches its own option list at init time; a raw .html()
            // swap leaves it stale, so the widget must be destroyed/reinitialized
            // for the new (and selected) option to actually render.
            if (select.data('select2')) {
                select.select2('destroy');
            }
            select.html(optionsHtml);
            select.val(isTarget ? selectedServiceId : currentVal);
            select.select2();

            if (isTarget) {
                select.trigger('change');
            }
        });

        // les menus du panneau de vérification IA ont un format légèrement différent
        // (placeholder + option "créer un nouveau service") mais doivent aussi refléter
        // la base de données à jour.
        $('.ia-review-service-select').each(function () {
            var select = $(this);
            var isTarget = targetRow && select.closest('tr').is(targetRow);
            var currentVal = select.val();
            select.html('<option value="">— Sélectionner —</option>' + optionsHtml + '<option value="__CREATE_NEW__">➕ Créer un nouveau service</option>');
            select.val(isTarget ? selectedServiceId : currentVal);
        });
    });
}
