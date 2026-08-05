<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Relances</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="index.php?option=com_rappel">Rappels</a></li>
                        <li class="breadcrumb-item active">Emails Envoyés</li>
                    </ul>
                </div>
            </div>
        </div>

        <?php
        global $db;
        $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . __prefixe_db__ . "relances A inner join " . __prefixe_db__ . "rappel B on B.id = A.id_rappel inner join " . __prefixe_db__ . "client C on B.id_client = C.id inner join " . __prefixe_db__ . "agence D on C.id_agence = D.id where D.id = %s order by A.date_send DESC",
            GetSQLValueString($_SESSION["agence"], "int"),
        );
        $result = $db->queryS($SQLselect);

        $seuilLabels = array(30 => 'J-30', 20 => 'J-20', 5 => 'J-5', 0 => 'Jour J');

        // Regroupement par client : chaque relance appartient à un rappel, lui-même lié à un
        // client - on construit un tableau associatif {id_client => [client, relances[]]} en
        // conservant l'ordre déjà trié par date_send DESC (le plus récent d'abord, y compris
        // pour l'ordre des groupes de clients puisqu'on ne crée un groupe qu'à sa 1re rencontre).
        $groupesParClient = array();
        foreach ($result as $relance) {
            $rappel = rappel::find($relance["id_rappel"], $_SESSION['agence']);
            $client = $rappel->getClient();
            $idClient = $client ? $client->getId() : 0;

            if (!isset($groupesParClient[$idClient])) {
                $groupesParClient[$idClient] = array('client' => $client, 'relances' => array());
            }
            $groupesParClient[$idClient]['relances'][] = array('relance' => $relance, 'rappel' => $rappel);
        }
        ?>

        <?php if (empty($groupesParClient)) : ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table">
                        <div class="card-body">
                            <div class="col-sm-12 mt-3 msgbox"></div>
                            <p class="text-muted mb-0">Aucune relance envoyée pour le moment.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="row">
                <div class="col-sm-12 msgbox"></div>
            </div>
            <?php foreach ($groupesParClient as $groupe) :
                $client = $groupe['client'];
                $nomClient = $client && $client->getId() ? (trim((string) $client->getRaisonSocial()) !== '' ? $client->getRaisonSocial() : trim($client->getPrenom() . ' ' . $client->getNom())) : 'Client inconnu';
            ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card card-table">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-user-circle mr-2 text-primary"></i>
                                    <?php if ($client && $client->getId()) : ?>
                                        <a href="index.php?option=com_client&task=detail&id=<?= $client->getId(); ?>"><?php echo htmlspecialchars($nomClient); ?></a>
                                    <?php else : ?>
                                        <?php echo htmlspecialchars($nomClient); ?>
                                    <?php endif; ?>
                                </h4>
                                <span class="badge badge-light"><?= count($groupe['relances']); ?> relance<?= count($groupe['relances']) > 1 ? 's' : ''; ?></span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-stripped table-center table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Type</th>
                                                <th>Domaine</th>
                                                <th>Date expiration</th>
                                                <th>Palier</th>
                                                <th>Envoyé le</th>
                                                <th class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($groupe['relances'] as $ligne) :
                                                $relance = $ligne['relance'];
                                                $rappel = $ligne['rappel'];
                                                $seuil = isset($relance['seuil_jours']) && $relance['seuil_jours'] !== null && $relance['seuil_jours'] !== '' ? intval($relance['seuil_jours']) : null;
                                            ?>
                                                <tr>
                                                    <td><?php echo $relance["id"]; ?></td>
                                                    <td><?php echo $rappel->getType(); ?></td>
                                                    <td><?php echo $rappel->getDomaine(); ?></td>
                                                    <td data-sort="<?= strtotime($rappel->getDateExpir())?>"><?php echo date("d/m/Y", strtotime($rappel->getDateExpir())); ?></td>
                                                    <td>
                                                        <?php if ($seuil !== null) : ?>
                                                            <span class="badge badge-info"><?= isset($seuilLabels[$seuil]) ? $seuilLabels[$seuil] : ($seuil . 'j'); ?></span>
                                                        <?php else : ?>
                                                            <span class="badge badge-light">Manuel</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td data-sort="<?= strtotime($relance["date_send"])?>"><?php echo date("d/m/Y H:i:s", strtotime($relance["date_send"])); ?></td>
                                                    <td class="text-right">
                                                        <?php if ($_SESSION['user']->hasDroit('delete', 'com_rappel')) :?>
                                                            <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $relance['id']; ?>"><i class="far fa-trash-alt"></i></a>
                                                        <?php endif;?>
                                                        <?php if ($_SESSION['user']->hasDroit('view', 'com_rappel')) :?>
                                                            <a href="index.php?option=com_rappel&task=relances&id=<?php echo $relance['id']; ?>" class="btn btn-sm btn-white text-info mr-2 view" data-toggle="tooltip" data-placement="top" data-original-title="Afficher" data-id="<?= $relance['id']; ?>"><i class="far fa-eye"></i></a>
                                                        <?php endif;?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<!-- /Page Wrapper -->
<script type="text/javascript">
    $(function() {

        var msgsucces = "Relance supprimé avec succès";
        $(document).on("click", ".delete", function() {
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_rappel/controleurs/router.php?task=deleteRelance", order, function(theResponse) {
                    if (parseInt(theResponse) == 1) {

                        $btn.parent().parent().addClass("table-danger");
                        setTimeout(function() {
                            $btn.parent().parent().remove()
                        }, 1000);

                        $('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    } else {
                        $('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    }
                });
            }
        })


    });
</script>