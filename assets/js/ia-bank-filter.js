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
    });
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
});
