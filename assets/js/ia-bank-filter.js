function refreshBankSelect() {
    var bankSelect = $('.bank-select');
    if (!bankSelect.length) {
        return;
    }
    var selectedOption = $('.client-select option:selected');
    var idAgence = selectedOption.data('agence') || '';
    var currentBankId = bankSelect.val() || '';

    $.get("components/com_bank/controleurs/router.php?task=getBanksByAgence&id_agence=" + idAgence + "&id_bank_actuel=" + currentBankId, function (optionsHtml) {
        if (bankSelect.data('select2')) {
            bankSelect.select2('destroy');
        }
        bankSelect.html(optionsHtml);
        if (currentBankId && bankSelect.find('option[value="' + currentBankId + '"]').length) {
            bankSelect.val(currentBankId);
        }
        bankSelect.select2();
        $(document).trigger('bankSelectRefreshed');
    });
}

// Comptes personnels (Hamid/Zakaria, marqués data-perso="1" par le serveur - voir
// com_bank/controleurs/bank/controleur.php:getBanksByAgence) : jamais de TVA sur un devis/une
// facture réglé(e) sur un compte perso, donc "Proforma" + Taux TVA à 0 sont forcés automatiquement
// dès que ce compte est choisi (et reverrouillés à chaque nouvelle sélection tant qu'il reste
// choisi), plutôt que de compter sur l'utilisateur pour y penser à chaque fois.
function appliquerReglePersoBanque() {
    var $bankSelect = $('.bank-select');
    if (!$bankSelect.length) {
        return;
    }
    var estPerso = $bankSelect.find('option:selected').data('perso') == 1;
    // Jamais .prop('disabled', true) sur la case "Proforma" : un checkbox désactivé n'est PAS
    // envoyé du tout à la soumission du formulaire (contrairement à un input readonly), donc le
    // serveur enregistrerait proforma=0 malgré la case cochée à l'écran. On verrouille donc via un
    // handler de clic (empêche juste le décochage) plutôt que l'attribut disabled.
    var $proforma = $('input[name="proforma"]');
    var $tva = $('input[name="tva"]');

    $proforma.off('click.reglePerso');
    if (estPerso) {
        $proforma.prop('checked', true).attr('title', 'Compte personnel : facture automatiquement en proforma, non modifiable');
        $proforma.on('click.reglePerso', function (e) {
            e.preventDefault();
            // afficherMessageStyle() est définie par page (com_devis/com_facture form.php) - ce
            // fichier JS étant chargé globalement (includes/tpl/bottom.php), on protège l'appel.
            if (typeof afficherMessageStyle === 'function') {
                afficherMessageStyle('Veuillez changer le compte affecté : il ne doit pas être un compte personnel.', 'error');
            }
        });
        if ($tva.length) {
            $tva.val(0).prop('readonly', true).attr('title', 'Compte personnel : jamais de TVA');
        }
    } else {
        $proforma.removeAttr('title');
        if ($tva.length) {
            $tva.prop('readonly', false).removeAttr('title');
        }
    }
}

// Sens inverse de appliquerReglePersoBanque() : cocher "Proforma" à la main (avant même d'avoir
// choisi une banque) ne doit proposer que les comptes perso dans le select, puisque c'est la seule
// catégorie de compte éligible au proforma. Filtrage purement local (jamais d'appel réseau, jamais
// de refreshBankSelect() ici) : appelée aussi bien après un refreshBankSelect() classique qu'au
// chargement initial, un appel réseau depuis cette fonction rebouclerait sur bankSelectRefreshed
// qui la rappelle elle-même - boucle infinie si "Proforma" n'est pas coché à ce moment-là. Ne fait
// rien si la case n'est pas cochée ; restaurer la liste complète est géré par le handler de clic
// sur la case elle-même, plus bas.
function filtrerBanquesSurProforma() {
    var $bankSelect = $('.bank-select');
    var $proforma = $('input[name="proforma"]');
    if (!$bankSelect.length || !$proforma.length || !$proforma.is(':checked')) {
        return;
    }

    var $persoOptions = $bankSelect.find('option[data-perso="1"]');
    if (!$persoOptions.length) {
        return; // aucun compte perso disponible pour cette agence : rien à filtrer
    }

    var currentBankId = $bankSelect.val() || '';
    var currentEstPerso = $bankSelect.find('option:selected').data('perso') == 1;

    // Options reconstruites à partir de zéro (plutôt que détacher/ré-attacher les <option>
    // existantes) : un <option> détaché qui garde selected=true côté DOM ferait sinon basculer la
    // sélection dessus dès qu'il est ré-inséré, même si l'utilisateur n'a rien choisi.
    var optionsHtml = '<option value="" selected disabled>Sélectionner</option>';
    $persoOptions.each(function () {
        optionsHtml += '<option value="' + $(this).val() + '" data-perso="1">' + $(this).text() + '</option>';
    });

    if ($bankSelect.data('select2')) {
        $bankSelect.select2('destroy');
    }
    $bankSelect.html(optionsHtml);
    if (currentEstPerso && currentBankId) {
        $bankSelect.val(currentBankId);
    }
    $bankSelect.select2();
}

$(function () {
    $(document).on('change', '.client-select', function () {
        refreshBankSelect();
    });
    $(document).on('clientSelectRefreshed', function () {
        refreshBankSelect();
    });
    if ($('.client-select').val()) {
        refreshBankSelect();
    }

    $(document).on('change', '.bank-select', function () {
        appliquerReglePersoBanque();
    });
    // Après le rafraîchissement AJAX des options (changement de client ou d'agence) : ne garde que
    // les comptes perso si "Proforma" est déjà coché à ce moment-là (no-op sinon).
    $(document).on('bankSelectRefreshed', function () {
        appliquerReglePersoBanque();
        filtrerBanquesSurProforma();
    });
    $(document).on('change', 'input[name="proforma"]', function () {
        if ($(this).is(':checked')) {
            filtrerBanquesSurProforma();
        } else {
            refreshBankSelect();
        }
    });
    appliquerReglePersoBanque();
    filtrerBanquesSurProforma();
});
