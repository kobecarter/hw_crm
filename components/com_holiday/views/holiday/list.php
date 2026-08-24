<!-- Page Wrapper -->
<style>
	.table-expired-recently {
		background-color: rgba(220, 53, 69, 0.5);
	}

	.table-expired-recently:hover {
		background-color: rgba(220, 53, 69, 0.6) !important;
	}

	.table-expired {
		background-color: rgba(220, 53, 69, 0.7);
	}

	.table-expired:hover {
		background-color: rgba(220, 53, 69, 0.8) !important;
	}
</style>
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Jours fériés</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Jours fériés</li>
					</ul>
				</div>
				<div class="col-auto">
					<?php if ($_SESSION['user']->hasDroit('add', 'com_holiday')) :?>
    					<a href="index.php?option=com_holiday&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter un jour férié">
    						<i class="fas fa-plus"></i>
    					</a>
					<?php endif;?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">Calendrier des jours fériés et événements</h4>
					</div>
					<div class="card-body">
						<div id="holiday-calendar"></div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Liste des jours fériés</h4>
					</div>
					<div class="card-body">
						<div class="col-sm-12 mt-3 msgbox"></div>
						<div class="row justify-content-end">
                            <div class="col-2">
								<div class="form-group px-4">
									<select class="select" name="year" style="width:300px">
										<?php for ($i=date('Y'); $i >=2023 ; $i--) :?>
											<option vallue="<?=$i?>"><?=$i?></option>
										<?php endfor;?>
									</select>
								</div>
                            </div>
                        </div>
						<div class="table-responsive table-holidays">
							
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
<!-- /Page Wrapper -->
<script type="text/javascript">
	function filterHolidays(year){
        let order = "year="+year
        $.post("components/com_holiday/controleurs/router.php?task=filterHolidays", order, function(theResponse) {
            console.log(theResponse);
            $(".table-holidays").html(theResponse)
        });
    }
	$(function() {
		var msgsucces = "Jour férié supprimé avec succès";
		$(document).on("click", ".delete", function() {
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_holiday/controleurs/router.php?task=deleteHoliday", order, function(theResponse) {
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

		$(document).on("change", "select[name='year']", function() {
            event.preventDefault();
            var value = $(this).val();
            filterHolidays(value)
        })
        filterHolidays($('select[name="year"]').val())

        if ($('#holiday-calendar').length) {
            $('#holiday-calendar').fullCalendar($.extend({
                header: { left: 'prev,next today', center: 'title', right: 'month,agendaWeek,listYear' },
                height: 650,
                // JSON_INVALID_UTF8_SUBSTITUTE : sans ce flag, UN SEUL jour férié avec un octet
                // invalide en base (ex. colonne encore en latin1 quelque part) fait échouer
                // json_encode() pour TOUT le tableau -> "events:" vide -> erreur de syntaxe JS qui
                // bloque tout le bloc <script> (calendrier ET tableau, chargé juste avant en AJAX).
                events: <?php echo json_encode(array_map(array('holiday', 'toCalendarEvent'), $holidays), JSON_INVALID_UTF8_SUBSTITUTE); ?>
            }, window.fullCalendarFrDefaults));
        }
	});
</script>