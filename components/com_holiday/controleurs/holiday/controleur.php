<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addHoliday':
            addHoliday($_POST);
            break;
        case 'editHoliday':
            editHoliday($_POST);
            break;
        case 'deleteHoliday':
            deleteHoliday($_POST);
            break;
        case 'filterHolidays':
            filterHolidays($_POST);
            break;
    }
}

function addHoliday($data)
{
    $indices = array("name", "start_date", "end_date");
    if (fieldCheck($data, $indices)) {
        if (buildHoliday($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editHoliday($data)
{
    $indices = array("id", "name", "start_date", "end_date");
    if (fieldCheck($data, $indices)) {
        if (buildHoliday($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteHoliday($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $id = $data["id"];
        $holiday = holiday::find($id);
        if ($holiday->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildHoliday($data, $id = null)
{
    $holiday = new holiday();

    if ($id) {
        $holiday = holiday::find($id);
    }

    $holiday->setName($data['name']);
    $holiday->setStartDate(dateBD($data['start_date']));
    $holiday->setEndDate(dateBD($data['end_date']));
    $holiday->setRemarque($data['remarque']);
    $holiday->setDateAdd(date('Y-m-d'));
    $holiday->setLastEdit(date('Y-m-d'));

    return $holiday;
}



function filterHolidays($data)
{
    $holidays = holiday::findByYear($data['year']);
?>
    <table class="table table-stripped table-center table-hover datatable-holidays">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($holidays as $holiday) : ?>
                <tr class="<?= $holiday->getColor() ?>">
                    <td><?php echo $holiday->getId(); ?></td>
                    <td><?php echo $holiday->getName(); ?></td>
                    <td data-sort="<?= strtotime($holiday->getStartDate()) ?>"><?= normalDate($holiday->getStartDate()); ?></td>
                    <td data-sort="<?= strtotime($holiday->getEndDate()) ?>"><?= normalDate($holiday->getEndDate()); ?></td>
                    <td class="text-right">

                        <?php if ($_SESSION['user']->hasDroit('edit', 'com_holiday')) : ?>
                            <a href="index.php?option=com_holiday&task=edit&id=<?= $holiday->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                        <?php endif; ?>
                        <?php if ($_SESSION['user']->hasDroit('delete', 'com_holiday')) : ?>
                            <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $holiday->getId(); ?>"><i class="far fa-trash-alt"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <script>
        $(document).ready(function(){
            if ($('.datatable-holidays').length > 0) {
                $('.datatable-holidays').DataTable({aoColumnDefs:[{bSortable:!1,aTargets:[0,1]}],aaSorting:[],order:[[0,'desc']]});
            }
        })
    </script>
<?php
}
