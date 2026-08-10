<?php

@$task = $_GET['task'];
switch ($task)
{
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_service')) {
            $action = "components/com_service/controleurs/router.php?task=addService";
            $submitName = "add";
            $submitValue = "Ajouter service";
			$categories = categorie::findAll($_SESSION["langue"], true, true);
			$intervenantsConnus = service::intervenantsConnus();
            include_once("components/com_service/views/service/add.php");
        }
        break;
    case 'edit' :
        if ($_SESSION['user']->hasDroit('edit', 'com_service')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $service = service::find($id, $_SESSION['langue']);
                $action = "components/com_service/controleurs/router.php?task=editService";
                $submitName = "edit";
                $submitValue = "Modifier service";
                $categories = categorie::findAll($_SESSION["langue"], true, true);
                $intervenantsConnus = service::intervenantsConnus();
                include_once("components/com_service/views/service/edit.php");
            }
        }
        break;
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_service')) {
            // Vue par défaut : uniquement les services actifs (le catalogue accumule des dizaines
            // de lignes historiques inactives rattachées à des catégories supprimées depuis -
            // volontairement masquées pour ne pas noyer le catalogue courant). Lien "Afficher les
            // services inactifs" pour les retrouver au besoin.
            $tousLesServices = isset($_GET['tous']) && $_GET['tous'] == '1';
            $services = service::findAll($_SESSION["langue"], $tousLesServices ? false : true, false, true);
            $categoriesService = categorie::findAll($_SESSION["langue"], true, true);

            $kpiServicesActifs = 0;
            $kpiPacksActifs = 0;
            $kpiCategoriesRepresentees = array();
            foreach ($services as $s) {
                if (!$s->isActive()) { continue; }
                $kpiServicesActifs++;
                if ($s->isPack()) { $kpiPacksActifs++; }
                if ($s->getCategorie() && $s->getCategorie()->getId()) {
                    $kpiCategoriesRepresentees[$s->getCategorie()->getId()] = true;
                }
            }
            include_once("components/com_service/views/service/list.php");
        }
        break;
}