<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Gestion des temoignage</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="index.php?option=com_temoignage">Temoignages</a></li>
                        <li class="breadcrumb-item active">Modifier temoignage</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><?php echo $submitValue; ?></h4>
                    </div>
                    <div class="card-body">
                        <?php include("form.php"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>