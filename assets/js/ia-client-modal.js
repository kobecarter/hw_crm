function openIaClientModal(options) {
    var modal = $(options.modalSelector);
    var order = 'prefill=' + encodeURIComponent(JSON.stringify(options.prefillClient || {}));

    $.get("components/com_client/controleurs/router.php?task=addClientForm&" + order, function (theResponse) {
        modal.find('.modal-body').html(theResponse);
        modal.modal('show');
        modal.find('select').select2();
        modal.find('select').trigger('chosen:updated');

        $(document).off('iaClientCreated.iaClientModal').on('iaClientCreated.iaClientModal', function (e, newClientId, newClientAgence) {
            if (typeof options.onCreated === 'function') {
                options.onCreated(newClientId, newClientAgence);
            }
        });
    });
}

// si le client créé appartient à une autre agence que celle de la session en cours,
// les listes de clients (filtrées par agence de session) ne l'afficheraient pas :
// on bascule d'abord la session sur la bonne agence avant de continuer.
function ensureSessionAgence(targetAgenceId, callback) {
    if (!targetAgenceId || parseInt(targetAgenceId) === parseInt(currentSessionAgence)) {
        callback();
        return;
    }
    $.post("components/com_config/controleurs/router.php?task=switchAgence", "agence=" + targetAgenceId, function () {
        currentSessionAgence = parseInt(targetAgenceId);
        callback();
    });
}
