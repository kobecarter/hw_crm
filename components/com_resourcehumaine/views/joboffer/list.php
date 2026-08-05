<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Offre d'emploi</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="index.php?option=com_resourcehumaine">Resources humaines</a></li>
                        <li class="breadcrumb-item active">Offre d'emploi : <?php echo $resourcehumaine->getFirstName() . " " . $resourcehumaine->getLastName() ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <?php include("components/com_resourcehumaine/views/resourcehumaine/_profile_header.php"); ?>

        <?php
        // Une offre déjà en attente/validée/envoyée en signature reste affichée en lecture seule
        // (statut + contenu) - le formulaire de création n'a de sens que s'il n'y a encore aucune
        // offre, ou si la dernière a été... on ne gère pas de "annulation", donc on affiche
        // toujours le formulaire pour permettre une nouvelle offre (ex: renégociation), mais la
        // dernière est toujours visible en tête de la liste ci-dessous pour éviter les doublons
        // silencieux.
        $statutLabels = array(
            joboffer::STATUT_BROUILLON => array('label' => 'Brouillon', 'class' => 'bg-secondary-light'),
            joboffer::STATUT_EN_ATTENTE_SLACK => array('label' => 'En attente de validation (Slack)', 'class' => 'bg-warning-light'),
            joboffer::STATUT_VALIDEE => array('label' => 'Validée — en attente du candidat', 'class' => 'bg-info-light'),
            joboffer::STATUT_REFUSEE => array('label' => 'Refusée', 'class' => 'bg-danger-light'),
            joboffer::STATUT_ENVOYEE_SIGNATURE => array('label' => 'Envoyée pour signature', 'class' => 'bg-info-light'),
            joboffer::STATUT_SIGNEE => array('label' => 'Signée', 'class' => 'bg-success-light'),
        );
        ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Générer une offre d'emploi</h4>
                    </div>
                    <div class="card-body">
                        <form id="jobOfferForm">
                            <div class="col-sm-12 msgbox"></div>
                            <input type="hidden" name="id_resourcehumaine" value="<?= $resourcehumaine->getId() ?>">
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label>Période d'essai<span class="text-danger"> * </span></label>
                                    <input type="text" class="form-control" name="periode_essai" placeholder="ex: 3 mois" required>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Salaire (DH)<span class="text-danger"> * </span></label>
                                    <input type="number" step="0.01" class="form-control" name="salaire" value="<?= $resourcehumaine->getSalaireActuel() ? $resourcehumaine->getSalaireActuel() : '' ?>" required>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Conditions de paiement<span class="text-danger"> * </span></label>
                                    <input type="text" class="form-control" name="conditions_paiement" placeholder="ex: viré le 28 de chaque mois" required>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Commissions</label>
                                    <input type="text" class="form-control" name="commissions" placeholder="ex: 5% sur objectif dépassé">
                                </div>
                            </div>

                            <div class="text-right mb-3">
                                <button type="button" id="btnGenererOffreIA" class="btn btn-info">
                                    <span class="spinner-border spinner-border-sm mr-2 loading-ia" style="display:none;"></span>
                                    <i class="fa fa-magic mr-1"></i> Générer l'offre d'emploi
                                </button>
                            </div>

                            <div class="form-group">
                                <label>Contenu de l'offre <small class="text-muted">(modifiable avant envoi)</small></label>
                                <textarea name="contenu" id="jobOfferContenu"></textarea>
                            </div>

                            <div class="text-right mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <span class="spinner-border spinner-border-sm mr-2 loading-submit" style="display:none;"></span>
                                    <i class="fab fa-slack mr-1"></i> Envoyer sur Slack pour validation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Historique des offres</h4>
                    </div>
                    <div class="card-body">
                        <?php if (empty($jobOffers)) : ?>
                            <p class="text-center">Aucune offre d'emploi pour le moment.</p>
                        <?php else : ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Statut</th>
                                            <th>Salaire</th>
                                            <th>Validée/refusée par</th>
                                            <th>Signature</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($jobOffers as $offer) :
                                            $st = isset($statutLabels[$offer->getStatut()]) ? $statutLabels[$offer->getStatut()] : array('label' => $offer->getStatut(), 'class' => 'bg-secondary-light');
                                        ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i', strtotime($offer->getDateAdd())) ?></td>
                                            <td><span class="badge <?= $st['class'] ?>"><?= $st['label'] ?></span></td>
                                            <td><?= number_format((float) $offer->getSalaire(), 2, ',', ' ') ?> DH</td>
                                            <td><?= $offer->getSlackValidatedBy() ? htmlspecialchars($offer->getSlackValidatedBy()) : '—' ?></td>
                                            <td>
                                                <?php if ($offer->getSignedFile()) : ?>
                                                    <a href="./images/resourceshumaines/offres/<?= $offer->getSignedFile() ?>" target="_blank"><i class="fa fa-file-pdf mr-1"></i> Voir le document signé</a>
                                                <?php elseif ($offer->getStatut() === joboffer::STATUT_VALIDEE) : ?>
                                                    <div class="mb-1">
                                                        <small class="text-success d-block"><i class="fa fa-paper-plane mr-1"></i> Lien d'acceptation envoyé par email au candidat</small>
                                                        <small class="text-muted d-block">Ou marquer signée manuellement (SignWell pas encore configuré) :</small>
                                                    </div>
                                                    <form class="form-marquer-signee d-inline-flex align-items-center" data-id="<?= $offer->getId() ?>" enctype="multipart/form-data">
                                                        <input type="file" name="signed_file" class="mr-1" style="max-width:160px;" required>
                                                        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check mr-1"></i> Marquer signée</button>
                                                    </form>
                                                <?php else : ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-nowrap">
                                                <a href="javascript:void(0)" class="btn btn-warning btn-sm mb-1 btn-modifier-renvoyer"
                                                    data-periode-essai="<?= htmlspecialchars($offer->getPeriodeEssai()) ?>"
                                                    data-salaire="<?= htmlspecialchars($offer->getSalaire()) ?>"
                                                    data-conditions-paiement="<?= htmlspecialchars($offer->getConditionsPaiement()) ?>"
                                                    data-commissions="<?= htmlspecialchars($offer->getCommissions()) ?>"
                                                    data-contenu="<?= htmlspecialchars($offer->getContenu()) ?>"
                                                    data-toggle="tooltip" title="Charger cette offre dans le formulaire pour la modifier et la renvoyer">
                                                    <i class="fa fa-redo mr-1"></i> Modifier et renvoyer
                                                </a><br>
                                                <a href="components/com_resourcehumaine/controleurs/router.php?task=telechargerOffrePDF&id=<?= $offer->getId() ?>" target="_blank" class="btn btn-white btn-sm mb-1" data-toggle="tooltip" title="Aperçu / téléchargement PDF (mise en page identique au modèle final)">
                                                    <i class="fa fa-file-pdf text-danger mr-1"></i> PDF
                                                </a>
                                                <a href="components/com_resourcehumaine/controleurs/router.php?task=telechargerOffreWord&id=<?= $offer->getId() ?>" class="btn btn-white btn-sm mb-1" data-toggle="tooltip" title="Télécharger en Word">
                                                    <i class="fa fa-file-word text-primary mr-1"></i> Word
                                                </a>
                                                <a href="javascript:void(0)" class="btn btn-white btn-sm mb-1 btn-supprimer-offre" data-id="<?= $offer->getId() ?>" data-toggle="tooltip" title="Supprimer">
                                                    <i class="fa fa-trash-alt text-danger"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    CKEDITOR.replace('jobOfferContenu', {
        allowedContent: true,
        filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
    });

    $(function () {
        $('#btnGenererOffreIA').on('click', function () {
            var $btn = $(this);
            var periode = $('input[name=periode_essai]').val();
            var salaire = $('input[name=salaire]').val();
            var conditions = $('input[name=conditions_paiement]').val();
            var commissions = $('input[name=commissions]').val();

            if (!periode || !salaire || !conditions) {
                $('#jobOfferForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Merci de remplir période d\'essai, salaire et conditions de paiement avant de générer.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                return;
            }

            $btn.prop('disabled', true);
            $('.loading-ia').show();

            $.post('components/com_resourcehumaine/controleurs/router.php?task=generateJobOfferAI', {
                id_resourcehumaine: <?= $resourcehumaine->getId() ?>,
                periode_essai: periode,
                salaire: salaire,
                conditions_paiement: conditions,
                commissions: commissions
            }, function (response) {
                $btn.prop('disabled', false);
                $('.loading-ia').hide();
                if (response.success) {
                    CKEDITOR.instances.jobOfferContenu.setData(response.contenu);
                } else {
                    $('#jobOfferForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur!</strong> ' + response.message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }, 'json').fail(function () {
                $btn.prop('disabled', false);
                $('.loading-ia').hide();
                $('#jobOfferForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur!</strong> Erreur de connexion.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
            });
        });

        $('#jobOfferForm').on('submit', function (e) {
            e.preventDefault();
            CKEDITOR.instances.jobOfferContenu.updateElement();
            var contenu = $('#jobOfferContenu').val();
            if (!contenu || contenu.trim() === '') {
                $('#jobOfferForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Le contenu de l\'offre est vide — générez-le avec l\'IA ou rédigez-le manuellement.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                return;
            }

            var $btn = $(this).find('button[type=submit]');
            $btn.prop('disabled', true);
            $('.loading-submit').show();

            $.post('components/com_resourcehumaine/controleurs/router.php?task=submitJobOffer', $(this).serialize(), function (response) {
                $btn.prop('disabled', false);
                $('.loading-submit').hide();
                if (response.success) {
                    var msg = response.slack_envoye
                        ? 'Offre enregistrée et envoyée sur Slack #administration pour validation.'
                        : 'Offre enregistrée, mais l\'envoi Slack a échoué (vérifier la configuration du bot/canal). Vous pouvez réessayer depuis l\'historique.';
                    $('#jobOfferForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Succès!</strong> ' + msg + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    setTimeout(function () { location.reload(); }, 1800);
                } else {
                    $('#jobOfferForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur!</strong> ' + response.message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }, 'json').fail(function () {
                $btn.prop('disabled', false);
                $('.loading-submit').hide();
                $('#jobOfferForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur!</strong> Erreur de connexion.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
            });
        });

        // Offre refusée : reprend ses valeurs dans le formulaire du haut pour l'ajuster avant de
        // la renvoyer (crée une nouvelle offre à l'enregistrement, l'ancienne refusée reste dans
        // l'historique tel quel - pas de mutation d'une offre déjà tranchée).
        $(document).on('click', '.btn-modifier-renvoyer', function () {
            var $btn = $(this);
            $('input[name=periode_essai]').val($btn.data('periode-essai'));
            $('input[name=salaire]').val($btn.data('salaire'));
            $('input[name=conditions_paiement]').val($btn.data('conditions-paiement'));
            $('input[name=commissions]').val($btn.data('commissions'));
            CKEDITOR.instances.jobOfferContenu.setData(String($btn.data('contenu')));
            $('html, body').animate({ scrollTop: $('#jobOfferForm').offset().top - 100 }, 'slow');
        });

        $(document).on('click', '.btn-supprimer-offre', function () {
            var $btn = $(this);
            if (!confirm('Supprimer définitivement cette offre d\'emploi ? Cette action est irréversible.')) {
                return;
            }
            $btn.prop('disabled', true);
            $.post('components/com_resourcehumaine/controleurs/router.php?task=deleteJobOffer', { id: $btn.data('id') }, function (response) {
                if (response.success) {
                    $btn.closest('tr').fadeOut(300, function () { $(this).remove(); });
                } else {
                    $btn.prop('disabled', false);
                    alert(response.message || 'Erreur lors de la suppression.');
                }
            }, 'json').fail(function () {
                $btn.prop('disabled', false);
                alert('Erreur de connexion.');
            });
        });

        // Marquer une offre validée comme signée manuellement (document scanné joint).
        $(document).on('submit', '.form-marquer-signee', function (e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData(this);
            formData.append('id', $form.data('id'));
            $form.find('button').prop('disabled', true);

            $.ajax({
                url: 'components/com_resourcehumaine/controleurs/router.php?task=marquerOffreSignee',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        $form.find('button').prop('disabled', false);
                        alert(response.message || 'Erreur lors de l\'enregistrement.');
                    }
                },
                error: function () {
                    $form.find('button').prop('disabled', false);
                    alert('Erreur de connexion.');
                }
            });
        });
    });
</script>
