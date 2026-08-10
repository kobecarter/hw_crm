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
    // Après le rafraîchissement AJAX des options (changement de client) et au chargement initial
    // d'une page d'édition où un compte perso était déjà enregistré.
    $(document).on('bankSelectRefreshed', function () {
        appliquerReglePersoBanque();
    });
    appliquerReglePersoBanque();
});
