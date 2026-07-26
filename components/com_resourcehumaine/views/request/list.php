<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Demandes</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Demandes</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="div-round-profile mb-3">
                                <img onerror="this.src='./images/default-image.jpeg'" src="./images/resourceshumaines/<?= $resourcehumaine->getPhoto() ?>" alt="<?= $resourcehumaine->getFirstName() . " " . $resourcehumaine->getLastName() ?>">
                            </div>
                            <h3 class="mb-0"><?= $resourcehumaine->getFirstName() . " " . $resourcehumaine->getLastName() ?></h3>
                            <span class="text-secondary"><?= $resourcehumaine->getFunction() ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include "components/com_resourcehumaine/views/request/requests.php"; ?>
        
    </div>
</div>