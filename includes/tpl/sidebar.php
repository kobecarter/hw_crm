<!-- Sidebar -->
<?php $agence = agence::find(isset($_SESSION['agence']) ? $_SESSION['agence'] : 1, isset($_SESSION['lang']) ? $_SESSION['lang'] : null); ?>
<div class="sidebar" id="sidebar">
	<div style="width:100%;height:100%;background:#37afeb30;position: absolute;top: 0;left: 0;"></div>
	<div class="sidebar-inner slimscroll">
		<div id="sidebar-menu" class="sidebar-menu">
			<ul>
				<li class="menu-title"><span>Main</span></li>
				<li class="<?= !isset($_GET['option']) && !isset($_GET['task']) ? "active" : "" ?>">
					<a href="index.php"><i class="fa fa-home"></i> <span>Tableau de bord</span></a>
				</li>
				<li class="<?= isset($_GET['option']) && $_GET['option'] == "com_documentation" ? "active" : "" ?>">
					<a href="index.php?option=com_documentation"><i class="fa fa-book"></i> <span>Documentation</span></a>
				</li>
				<!-- Liens documents -->
				<?php if($_SESSION['user']->isResourceHumaine() || $_SESSION['user']->isSuperUser()) :?>
				<li class="submenu">
					<a href="#"><i class="fab fa-google-drive"></i> <span> Documents Drive</span> <span class="menu-arrow"></span></a>
					<ul class="p-0">
						<li><a href="https://docs.google.com/spreadsheets/d/1h8TfaUaZwVjAbSKnGPQbG_OYXZNBAyP5xG8wlKoMVdY/" target="_blank">Réunion technique</a></li>
						<li><a href="https://docs.google.com/spreadsheets/d/13tpVW3d1kI3ODGRYGg-s-Fk5Aq9mrewznEbJjfD2xRA/" target="_blank">Réunion administrative</a></li>
						<li><a href="https://docs.google.com/spreadsheets/d/1ORamihKzsaU-Uj89oPPis5YASis4p8lu438mtjfRdwA/" target="_blank">Réunion commerciale</a></li>
						<li><a href="https://docs.google.com/spreadsheets/d/1Epds_3x7hjvvirLXjMvMQ2SjpvtYGlVEYdHLBA7djuA/" target="_blank">Situation HW LABEL</a></li>
						<li><a href="https://docs.google.com/spreadsheets/d/1fVLZCvUokc84NLwPszf53yQmmAKUccW7zVRdhl8Akpc/" target="_blank">Situation VERSE CONCEPT</a></li>
					</ul>
				</li>
				<?php endif; ?>
				
				<?php if($_SESSION['user']->isResourceHumaine()) :?>
					<li class="<?= isset($_GET['task']) && $_GET['task']  == "absences" ? "active" : "" ?>">
						<a href="index.php?task=absences"><i class="fa fa-calendar"></i> <span>Absences et congés</span></a>
					</li>
					<li class="<?= isset($_GET['task']) && $_GET['task']  == "files" ? "active" : "" ?>">
						<a href="index.php?task=files"><i class="fa fa-file"></i> <span>Fichiers</span></a>
					</li>
					<li class="<?= isset($_GET['task']) && $_GET['task']  == "bonuses" ? "active" : "" ?>">
						<a href="index.php?task=bonuses"><i class="fas fa-dollar-sign"></i> <span>Primes</span></a>
					</li>
					<li class="<?= isset($_GET['task']) && $_GET['task']  == "payslips" ? "active" : "" ?>">
						<a href="index.php?task=payslips"><i class="fas fa-dollar-sign"></i> <span>Bulletins de paie</span></a>
					</li>
					<li class="<?= isset($_GET['task']) && $_GET['task']  == "requests" ? "active" : "" ?>">
						<a href="index.php?task=requests"><i class="fas fa-envelope"></i> <span>Demandes</span></a>
					</li>
				<?php
				endif;
				$modules = module::findAllPosition("side");
				foreach ($modules as $module) {
					$active = isset($_GET['option']) && $_GET['option'] == $module->getIdModule() ? "active" : "";
					$arrowactive = isset($_GET['option']) && $_GET['option'] == $module->getIdModule() ? "subdrop" : "";
					$submenulistactive = isset($_GET['option']) && $_GET['option'] == $module->getIdModule() ? "block" : "none";
					if ($_SESSION['user']->hasDroit('view', $module->getIdModule())) {
						if ($module->getIdModule() == "com_resourcehumaine") {
				?>
							<li class="submenu <?= $active ?>">
								<a href="#" class="<?php echo $arrowactive; ?>"><i class="fa fa-<?= $module->getIcon(); ?>"></i> <span> <?= $module->getNom(); ?></span> <span class="menu-arrow"></span></a>
								<ul style="display: <?= $submenulistactive ?>;" class="p-0">
									<li class="<?= isset($_GET['option']) && $_GET['option'] == $module->getIdModule() && isset($_GET['task']) && $_GET['task'] == 'personal' ? 'active' : null ?>"><a href="index.php?option=<?= $module->getIdModule(); ?>&task=personal">Personnel</a></li>
									<li class="<?= isset($_GET['option']) && $_GET['option'] == $module->getIdModule() && isset($_GET['task']) && $_GET['task'] == 'pointage' ? 'active' : null ?>"><a href="index.php?option=<?= $module->getIdModule(); ?>&task=pointage">Pointage</a></li>
								</ul>
							</li>
						<?php
						}
					}
				}
				foreach ($modules as $module) {
					$active = isset($_GET['option']) && $_GET['option'] == $module->getIdModule() ? "active" : "";
					$arrowactive = isset($_GET['option']) && $_GET['option'] == $module->getIdModule() ? "subdrop" : "";
					$submenulistactive = isset($_GET['option']) && $_GET['option'] == $module->getIdModule() ? "block" : "none";
					if ($_SESSION['user']->hasDroit('view', $module->getIdModule())) {
						if ($module->getIdModule() == "com_accounting") {
							// "Rapprochement Bancaire" est un module à part (option=com_rapprochement, pas
							// une task de com_accounting) mais rangé ici dans Comptabilité à la demande -
							// il faut donc élargir manuellement la détection active/ouverte du sous-menu.
							$accountingOuvert = isset($_GET['option']) && in_array($_GET['option'], [$module->getIdModule(), 'com_rapprochement']);
							$active = $accountingOuvert ? "active" : "";
							$arrowactive = $accountingOuvert ? "subdrop" : "";
							$submenulistactive = $accountingOuvert ? "block" : "none";
						?>
							<li class="submenu <?= $active ?>">
								<a href="#" class="<?php echo $arrowactive; ?>"><i class="fa fa-<?= $module->getIcon(); ?>"></i> <span> <?= $module->getNom(); ?></span> <span class="menu-arrow"></span></a>
								<ul style="display: <?= $submenulistactive ?>;" class="p-0">
									<li class="<?= isset($_GET['option']) && $_GET['option'] == $module->getIdModule() && isset($_GET['task']) && $_GET['task'] == 'cnss' ? 'active' : null ?>"><a href="index.php?option=<?= $module->getIdModule(); ?>&task=cnss">CNSS</a></li>
									<li class="<?= isset($_GET['option']) && $_GET['option'] == $module->getIdModule() && isset($_GET['task']) && $_GET['task'] == 'tva' ? 'active' : null ?>"><a href="index.php?option=<?= $module->getIdModule(); ?>&task=tva">TVA</a></li>
									<li class="<?= isset($_GET['option']) && $_GET['option'] == $module->getIdModule() && isset($_GET['task']) && $_GET['task'] == 'bilan' ? 'active' : null ?>"><a href="index.php?option=<?= $module->getIdModule(); ?>&task=bilan">BILAN</a></li>
									<li class="<?= isset($_GET['option']) && $_GET['option'] == $module->getIdModule() && isset($_GET['task']) && $_GET['task'] == 'taxeprofessionnelle' ? 'active' : null ?>"><a href="index.php?option=<?= $module->getIdModule(); ?>&task=taxeprofessionnelle">TAXE PRO</a></li>
									<li class="<?= isset($_GET['option']) && $_GET['option'] == $module->getIdModule() && isset($_GET['task']) && $_GET['task'] == 'impot' ? 'active' : null ?>"><a href="index.php?option=<?= $module->getIdModule(); ?>&task=impot">IMPÔTS</a></li>
									<?php if ($_SESSION['user']->hasDroit('view', 'com_rapprochement')) :?>
									<li class="<?= isset($_GET['option']) && $_GET['option'] == 'com_rapprochement' ? 'active' : null ?>"><a href="index.php?option=com_rapprochement">BANK STATEMENT</a></li>
									<?php endif;?>
								</ul>
							</li>
					<?php
						}
					}
				}
				if ($_SESSION['user']->hasDroit('view', 'com_client') || $_SESSION['user']->hasDroit('view', 'com_devis') || $_SESSION['user']->hasDroit('view', 'com_facture') || $_SESSION['user']->hasDroit('view', 'com_contract') || $_SESSION['user']->hasDroit('view', 'com_cheque') || $_SESSION['user']->hasDroit('view', 'com_relance')):
					?>
					<li class="submenu <?= isset($_GET['option']) && in_array($_GET['option'], ['com_facture', 'com_cheque', 'com_relance']) ? "active" : "" ?>">
						<a href="#"><i class="fa fa-file-invoice"></i> <span> Facturation</span> <span class="menu-arrow"></span></a>
						<ul style="display: <?= isset($_GET['option']) && in_array($_GET['option'], ['com_facture', 'com_cheque', 'com_relance']) ? "block" : "none" ?>;" class="p-0">
							<?php
							$modules = module::findAllPosition("side");
							if ($_SESSION['user']->hasDroit('view', 'com_client')) {
								foreach ($modules as $module) :
									if (in_array($module->getIdModule(), ['com_client'])):
							?>
										<li class="<?= isset($_GET['option']) && $_GET['option'] == 'com_facture' && isset($_GET['task']) && $_GET['task'] == str_replace('com_', '', $module->getIdModule()) ? 'active' : null ?>"><a href="index.php?option=com_facture&task=<?= str_replace('com_', '', $module->getIdModule()) ?>"><?= $module->getNom(); ?></a></li>
									<?php
									endif;
								endforeach;
							}
							if ($_SESSION['user']->hasDroit('view', 'com_devis')) {
								foreach ($modules as $module) :
									if (in_array($module->getIdModule(), ['com_devis'])):
									?>
										<li class="<?= isset($_GET['option']) && $_GET['option'] == 'com_facture' && isset($_GET['task']) &&  $_GET['task'] == str_replace('com_', '', $module->getIdModule()) ? 'active' : null ?>"><a href="index.php?option=com_facture&task=<?= str_replace('com_', '', $module->getIdModule()) ?>"><?= $module->getNom(); ?></a></li>
									<?php
									endif;
								endforeach;
							}
							if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
								foreach ($modules as $module) :
									if (in_array($module->getIdModule(), ['com_facture'])):
									?>
										<li class="<?= isset($_GET['option']) && $_GET['option'] == 'com_facture' && isset($_GET['task']) &&  $_GET['task'] == str_replace('com_', '', $module->getIdModule()) ? 'active' : null ?>"><a href="index.php?option=com_facture&task=<?= str_replace('com_', '', $module->getIdModule()) ?>"><?= $module->getNom(); ?></a></li>
									<?php
									endif;
								endforeach;
							}
							if ($_SESSION['user']->hasDroit('view', 'com_contract')) {
								foreach ($modules as $module) :
									if (in_array($module->getIdModule(), ['com_contract'])):
									?>
										<li class="<?= isset($_GET['option']) && $_GET['option'] == 'com_facture' && isset($_GET['task']) &&  $_GET['task'] == str_replace('com_', '', $module->getIdModule()) ? 'active' : null ?>"><a href="index.php?option=com_facture&task=<?= str_replace('com_', '', $module->getIdModule()) ?>"><?= $module->getNom(); ?></a></li>
									<?php
									endif;
								endforeach;
							}
							if ($_SESSION['user']->hasDroit('view', 'com_cheque')) {
								foreach ($modules as $module) :
									if (in_array($module->getIdModule(), ['com_cheque'])):
									?>
										<li class="<?= isset($_GET['option']) && $_GET['option'] == $module->getIdModule() ? "active" : null ?>"><a href="index.php?option=<?= $module->getIdModule(); ?>"><?= $module->getNom(); ?></a></li>
									<?php
									endif;
								endforeach;
							}
							if ($_SESSION['user']->hasDroit('view', 'com_relance')) {
								foreach ($modules as $module) :
									if (in_array($module->getIdModule(), ['com_relance'])):
									?>
										<li class="<?= isset($_GET['option']) && $_GET['option'] == $module->getIdModule() ? "active" : null ?>"><a href="index.php?option=<?= $module->getIdModule(); ?>"><?= $module->getNom(); ?></a></li>
								<?php
									endif;
								endforeach;
							}
							if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
								?>
								<li class="<?= isset($_GET['option']) && $_GET['option'] == 'com_facture' && isset($_GET['task']) && $_GET['task'] == 'paiement' ? 'active' : null ?>"><a href="index.php?option=com_facture&task=paiement">Gestion des règlements</a></li>
							<?php } ?>
						</ul>
					</li>
				<?php
				endif;
				if ($_SESSION['user']->hasDroit('view', 'com_holiday') || $_SESSION['user']->hasDroit('view', 'com_rappel')) : ?>
					<li class="submenu <?= isset($_GET['option']) && in_array($_GET['option'], ['com_holiday', 'com_rappel']) ? "active" : "" ?>">
						<a href="#"><i class="fa fa-calendar-alt"></i> <span> Calendrier</span> <span class="menu-arrow"></span></a>
						<ul style="display: <?= isset($_GET['option']) && in_array($_GET['option'], ['com_holiday', 'com_rappel']) ? "block" : "none" ?>;" class="p-0">
							<?php
							$modules = module::findAllPosition("side");
							if ($_SESSION['user']->hasDroit('view', 'com_holiday')):
								foreach ($modules as $module) :
									if (in_array($module->getIdModule(), ['com_holiday'])):
							?>
										<li class="<?= isset($_GET['option']) && $_GET['option'] == 'com_holiday' ? 'active' : null ?>"><a href="index.php?option=<?= $module->getIdModule() ?>"><?= $module->getNom(); ?></a></li>
									<?php
									endif;
								endforeach;
							endif;
							if ($_SESSION['user']->hasDroit('view', 'com_rappel')):
								foreach ($modules as $module) :
									if (in_array($module->getIdModule(), ['com_rappel'])):
									?>
										<li class="<?= isset($_GET['option']) && $_GET['option'] == 'com_rappel' ? 'active' : null ?>"><a href="index.php?option=<?= $module->getIdModule() ?>"><?= $module->getNom(); ?></a></li>
							<?php
									endif;
								endforeach;
							endif ?>
						</ul>
					</li>
					<?php
				endif;
				$modules = module::findAllPosition("side");
				foreach ($modules as $module) {
					$active = isset($_GET['option']) && $_GET['option'] == $module->getIdModule() ? "active" : "";
					$arrowactive = isset($_GET['option']) && $_GET['option'] == $module->getIdModule() ? "subdrop" : "";
					$submenulistactive = isset($_GET['option']) && $_GET['option'] == $module->getIdModule() ? "block" : "none";
					if ($_SESSION['user']->hasDroit('view', $module->getIdModule())) {
						if (!in_array($module->getIdModule(), ['com_facture', 'com_devis', 'com_contract', 'com_client', 'com_accounting', 'com_resourcehumaine', 'com_cheque', 'com_relance', 'com_holiday', 'com_rappel', 'com_rapprochement'])) {
					?>

							<li class="<?= $active ?>">
								<a href="index.php?option=<?= $module->getIdModule(); ?>"><i class="fa fa-<?= $module->getIcon(); ?>"></i> <span><?= $module->getNom(); ?></span></a>
							</li>
				<?php
						}
					}
				}
				?>
			</ul>
		</div>
	</div>
</div>
<!-- /Sidebar -->