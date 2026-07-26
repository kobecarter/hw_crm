<?php
include_once "components/com_lang/traduction.php";
?>
<div class="sub-sidebar-wrapper">
    <ul>
        <?php if ($_SESSION['user']->hasDroit('add', 'com_lang')) { ?>
            <li>
                <a href="index.php?option=com_lang&task=add"> <?= $trad_com_lang['AJOUTER_LANGUE'][$_SESSION['user']->getLangue()]; ?></a>
            </li>
        <?php } ?>
        <?php if ($_SESSION['user']->hasDroit('view', 'com_lang')) { ?>
            <li>
                <a href="index.php?option=com_lang"> <?= $trad_com_lang['LISTE_LANGUE'][$_SESSION['user']->getLangue()]; ?></a>
            </li>
        <?php } ?>
    </ul>
</div>
</div>
<div class="main-content">
    <?php
    @$task = $_GET['task'];
    switch ($task) {
        case 'edit' :
            if ($_SESSION['user']->hasDroit('edit', 'com_lang')) {
                edit();
                break;
            }
        case 'add' :
            if ($_SESSION['user']->hasDroit('add', 'com_lang')) {
                add();
                break;
            }
        default :
            if ($_SESSION['user']->hasDroit('view', 'com_lang')) {
                showList();
            } // Charge la liste des articles
    }

    /* ---------------------------- function ---------------------------- */

    /* ---------------------------- showList ---------------------------- */
    function showList()
    {
        global $db, $trad_com_lang;
        ?>
        <script type="text/javascript">
            $(function () {
                var succes = "<?= $trad_com_lang['SUCCES'][$_SESSION['user']->getLangue()];?>";
                var error = "<?= $trad_com_lang['ERREUR'][$_SESSION['user']->getLangue()];?>";
                $(".delete").click(function (event) {
                    event.preventDefault();
                    var succes_msg = "<?= $trad_com_lang['SUCCES_DEL'][$_SESSION['user']->getLangue()];?>";
                    var error_msg = "<?= $trad_com_lang['ERREUR_DEL'][$_SESSION['user']->getLangue()];?>";
                    if (confirm("<?= $trad_com_lang['QST_DEL'][$_SESSION['user']->getLangue()];?>")) {
                        var t = $(this).attr("id").split("_");
                        var id = t[1];
                        var order = 'id=' + id;
                        $.post("components/com_lang/controleur/langue.php?task=deleteLangue", order, function (theResponse) {
                            if (parseInt(theResponse) == 1) {
                                $("#row_" + id).addClass("danger");
                                setTimeout(function () {
                                    $("#row_" + id).remove()
                                }, 300);
                                $('.msgbox').html('<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>' + succes + '</strong>' + succes_msg + '</div>');
                                $('.msgbox').slideDown();
                            }
                            else {
                                $('.msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>' + error + '</strong>' + error_msg + '</div>');
                                $('.msgbox').slideDown();
                            }
                        });
                    }
                });
                $(".enable").click(function (event) {
                    event.preventDefault();
                    var succes_msg = "<?= $trad_com_lang['SUCCES_ACTIVE'][$_SESSION['user']->getLangue()];?>";
                    var error_msg = "<?= $trad_com_lang['ERREUR_ACTIVE'][$_SESSION['user']->getLangue()];?>";
                    var t = $(this).attr("id").split("_");
                    var id = t[1];
                    var order = 'id=' + id;
                    $.post("components/com_lang/controleur/langue.php?task=enableLangue", order, function (theResponse) {
                        if (parseInt(theResponse) == 1) {
                            $('.msgbox').html('<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>' + succes + '</strong>' + succes_msg + '</div>');
                            $('.msgbox').slideDown();
                            setTimeout(function () {
                                document.location.reload();
                            }, 1000);
                        }
                        else {
                            $('.msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>' + error + '</strong>' + error_msg + '</div>');
                            $('.msgbox').slideDown();
                        }
                    });
                });
                $(".disable").click(function (event) {
                    event.preventDefault();
                    var succes_msg = "<?= $trad_com_lang['SUCCES_DESACTIVE'][$_SESSION['user']->getLangue()];?>";
                    var error_msg = "<?= $trad_com_lang['ERREUR_DESACTIVE'][$_SESSION['user']->getLangue()];?>";
                    if (confirm("<?= $trad_com_lang['QST_DESACTIVE'][$_SESSION['user']->getLangue()];?>")) {
                        var t = $(this).attr("id").split("_");
                        var id = t[1];
                        var order = 'id=' + id;
                        $.post("components/com_lang/controleur/langue.php?task=disableLangue", order, function (theResponse) {
                            if (parseInt(theResponse) == 1) {
                                $('.msgbox').html('<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>' + succes + '</strong>' + succes_msg + '</div>');
                                $('.msgbox').slideDown();
                                setTimeout(function () {
                                    document.location.reload();
                                }, 1000);
                            }
                            else {
                                $('.msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>' + error + '</strong>' + error_msg + '</div>');
                                $('.msgbox').slideDown();
                            }
                        });
                    }
                });
            });
        </script>
        <ol class="breadcrumb">
            <li><a href="index.php"><?= $trad_com_lang['TABLE_BORD'][$_SESSION['user']->getLangue()]; ?></a></li>
            <li class="active"><?= $trad_com_lang['com_lang'][$_SESSION['user']->getLangue()]; ?></li>
        </ol>

        <div class="widget widget-blue">
            <div class="widget-title">
                <div class="widget-controls">
                    <a href="#" class="widget-control widget-control-refresh" data-toggle="tooltip" data-placement="top"
                       title=""
                       data-original-title="<?= $trad_com_lang['ACTUALISER'][$_SESSION['user']->getLangue()]; ?>"><i
                                class="icon-refresh"></i></a>
                    <a href="#" class="widget-control widget-control-minimize" data-toggle="tooltip"
                       data-placement="top" title=""
                       data-original-title="<?= $trad_com_lang['MINIMISER'][$_SESSION['user']->getLangue()]; ?>"><i
                                class="icon-minus-sign"></i></a>
                </div>
                <h3><i class="icon-table"></i> <?= $trad_com_lang['LISTE_LANGUE'][$_SESSION['user']->getLangue()]; ?>
                </h3>
            </div>
            <div class="widget-content">
                <div class="msgbox"></div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th><?= $trad_com_lang['ID'][$_SESSION['user']->getLangue()]; ?></th>
                            <th><?= $trad_com_lang['NOM'][$_SESSION['user']->getLangue()]; ?></th>
                            <th><?= $trad_com_lang['CODE'][$_SESSION['user']->getLangue()]; ?></th>
                            <th><?= $trad_com_lang['DRAPEAU'][$_SESSION['user']->getLangue()]; ?></th>
                            <th><?= $trad_com_lang['ACTION'][$_SESSION['user']->getLangue()]; ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $SQLselect = "SELECT id FROM " . __prefixe_db__ . "langue ORDER BY date_add DESC";
                        $result = $db->queryS($SQLselect);
                        foreach ($result as $data) {
                            $l = new langue($data['id'], $db);
                            ?>
                            <tr id="row_<?php echo $l->getId(); ?>">
                                <td><?php echo $l->getId(); ?></td>
                                <td><?php echo $l->getNom() ?></td>
                                <td><?php echo $l->getCode() ?></td>
                                <td class="text-center">
                                    <?php if($l->getFlag()){ ?>
                                        <img src="../images/langues/<?php echo $l->getFlag(); ?>"
                                                                 height="15"/></td>
                                    <?php } ?>
                                <td class="text-center">
                                    <?php if ($l->isDefault()) { ?>
                                        <a href="#0" data-toggle="tooltip" data-placement="top"
                                           data-original-title="<?= $trad_com_lang['LANGUE_PAR_DEFAUT'][$_SESSION['user']->getLangue()]; ?>"
                                           class="btn btn-success btn-xs"><i class="icon-check2"></i></a>
                                    <?php } ?>
                                    <?php if ($_SESSION['user']->hasDroit('edit', 'com_lang')) { ?>
                                        <?php if ($l->isActif()) { ?>
                                            <a href="#0" id="disable_<?php echo $l->getId(); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               data-original-title="<?= $trad_com_lang['ACTIVE'][$_SESSION['user']->getLangue()]; ?>"
                                               class="btn btn-success btn-xs disable"><i
                                                        class="fa fa-toggle-on"></i></a>
                                        <?php } else { ?>
                                            <a href="#0" id="disable_<?php echo $l->getId(); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               data-original-title="<?= $trad_com_lang['DESACTIVE'][$_SESSION['user']->getLangue()]; ?>"
                                               class="btn btn-warning btn-xs enable"><i
                                                        class="fa fa-toggle-off"></i></a>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php if ($_SESSION['user']->hasDroit('edit', 'com_lang')) { ?>
                                        <a href="index.php?option=com_lang&task=edit&id=<?php echo $l->getId(); ?>"
                                           data-toggle="tooltip" data-placement="top"
                                           data-original-title="<?= $trad_com_lang['MODIFIER'][$_SESSION['user']->getLangue()]; ?>"
                                           class="btn btn-warning btn-xs"><i class="icon-pencil"></i></a>
                                    <?php } ?>
                                    <?php if ($_SESSION['user']->hasDroit('delete', 'com_lang')) { ?>
                                        <a href="#0" id="delete_<?php echo $l->getId(); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           data-original-title="<?= $trad_com_lang['SUPPRIMER'][$_SESSION['user']->getLangue()]; ?>"
                                           class="btn btn-danger btn-xs delete"><i class="icon-trash"></i></a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }

    /* ---------------------------- edit ---------------------------- */
    function edit()
    {
        global $db, $trad_com_lang;
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = intval($_GET['id']);
            $l = new langue($id, $db);
            ?>
            <ol class="breadcrumb">
                <li><a href="index.php"><?= $trad_com_lang['TABLE_BORD'][$_SESSION['user']->getLangue()]; ?></a></li>
                <li>
                    <a href="index.php?option=com_lang"><?= $trad_com_lang['LANGUES'][$_SESSION['user']->getLangue()]; ?></a>
                </li>
                <li class="active"><?= $trad_com_lang['MODIFIER_LANGUE'][$_SESSION['user']->getLangue()]; ?></li>
            </ol>
            <div class="row">
                <div class="col-md-12">
                    <div class="msgbox"></div> <!-- conteneur de message -->
                    <div class="widget widget-green">
                        <div class="widget-title">
                            <div class="widget-controls">
                                <a href="#" class="widget-control widget-control-refresh" data-toggle="tooltip"
                                   data-placement="top" title=""
                                   data-original-title="<?= $trad_com_lang['ACTUALISER'][$_SESSION['user']->getLangue()]; ?>"><i
                                            class="icon-refresh"></i></a>
                                <a href="#" class="widget-control widget-control-minimize" data-toggle="tooltip"
                                   data-placement="top" title=""
                                   data-original-title="<?= $trad_com_lang['MINIMISER'][$_SESSION['user']->getLangue()]; ?>"><i
                                            class="icon-minus-sign"></i></a>
                            </div>
                            <h3>
                                <i class="icon-edit-sign"></i> <?= $trad_com_lang['MODIFIER_LANGUE'][$_SESSION['user']->getLangue()]; ?>
                            </h3>
                        </div>
                        <div class="widget-content">
                            <?php include("components/com_lang/forms/form.php"); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    /* ---------------------------- add ---------------------------- */
    function add()
    {
        global $db, $trad_com_lang;
        ?>
        <ol class="breadcrumb">
            <li><a href="index.php"><?= $trad_com_lang['TABLE_BORD'][$_SESSION['user']->getLangue()]; ?></a></li>
            <li>
                <a href="index.php?option=com_lang"><?= $trad_com_lang['LANGUES'][$_SESSION['user']->getLangue()]; ?></a>
            </li>
            <li class="active"><?= $trad_com_lang['AJOUTER_LANGUE'][$_SESSION['user']->getLangue()]; ?></li>
        </ol>
        <div class="row">
            <div class="col-md-12">
                <div class="msgbox"></div> <!-- conteneur de message -->
                <div class="widget widget-green">
                    <div class="widget-title">
                        <div class="widget-controls">
                            <a href="#" class="widget-control widget-control-refresh" data-toggle="tooltip"
                               data-placement="top" title=""
                               data-original-title="<?= $trad_com_lang['ACTUALISER'][$_SESSION['user']->getLangue()]; ?>"><i
                                        class="icon-refresh"></i></a>
                            <a href="#" class="widget-control widget-control-minimize" data-toggle="tooltip"
                               data-placement="top" title=""
                               data-original-title="<?= $trad_com_lang['MINIMISER'][$_SESSION['user']->getLangue()]; ?>"><i
                                        class="icon-minus-sign"></i></a>
                        </div>
                        <h3>
                            <i class="icon-plus-sign-alt"></i> <?= $trad_com_lang['AJOUTER_LANGUE'][$_SESSION['user']->getLangue()]; ?>
                        </h3>
                    </div>
                    <div class="widget-content">
                        <?php include("components/com_lang/forms/form.php"); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    ?>
</div>

