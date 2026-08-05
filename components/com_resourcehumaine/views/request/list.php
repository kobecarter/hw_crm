<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Demandes</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="index.php?option=com_resourcehumaine">Resources humaines</a></li>
                        <li class="breadcrumb-item active">Demandes : <?php echo $resourcehumaine->getFirstName() . " " . $resourcehumaine->getLastName() ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <?php include("components/com_resourcehumaine/views/resourcehumaine/_profile_header.php"); ?>

        <?php include "components/com_resourcehumaine/views/request/requests.php"; ?>
        
    </div>
</div>