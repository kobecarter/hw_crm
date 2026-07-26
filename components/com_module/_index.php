<?php
include_once "components/com_module/traduction.php";
?>

<div class="sub-sidebar-wrapper">
    <ul>
        <?php if ($_SESSION['user']->hasDroit('add', 'com_module')) { ?>
            <li>
                <a href="index.php?option=com_module&task=add"> <?= $trad_com_module['AJOUTER_MODULE'][$_SESSION['user']->getLangue()]; ?></a>
            </li>
        <?php } ?>
        <?php if ($_SESSION['user']->hasDroit('view', 'com_module')) { ?>
            <li>
                <a href="index.php?option=com_module"> <?= $trad_com_module['LISTE_MODULE'][$_SESSION['user']->getLangue()]; ?></a>
            </li>
        <?php } ?>
    </ul>
</div>

<div class="main-content">
    <?php
    @$task = $_GET['task'];
    switch ($task) {
        case 'add' :
            if ($_SESSION['user']->hasDroit('add', 'com_module')) {
                add();
            }
            break;
        case 'edit' :
            if ($_SESSION['user']->hasDroit('edit', 'com_module')) {
                edit();
            }
            break;
        default :
            if ($_SESSION['user']->hasDroit('view', 'com_module')) {
                showList();
            } // Charge la liste des articles
    }

    /* ---------------------------- function ---------------------------- */

    /* ---------------------------- showList ---------------------------- */
    function showList()
    {
        global $db, $trad_com_module;
        ?>

        <script type="text/javascript">

            $(function () {
                var succes = "<?= $trad_com_module['SUCCES'][$_SESSION['user']->getLangue()];?>";
                var error = "<?= $trad_com_module['ERREUR'][$_SESSION['user']->getLangue()];?>";

                $(".delete").click(function (event) {
                    event.preventDefault();
                    if (confirm("<?= $trad_com_module['QST_DEL'][$_SESSION['user']->getLangue()];?>")) {
                        var succes_msg = "<?= $trad_com_module['SUCCES_DEL'][$_SESSION['user']->getLangue()];?>";
                        var error_msg = "<?= $trad_com_module['ERREUR_DEL'][$_SESSION['user']->getLangue()];?>";
                        var t = $(this).attr("id").split("-");
                        var id = t[1];
                        var order = 'id=' + id;
                        $.post("components/com_module/controleur/module.php?task=deleteModule", order, function (theResponse) {
                            if (parseInt(theResponse) === 1) {
                                $("#row_" + id).addClass("danger");
                                setTimeout(function () {
                                    $("#row_" + id).remove()
                                }, 300);
                                $('.msgbox').html('<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>' + succes + '</strong> ' + succes_msg + '</div>').slideDown();
                            }
                            else {
                                $('.msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>' + error + '</strong> ' + error_msg + '</div>').slideDown();
                            }
                        });
                    }
                });

                $(".install").click(function (event) {
                    event.preventDefault();
                    var succes_msg = "<?= $trad_com_module['SUCCES_INSTALL'][$_SESSION['user']->getLangue()];?>";
                    var error_msg = "<?= $trad_com_module['ERREUR_INSTALL'][$_SESSION['user']->getLangue()];?>";
                    var t = $(this).attr("id").split("-");
                    var id = t[1];
                    var order = 'id=' + id;
                    $.post("components/com_module/controleur/module.php?task=installModule", order, function (theResponse) {
                        if (parseInt(theResponse) === 1) {
                            $('.msgbox').html('<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>' + succes + '</strong> ' + succes_msg + '</div>').slideDown();
                            setTimeout(function () {
                                document.location.reload();
                            }, 1000);
                        }
                        else {
                            $('.msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>' + error + '</strong> ' + error_msg + '</div>').slideDown();
                        }
                    });
                });

                $(".desinstall").click(function (event) {
                    event.preventDefault();
                    if (confirm("<?= $trad_com_module['QST_DESINSTALL'][$_SESSION['user']->getLangue()];?>")) {
                        var succes_msg = "<?= $trad_com_module['SUCCES_DESINSTALL'][$_SESSION['user']->getLangue()];?>";
                        var error_msg = "<?= $trad_com_module['ERREUR_DESINSTALL'][$_SESSION['user']->getLangue()];?>";
                        var t = $(this).attr("id").split("-");
                        var id = t[1];
                        var order = 'id=' + id;
                        $.post("components/com_module/controleur/module.php?task=desinstallModule", order, function (theResponse) {
                            if (parseInt(theResponse) === 1) {
                                $('.msgbox').html('<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>' + succes + '</strong> ' + succes_msg + '</div>').slideDown();
                                setTimeout(function () {
                                    document.location.reload();
                                }, 1000);
                            }
                            else {
                                $('.msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>' + error + '</strong> ' + error_msg + '</div>').slideDown();
                            }
                        });
                    }
                });

                $(".enable").click(function (event) {
                    event.preventDefault();
                    var succes_msg = "<?= $trad_com_module['SUCCES_ACTIVE'][$_SESSION['user']->getLangue()];?>";
                    var error_msg = "<?= $trad_com_module['ERREUR_ACTIVE'][$_SESSION['user']->getLangue()];?>";
                    var t = $(this).attr("id").split("-");
                    var id = t[1];
                    var order = 'id=' + id;
                    $.post("components/com_module/controleur/module.php?task=enableModule", order, function (theResponse) {
                        if (parseInt(theResponse) === 1) {
                            $('.msgbox').html('<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>' + succes + '</strong> ' + succes_msg + '</div>').slideDown();
                            setTimeout(function () {
                                document.location.reload();
                            }, 1000);
                        }
                        else {
                            $('.msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>' + error + '</strong> ' + error_msg + '</div>').slideDown();
                        }
                    });
                });

                $(".disable").click(function (event) {
                    event.preventDefault();
                    var succes_msg = "<?= $trad_com_module['SUCCES_DESACTIVE'][$_SESSION['user']->getLangue()];?>";
                    var error_msg = "<?= $trad_com_module['ERREUR_DESACTIVE'][$_SESSION['user']->getLangue()];?>";
                    if (confirm("<?= $trad_com_module['QST_DESACTIVE'][$_SESSION['user']->getLangue()];?>")) {
                        var t = $(this).attr("id").split("-");
                        var id = t[1];
                        var order = 'id=' + id;
                        $.post("components/com_module/controleur/module.php?task=disableModule", order, function (theResponse) {
                            if (parseInt(theResponse) === 1) {
                                $('.msgbox').html('<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>' + succes + '</strong> ' + succes_msg + '</div>').slideDown();
                                setTimeout(function () {
                                    document.location.reload();
                                }, 1000);
                            }
                            else {
                                $('.msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>' + error + '</strong> ' + error_msg + '</div>').slideDown();
                            }
                        });
                    }
                });

                $(".migrate").click(function (event) {
                    event.preventDefault();
                    var succes_msg = "<?= $trad_com_module['SUCCES_MIGRATE'][$_SESSION['user']->getLangue()];?>";
                    var error_msg = "<?= $trad_com_module['ERREUR_MIGRATE'][$_SESSION['user']->getLangue()];?>";
                    var t = $(this).attr("id").split("-");
                    var id = t[1];
                    var order = 'id=' + id;
                    $.post("components/com_module/controleur/module.php?task=migrateModule", order, function (theResponse) {
                        if (parseInt(theResponse) === 1) {
                            $('.msgbox').html('<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>' + succes + '</strong> ' + succes_msg + '</div>').slideDown();
                            setTimeout(function () {
                                document.location.reload();
                            }, 1000);
                        }
                        else {
                            $('.msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>' + error + '</strong> ' + error_msg + '</div>').slideDown();
                        }
                    });
                });

            });
        </script>

        <ol class="breadcrumb">
            <li><a href="index.php"><?= $trad_com_module['TABLE_BORD'][$_SESSION['user']->getLangue()]; ?></a></li>
            <li class="active"><?= $trad_com_module['com_module'][$_SESSION['user']->getLangue()]; ?></li>
        </ol>

        <div class="widget widget-blue">
            <div class="widget-title">
                <div class="widget-controls">
                    <a href="#" class="widget-control widget-control-refresh" data-toggle="tooltip" data-placement="top"
                       title=""
                       data-original-title="<?= $trad_com_module['ACTUALISER'][$_SESSION['user']->getLangue()]; ?>"><i
                                class="icon-refresh"></i></a>
                    <a href="#" class="widget-control widget-control-minimize" data-toggle="tooltip"
                       data-placement="top" title=""
                       data-original-title="<?= $trad_com_module['MINIMISER'][$_SESSION['user']->getLangue()]; ?>"><i
                                class="icon-minus-sign"></i></a>
                </div>
                <h3><i class="icon-table"></i> <?= $trad_com_module['LISTE_MODULE'][$_SESSION['user']->getLangue()]; ?>
                </h3>
            </div>
            <div class="widget-content">
                <div class="msgbox"></div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th><?= $trad_com_module['ID'][$_SESSION['user']->getLangue()]; ?></th>
                            <th><?= $trad_com_module['NOM'][$_SESSION['user']->getLangue()]; ?></th>
                            <th><?= $trad_com_module['ICONE'][$_SESSION['user']->getLangue()]; ?></th>
                            <th><?= $trad_com_module['ACTION'][$_SESSION['user']->getLangue()]; ?></th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php

                        $directory = "components/";
                        $items = scandir($directory);
                        foreach ($items as $item){
                        if ($item == "." || $item == "..") {
                            continue;
                        }
                        $item = $item . "/";
                        if (is_dir($directory . $item)){
                        if (preg_match("/^com_/", $item)) {
                        $xml = simplexml_load_file($directory . $item . "config.xml");
                        ?>
                        <tr id="row_<?php echo $xml->id; ?>">
                            <td><?php echo $xml->id; ?></td>
                            <td><?php echo $xml->nom; ?></td>
                            <td class="text-center"><?php echo '<div class="fa fa-' . $xml->icon . '"></div>'; ?></td>
                            <?php
                            $m = new module($xml->id, $db, $_SESSION['langue']);
                            ?>
                            <td class="text-center">
                                <?php if (!$m->isInstalled() && !$m->isEnabled()) { ?>
                                    <?php if ($_SESSION['user']->hasDroit('add', 'com_module')) { ?>
                                        <a href="javascript:void(0)" id="install-<?php echo $m->getIdModule(); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           data-original-title="<?= $trad_com_module['INSTALLER'][$_SESSION['user']->getLangue()]; ?>"
                                           class="btn btn-info btn-xs install"><i class="fa fa-inbox"></i></a>
                                    <?php } ?>
                                    <?php if ($_SESSION['user']->hasDroit('delete', 'com_module') && !$m->isSystem()) { ?>
                                        <a href="javascript:void(0)" id="delete-<?php echo $m->getIdModule(); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           data-original-title="<?= $trad_com_module['SUPPRIMER'][$_SESSION['user']->getLangue()]; ?>"
                                           class="btn btn-danger btn-xs delete"><i class="fa fa-trash"></i></a>
                                    <?php } ?>
                                <?php } else if ($m->isInstalled() && !$m->isEnabled()) { ?>
                                    <?php if ($_SESSION['user']->hasDroit('edit', 'com_module')) { ?>
                                        <a href="javascript:void(0)" id="enable-<?php echo $m->getIdModule(); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           data-original-title="<?= $trad_com_module['DESACTIVE'][$_SESSION['user']->getLangue()]; ?>"
                                           class="btn btn-warning btn-xs enable"><i class="fa fa-toggle-off"></i></a>
                                    <?php } ?>
                                    <?php if ($_SESSION['user']->hasDroit('delete', 'com_module') && !$m->isSystem()) { ?>
                                        <a href="javascript:void(0)" id="desinstall-<?php echo $m->getIdModule(); ?>"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           data-original-title="<?= $trad_com_module['DESINSTALLER'][$_SESSION['user']->getLangue()]; ?>"
                                           class="btn btn-default btn-xs desinstall"><i class="fa fa-archive"></i></a>
                                    <?php } ?>
                                    <?php if ($_SESSION['user']->hasDroit('delete', 'com_module') && !$m->isSystem()) { ?>
                                        <a href="javascript:void(0)" id="delete-<?php echo $m->getIdModule(); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           data-original-title="<?= $trad_com_module['SUPPRIMER'][$_SESSION['user']->getLangue()]; ?>"
                                           class="btn btn-danger btn-xs delete"><i class="fa fa-trash"></i></a>
                                    <?php } ?>
                                <?php } else if ($m->isInstalled() && $m->isEnabled()) { ?>
                                    <?php if ($_SESSION['user']->hasDroit('edit', 'com_module') && !$m->isSystem()) { ?>
                                        <a href="javascript:void(0)" id="disable-<?php echo $m->getIdModule(); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           data-original-title="<?= $trad_com_module['ACTIVE'][$_SESSION['user']->getLangue()]; ?>"
                                           class="btn btn-success btn-xs disable"><i class="fa fa-toggle-on"></i></a>
                                    <?php } ?>
                                    <?php if ($m->isTranslated()) { ?>
                                        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top"
                                           data-original-title="<?= $trad_com_module['TRADUCTION'][$_SESSION['user']->getLangue()]; ?>"
                                           class="btn btn-primary btn-xs"><i
                                                    class="fa fa-globe"></i></a>
                                    <?php } ?>
                                    <?php if ($m->isUrl()) { ?>
                                        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top"
                                           data-original-title="<?= $trad_com_module['URL'][$_SESSION['user']->getLangue()]; ?>"
                                           class="btn btn-default btn-xs"><i
                                                    class="fa fa-link"></i></a>
                                    <?php } ?>
                                    <?php if ($_SESSION['user']->hasDroit('edit', 'com_module') && !$m->isSystem()) { ?>
                                        <a href="index.php?option=com_module&task=edit&id_module=<?php echo $m->getIdModule(); ?>"
                                           data-toggle="tooltip" data-placement="top"
                                           data-original-title="<?= $trad_com_module['MODIFIER'][$_SESSION['user']->getLangue()]; ?>"
                                           class="btn btn-warning btn-xs"><i class="icon-pencil"></i></a>
                                    <?php } ?>
                                    <?php if ($m->isSystem()) { ?>
                                        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top"
                                           data-original-title="<?= $trad_com_module['SYSTEME'][$_SESSION['user']->getLangue()]; ?>"
                                           class="btn btn-danger btn-xs"><i
                                                    class="fa fa-plug"></i></a>
                                    <?php } ?>
                                    <?php if ($_SESSION['user']->hasDroit('edit', 'com_module') && $m->getIdModule() != "com_login") { ?>
                                        <a href="index.php?option=<?php echo $m->getIdModule(); ?>"
                                           data-toggle="tooltip" data-placement="top"
                                           data-original-title="<?= $trad_com_module['CONSULTER'][$_SESSION['user']->getLangue()]; ?>"
                                           class="btn btn-default btn-xs"><i class="fa fa-eye"></i></a>
                                    <?php } ?>
                                    <?php if(file_exists($directory . $item . "migration.php")) { ?>
                                        <a href="javascript:void(0)"
                                           data-toggle="tooltip" data-placement="top"
                                           id="migrate-<?= $m->getIdModule();?>"
                                           data-original-title="<?= $trad_com_module['MIGRATION'][$_SESSION['user']->getLangue()]; ?>"
                                           class="btn btn-success btn-xs migrate"><i class="fa fa-database"></i></a>
                                    <?php } ?>
                                <?php } ?>
                            </td>
                            <?php
                            }
                            }
                            }
                            ?>
                        </tr>
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
        global $db, $trad_com_module;

        if (isset($_GET['id_module']) && !empty($_GET['id_module'])) {
            $id_module = $_GET['id_module'];
            $m = new module($id_module, $db);
            ?>
            <ol class="breadcrumb">
                <li><a href="index.php"><?= $trad_com_module['TABLE_BORD'][$_SESSION['user']->getLangue()]; ?></a></li>
                <li>
                    <a href="index.php?option=com_module"><?= $trad_com_module['MODULES'][$_SESSION['user']->getLangue()]; ?></a>
                </li>
                <li class="active"><?= $trad_com_module['MODIFIER_MODULE'][$_SESSION['user']->getLangue()]; ?></li>
            </ol>
            <div class="row">
                <div class="col-md-12">
                    <div class="msgbox"></div> <!-- conteneur de message -->
                    <div class="widget widget-green">
                        <div class="widget-title">
                            <div class="widget-controls">
                                <a href="#" class="widget-control widget-control-refresh" data-toggle="tooltip"
                                   data-placement="top" title=""
                                   data-original-title="<?= $trad_com_module['ACTUALISER'][$_SESSION['user']->getLangue()]; ?>"><i
                                            class="icon-refresh"></i></a>
                                <a href="#" class="widget-control widget-control-minimize" data-toggle="tooltip"
                                   data-placement="top" title=""
                                   data-original-title="<?= $trad_com_module['MINIMISER'][$_SESSION['user']->getLangue()]; ?>"><i
                                            class="icon-minus-sign"></i></a>
                            </div>
                            <h3>
                                <i class="icon-edit-sign"></i> <?= $trad_com_module['MODIFIER_MODULE'][$_SESSION['user']->getLangue()]; ?>
                            </h3>
                        </div>
                        <div class="widget-content">
                            <?php include("components/com_module/forms/form.php"); ?>
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
        global $db, $trad_com_module;
        ?>
        <ol class="breadcrumb">
            <li><a href="index.php"><?= $trad_com_module['TABLE_BORD'][$_SESSION['user']->getLangue()]; ?></a></li>
            <li>
                <a href="index.php?option=com_module"><?= $trad_com_module['MODULES'][$_SESSION['user']->getLangue()]; ?></a>
            </li>
            <li class="active"><?= $trad_com_module['AJOUTER_MODULE'][$_SESSION['user']->getLangue()]; ?></li>
        </ol>
        <div class="row">
            <div class="col-md-12">
                <div class="msgbox"></div> <!-- conteneur de message -->
                <div class="widget widget-green">
                    <div class="widget-title">
                        <div class="widget-controls">
                            <a href="#" class="widget-control widget-control-refresh" data-toggle="tooltip"
                               data-placement="top" title=""
                               data-original-title="<?= $trad_com_module['ACTUALISER'][$_SESSION['user']->getLangue()]; ?>"><i
                                        class="icon-refresh"></i></a>
                            <a href="#" class="widget-control widget-control-minimize" data-toggle="tooltip"
                               data-placement="top" title=""
                               data-original-title="<?= $trad_com_module['MINIMISER'][$_SESSION['user']->getLangue()]; ?>"><i
                                        class="icon-minus-sign"></i></a>
                        </div>
                        <h3>
                            <i class="icon-plus-sign-alt"></i> <?= $trad_com_module['AJOUTER_MODULE'][$_SESSION['user']->getLangue()]; ?>
                        </h3>
                    </div>
                    <div class="widget-content">
                        <?php include("components/com_module/forms/upload.php"); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    ?>
</div>
