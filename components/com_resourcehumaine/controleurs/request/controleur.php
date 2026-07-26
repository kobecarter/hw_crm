<?php

use Mpdf\Tag\NewColumn;

if (isset($task) && !empty($task)) {
    switch ($task) {
        case "addRequest":
            addRequest($_POST);
            break;
        case "editRequest":
            editRequest($_POST);
            break;
        case "deleteRequest":
            deleteRequest($_POST);
            break;
        case "showRequest":
            showRequest($_POST);
            break;
        case "changeStatusRequest":
            changeStatusRequest($_POST);
            break;
        case "showSetResponseRequest":
            showSetResponseRequest($_POST);
            break;
        case "setResponseRequest":
            setResponseRequest($_POST);
            break;
    }
}

function addRequest($data)
{
    $indices = array("id_resourcehumaine", "title", "description");
    if (validateRequest($data, $indices)) {
        if (buildRequest($data)->add() == 1){
            $last_id = request::getLastId();
            $request = request::find($last_id, $_SESSION["langue"]);
            $notification = [
                "id" => null,
                "user_id" => null,
                "type_user" => "user",
                "title" => "Nouvelle demande de " . $request->getResourcehumaine()->getFirstName() . " " . $request->getResourcehumaine()->getLastName(),
                "message" => $request->getTitle(),
                "type_message" => "success",
                "url" => "index.php?option=com_resourcehumaine&task=request&id=".$request->getResourcehumaine()->getId(),
                "class" => "request",
                "data" => $request->getId(),
                "is_read" => 0,
                "date_add" => date("Y-m-d"),
                "date_edit" => date("Y-m-d")
            ];
            notification::build($notification)->add();
            echo "1";
        }else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editRequest($data)
{
    $indices = array("id", "id_resourcehumaine", "title", "description");
    if (validateRequest($data, $indices)) {
        if (buildRequest($data, $data['id'])->edit() == 1){
            $request = request::find($data['id'], $_SESSION["langue"]);
            $user_id = !$_SESSION["user"]->isResourceHumaine() ? $request->getResourcehumaine()->getId() : null;
            $type_user = !$_SESSION["user"]->isResourceHumaine() ? "resourcehumaine" : "user";
            $title = !$_SESSION["user"]->isResourceHumaine() ? "L'Administrateur a mis à jour la demande"  : $request->getResourcehumaine()->getFirstName() . " " . $request->getResourcehumaine()->getLastName(). " a mis à jour la demande";
            $url = !$_SESSION["user"]->isResourceHumaine() ? "index.php?task=requests" : "index.php?option=com_resourcehumaine&task=request&id=".$request->getResourcehumaine()->getId();
            $notification = [
                "id" => null,
                "user_id" => $user_id,
                "type_user" => $type_user,
                "title" => $title,
                "message" => $request->getTitle(),
                "type_message" => "warning",
                "url" => $url,
                "class" => "request",
                "data" => $request->getId(),
                "is_read" => 0,
                "date_add" => date("Y-m-d"),
                "date_edit" => date("Y-m-d")
            ];
            notification::build($notification)->add();
            echo "1";
        }else{
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteRequest($data)
{
    $indices = array("id");
    if (validateRequest($data, $indices)) {
        $id = $data["id"];
        $request = request::find($id, $_SESSION["langue"]);
        if ($request->delete() == 1) {
            $user_id = null;
            $type_user = "user";
            $title = $request->getResourcehumaine()->getFirstName() . " " . $request->getResourcehumaine()->getLastName(). " a supprimé la demande";
            $url = null;
            $notification = [
                "id" => null,
                "user_id" => $user_id,
                "type_user" => $type_user,
                "title" => $title,
                "message" => $request->getTitle(),
                "type_message" => "danger",
                "url" => $url,
                "class" => "request",
                "data" => $request->getId(),
                "is_read" => 0,
                "date_add" => date("Y-m-d"),
                "date_edit" => date("Y-m-d")
            ];
            notification::build($notification)->add();
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function setResponseRequest($data)
{
    $indices = array("id", "response");
    if (validateRequest($data, $indices)) {
        $request = request::find($data['id'], $_SESSION["langue"]);
        $request->setResponse($data['response']);
        if ($request->edit() == 1){
            $user_id = $request->getResourcehumaine()->getId();
            $type_user = "resourcehumaine";
            $title = "L'administrateur a défini une réponse dans la demande";
            $url = "index.php?task=requests";
            $notification = [
                "id" => null,
                "user_id" => $user_id,
                "type_user" => $type_user,
                "title" => $title,
                "message" => $request->getTitle(),
                "type_message" => "warning",
                "url" => $url,
                "class" => "request",
                "data" => $request->getId(),
                "is_read" => 0,
                "date_add" => date("Y-m-d"),
                "date_edit" => date("Y-m-d")
            ];
            notification::build($notification)->add();
            echo "1";
        }else{
            echo "2";
        } 
    } else {
        echo "0";
    }
}

function changeStatusRequest($data)
{
    $indices = array("id","status");
    if (validateRequest($data, $indices)) {
        $id = $data["id"];
        $status = 0;
        if($data['status'] == "approve"){
            $status = 1;
        }else if($data['status'] == "reject"){
            $status = 2;
        }
        $request = request::find($id, $_SESSION["langue"]);
        $request->setStatus($status);
        if ($request->edit() == 1) {
            $user_id = $request->getResourcehumaine()->getId();
            $type_user = "resourcehumaine";
            $title = "Message non defini";
            $status = "danger"; 
            if($request->getStatus() == 1){
                $title = "L'administrateur a apprové la demande";
                $status = "success";
            }elseif($request->getStatus() == 2){
                $title = "L'administrateur a refusé la demande";
                $status = "danger";
            }
            $url = "index.php?task=requests";
            $notification = [
                "id" => null,
                "user_id" => $user_id,
                "type_user" => $type_user,
                "title" => $title,
                "message" => $request->getTitle(),
                "type_message" => $status,
                "url" => $url,
                "class" => "request",
                "data" => $request->getId(),
                "is_read" => 0,
                "date_add" => date("Y-m-d"),
                "date_edit" => date("Y-m-d")
            ];
            notification::build($notification)->add();
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function showRequest($data)
{
    $id = $data['id'];
    if (isset($id) && !empty($id)) {
        $request = request::find($id, $_SESSION["langue"]);
?>
        <table class="table table-show table-request-detail">
            <tbody>
                <tr>
                    <th>Titre</th>
                    <td><span class="<?=$request->getTitle() ? 'text-secondary' : 'text-danger'?>"><?=$request->getTitle() ? $request->getTitle() : 'Indéfinie'?></span></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td><p class="break-lines <?=$request->getDescription() ? 'text-secondary' : 'text-danger'?>"><?=$request->getDescription() ? $request->getDescription() : 'Indéfinie'?></p></td>
                </tr>
                <tr>
                    <th>Réponse</th>
                    <td><span class="<?=$request->getResponse() ? 'text-secondary' : 'text-danger'?>"><?=$request->getResponse() ? $request->getResponse() : 'Indéfinie'?></span></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <?php
                    $class="";
                    $value="Indéfinie";
                    if($request->getStatus() == 0){
                        $class="badge-warning text-white";
                        $value="En attente";
                    }else if($request->getStatus() == 1){
                        $class="badge-success text-white";
                        $value="Apprové";
                    }else if($request->getStatus() == 2){
                        $class="badge-danger text-white";
                        $value="Refusé";
                    }
                    ?>
                    <td><span class="badge <?=$class?>"><?=$value?></span></td>
                </tr>
            </tbody>
        </table>
<?php
    }
}

function showSetResponseRequest($data)
{
    $id = $data['id'];
    if (isset($id) && !empty($id)) {
        $request = request::find($id, $_SESSION["langue"]);
?>
        <form method="post" action="components/com_resourcehumaine/controleurs/router.php?task=setResponseRequest" id="setResponseForm">
            <input type="hidden" name="id" value="<?=$request->getId()?>">
            <div class="row">
            <div class="col-12 msgbox"></div>
                <div class="col-12">
                    <div class="form-group">
                        <label>Réponse</label>
                        <textarea name="response" class="form-control" rows="6"><?=$request->getResponse()?></textarea>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Valider</button>
                    </div>
                </div>
            </div>
        </form>

        <script>
            $(function() {
                // envoi du formulaire en ajax
                $('form#setResponseForm').ajaxForm({
                    beforeSubmit: function() {
                        $(".loading").fadeIn();
                    },
                    success: function(theResponse) {
                        console.log(theResponse)    
                        $(".loading").fadeOut();
                        $("html, body").animate({
                            scrollTop: 0
                        }, "slow");
                        if (parseInt(theResponse) === 1) {
                            $('#setResponseForm .msgbox').html(
                                '<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>Succès</strong> Réponse effectuée avec successe.</div>').slideDown();
                            let index = $('#div-show-model').attr('data-tr')
                            let text = $('#setResponseForm').find('textarea').val()
                            console.log(index)
                            console.log(text)
                            $(".table-requests tbody").find('tr:eq('+index+')').find('td.response').html('<span title="'+text+'">'+text.substring(0,30)+ (text.length > 30 ? ' [...]' : '')+'</span>')
                            setTimeout(function() {
                                $(".div-show-model-content").html("");
                                $("#div-show-model").modal('hide');
                            },2000)
                        } else if (parseInt(theResponse) === 0) {
                            $('#setResponseForm .msgbox').html(
                                '<div class="alert alert-warning alert-dismissable"><i class="icon-remove-sign"></i> <strong>Attention!</strong> Réponse non effectuée.</div>').slideDown();
                        } else {
                            $('#setResponseForm .msgbox').html(
                                '<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>Erreur!</strong> Une erreur est survenue.</div>').slideDown();
                        }
                    }
                });
            })
            </script>
<?php
    }
}


function validateRequest($data = array(), $indices = array())
{
    foreach ($indices as $indice) {
        if ($indice == "file") {
            if (!isset($_FILES["file"]) || $_FILES["file"]["name"][0] == "") {
                return false;
            }
        } else {
            if (!isset($data[$indice]) || empty($data[$indice])) {
                return false;
            }
        }
    }
    return true;
}

function buildRequest($data, $id = null)
{
    $request = new request();
    if ($id) {
        $request = request::find($id);
    }

    $request->setResourcehumaine(resourcehumaine::find($_SESSION['user']->getId()));
    $request->setTitle($data["title"]);
    $request->setDescription($data["description"]);
    $request->setStatus(isset($_POST["status"]) ? 1 : 0);
    $request->setDateAdd(date("Y-m-d"));
    $request->setDateEdit(date("Y-m-d"));

    return $request;
}
