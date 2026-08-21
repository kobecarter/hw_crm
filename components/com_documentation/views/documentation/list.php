<!-- Page Wrapper -->
<div class="page-wrapper glass-page">
	<div class="content container-fluid doc-page">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Documentation</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Documentation</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="card mb-4">
			<div class="card-body">
				<div class="doc-eyebrow">HW_CRM · HELLO WORLD AGENCE DE COMMUNICATION · ÉDITION 2026-08-20</div>
				<h2 class="mb-2">Le cahier d'utilisation du CRM</h2>
				<p class="text-muted mb-3">Ce que chaque poste fait, comment s'en servir, dans quelles conditions, et où faire attention — de l'espace employé jusqu'aux automatisations comptables. Un seul document, quatre lecteurs différents.</p>
				<div class="doc-personas">
					<span class="doc-persona"><i class="fa fa-user"></i> Employé — pointage, congés, bulletins</span>
					<span class="doc-persona"><i class="fa fa-folder"></i> Admin — pilotage de l'agence</span>
					<span class="doc-persona"><i class="fa fa-dollar-sign"></i> Comptable — TVA, CNSS, rapprochement</span>
					<span class="doc-persona"><i class="fa fa-desktop"></i> Développeur — architecture, pièges</span>
				</div>
			</div>
		</div>

		<div class="row">
			<!-- Sommaire -->
			<div class="col-lg-3">
				<div class="card doc-toc">
					<div class="card-body">
						<div class="doc-toc-title">Sommaire</div>
						<nav id="docToc">
							<div class="doc-toc-group">PRISE EN MAIN</div>
							<a href="#comment-lire">Comment lire ce document</a>
							<a href="#acces-connexion">Accès &amp; connexion</a>

							<div class="doc-toc-group">01 · ESPACE EMPLOYÉ</div>
							<a href="#tableau-de-bord">Tableau de bord</a>
							<a href="#pointage">Pointage</a>
							<a href="#absences-conges">Absences &amp; congés</a>
							<a href="#bulletins-de-paie">Bulletins de paie</a>
							<a href="#demandes-rh">Demandes RH</a>
							<a href="#bonus-fichiers">Bonus, fichiers, parrainage, profil</a>

							<div class="doc-toc-group">02 · VENTE &amp; CLIENT</div>
							<a href="#clients">Clients</a>
							<a href="#devis">Devis</a>
							<a href="#services">Services</a>
							<a href="#facturation">Facturation</a>
							<a href="#contrats">Contrats</a>
							<a href="#cheques">Chèques</a>
							<a href="#reglements">Règlements</a>
							<a href="#relances">Relances</a>

							<div class="doc-toc-group">03 · FINANCE &amp; CONFORMITÉ</div>
							<a href="#charges">Charges</a>
							<a href="#fournisseurs">Fournisseurs</a>
							<a href="#comptabilite">CNSS, TVA, Bilan, Impôts</a>
							<a href="#bank-statement">Bank statement</a>

							<div class="doc-toc-group">04 · OPÉRATIONS &amp; RH</div>
							<a href="#assistant">Assistant</a>
							<a href="#rappels">Rappels</a>
							<a href="#reclamations">Réclamations</a>
							<a href="#recherche-globale">Recherche globale</a>
						</nav>
					</div>
				</div>
			</div>

			<!-- Contenu -->
			<div class="col-lg-9">

				<section id="comment-lire" class="doc-section">
					<div class="doc-kicker">PRISE EN MAIN</div>
					<h3>Comment lire ce document</h3>
					<div class="row">
						<div class="col-md-6">
							<div class="doc-callout doc-condition"><strong>CONDITION</strong> Ce qu'il faut avoir fait avant que l'action soit possible — un devis accepté, un document manquant, etc.</div>
						</div>
						<div class="col-md-6">
							<div class="doc-callout doc-attention"><strong>ATTENTION</strong> Un piège réel déjà rencontré, ou une action qui n'est pas réversible sans passer par le bon écran.</div>
						</div>
						<div class="col-md-6">
							<div class="doc-callout doc-automatise"><strong>AUTOMATISÉ</strong> Se déclenche seul (cron, synchronisation) — rien à cliquer, mais utile de savoir que ça tourne.</div>
						</div>
						<div class="col-md-6">
							<div class="doc-callout doc-mobile"><strong>MOBILE</strong> Ce qui change spécifiquement sur téléphone — mise en page, gestes, ou fonctionnalité absente.</div>
						</div>
					</div>
				</section>

				<section id="acces-connexion" class="doc-section">
					<h3>Accès &amp; connexion</h3>
					<p>Le CRM a deux portes d'entrée séparées, avec deux mots de passe différents même quand l'e-mail est identique — c'est la source d'erreur la plus fréquente pour un nouvel arrivant.</p>
					<div class="table-responsive">
						<table class="table table-bordered doc-table">
							<thead>
								<tr><th>Espace</th><th>URL</th><th>Qui</th><th>Remarque</th></tr>
							</thead>
							<tbody>
								<tr>
									<td><strong>CRM Administration</strong></td>
									<td><code>index.php</code></td>
									<td>Admin, commercial, comptable</td>
									<td>Session <code>com_users</code> — droits par module (voir/ajouter/modifier/supprimer), configurés par un administrateur dans Gestion des accès.</td>
								</tr>
								<tr>
									<td><strong>Espace Employé</strong></td>
									<td><code>index.php?option=com_elogin</code></td>
									<td>Tout collaborateur avec une fiche RH active</td>
									<td>Session <code>com_resourcehumaine</code> séparée — même si l'adresse e-mail est la même que le compte admin, le mot de passe ne l'est pas.</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="doc-callout doc-condition">
						<strong>CONDITION</strong> Un compte Espace Employé n'existe et ne se connecte que si la fiche RH est <strong>active</strong>. Un ancien collaborateur désactivé reçoit le message « le compte a été désactivé » à la connexion, même avec le bon mot de passe.
					</div>
					<div class="doc-callout doc-attention">
						<strong>ATTENTION</strong> Comptabilité (CNSS, TVA, Bilan, Impôts) est protégée par un second verrou, un code PIN, demandé une fois par session — indépendant du mot de passe de connexion.
					</div>
				</section>

				<hr class="doc-part-sep">
				<div class="doc-part-header"><span class="doc-part-badge">PARTIE 01</span> <i class="fa fa-user"></i> EMPLOYÉ</div>
				<h2 class="doc-part-title">L'espace employé</h2>
				<p class="text-muted">Neuf onglets, un seul menu horizontal — pointage, congés, bulletins, primes, demandes RH, parrainage, fichiers, et le profil. Tout ce qu'un collaborateur touche sans jamais avoir besoin d'un accès au CRM administration.</p>

				<section id="tableau-de-bord" class="doc-section">
					<h3><i class="fa fa-home doc-section-icon"></i> Tableau de bord</h3>
					<p>Première page après connexion à l'Espace Employé.</p>
					<ul>
						<li><strong>Fiche d'identité</strong> — matricule, poste, e-mail, ancienneté. Le bouton « Modifier » ne touche que la photo, le téléphone et l'adresse — le reste (poste, salaire, contrat) est géré par les RH, jamais en libre-service.</li>
						<li><strong>Jauge de congés restants</strong> — jours acquis moins jours déjà pris, mise à jour en temps réel à chaque validation RH.</li>
						<li><strong>Six tuiles KPI du mois</strong> — ancienneté, documents manquants, commission de parrainage, retard cumulé, demandes en attente, jours d'absence. Un chiffre à 0 est une bonne nouvelle ici, pas une case vide.</li>
						<li><strong>Accès rapide</strong> — raccourci direct vers chacun des 6 autres onglets, en plus du menu du haut.</li>
						<li><strong>Détail des congés par année</strong> — solde mois par mois depuis l'entrée dans l'agence, pour vérifier soi-même le calcul avant de poser une demande.</li>
					</ul>
					<div class="doc-callout doc-mobile">
						<strong>MOBILE</strong> Le menu des 9 onglets passe en rangée d'icônes seules (sans libellé) sous 768px — les tuiles KPI s'empilent en 2 colonnes au lieu de 6.
					</div>
				</section>

				<section id="pointage" class="doc-section">
					<h3><i class="fa fa-clock doc-section-icon"></i> Pointage <code class="doc-task">task=pointageweb</code></h3>
					<p class="doc-lede">Pointer son arrivée, sa pause et son départ depuis son téléphone ou son ordinateur, sans badgeuse physique — la position est vérifiée à chaque clic plutôt que l'adresse IP du bureau.</p>
					<p>Un jour normal, quatre boutons apparaissent : Arrivée, Départ pause, Retour pause, Départ.</p>
					<div class="doc-block-title">Comment ça marche</div>
					<ul>
						<li>Au clic sur un bouton, le navigateur demande la position GPS/Wi-Fi et calcule la distance réelle jusqu'au bureau (formule de Haversine) — pas besoin d'être sur le Wi-Fi de l'agence.</li>
						<li>Une bannière confirme si la position est valide avant d'enregistrer l'heure.</li>
						<li>Un ancien contrôle par adresse IP fixe reste actif en repli si l'agence en a configuré un, mais n'est plus obligatoire.</li>
					</ul>
					<div class="doc-callout doc-condition">
						<strong>CONDITION</strong> Le pointage est désactivé les jours fériés et pendant un congé validé — le bouton n'apparaît pas, remplacé par un message.
					</div>
					<div class="doc-callout doc-attention">
						<strong>ATTENTION</strong> Un retard de plus de 2 heures — ou une journée entière sans aucun pointage — n'est jamais compté comme un simple retard : le système le classe automatiquement en <strong>absence non justifiée</strong>, visible par l'administrateur, qui peut la requalifier depuis la fiche RH.
					</div>
				</section>

				<section id="absences-conges" class="doc-section">
					<h3><i class="fa fa-umbrella-beach doc-section-icon"></i> Absences &amp; congés <code class="doc-task">task=absences</code></h3>
					<p class="doc-lede">L'historique complet des congés et absences, avec leur statut — posé ici ou constaté par le système (retard, absence non justifiée), tout finit dans cette même liste.</p>
					<div class="doc-block-title">À savoir</div>
					<ul>
						<li>Une demande de congé se pose depuis l'onglet <strong>Demandes</strong> (type « Congé »), pas directement ici — cette page est un historique, en lecture seule côté employé.</li>
						<li>Une absence détectée automatiquement (retard &gt;2h, jour sans pointage) apparaît ici sous le statut « Non justifié » dès sa création par le système.</li>
					</ul>
					<div class="doc-callout doc-automatise">
						<strong>AUTOMATISÉ</strong> La détection d'absence tourne une fois par jour via une tâche planifiée externe — elle ne dépend d'aucune action de l'employé ni de l'administrateur.
					</div>
				</section>

				<section id="bulletins-de-paie" class="doc-section">
					<h3><i class="fa fa-file-invoice-dollar doc-section-icon"></i> Bulletins de paie <code class="doc-task">task=payslips</code></h3>
					<p class="doc-lede">Consulter et télécharger chaque bulletin de paie depuis l'entrée dans l'agence, en PDF.</p>
					<div class="doc-block-title">Comment ça marche</div>
					<ul>
						<li>Chaque carte correspond à un mois — icônes « œil » (aperçu) et « télécharger » (PDF) sous chaque date.</li>
						<li>Le bandeau orange en haut liste tous les mois <em>sans</em> bulletin déposé depuis la date de signature du contrat — pas seulement depuis l'année en cours.</li>
					</ul>
					<div class="doc-callout doc-condition">
						<strong>CONDITION</strong> Un bulletin n'apparaît ici qu'une fois déposé côté administrateur — l'employé ne peut pas en générer un lui-même.
					</div>
				</section>

				<section id="demandes-rh" class="doc-section">
					<h3><i class="fa fa-comment-dots doc-section-icon"></i> Demandes RH <code class="doc-task">task=requests</code></h3>
					<p class="doc-lede">Le canal officiel pour toute demande adressée aux ressources humaines — congé, attestation, question administrative — avec suivi de statut, plutôt qu'un message Slack ou un e-mail qui se perd.</p>
					<div class="doc-block-title">Comment ça marche</div>
					<ol>
						<li>Choisir un type (Congé ou autre), donner un titre court et une description.</li>
						<li>Envoyer — la demande apparaît immédiatement dans l'historique avec un statut « en attente ».</li>
						<li>L'administrateur traite la demande depuis le CRM ; le statut se met à jour ici (Acceptée / Refusée) sans notification push — à revérifier soi-même.</li>
					</ol>
					<div class="doc-callout doc-mobile">
						<strong>MOBILE</strong> Formulaire et historique s'empilent verticalement sans perte de fonctionnalité — seule la mise en page qui change.
					</div>
				</section>

				<section id="bonus-fichiers" class="doc-section">
					<h3>Bonus, fichiers, parrainage &amp; profil</h3>
					<p class="doc-lede">Les quatre onglets restants, plus courts, groupés ici pour ne pas répéter la même mise en page quatre fois.</p>

					<div class="doc-subblock">
						<div class="doc-subblock-title"><i class="fa fa-gift"></i> Bonus <code class="doc-task">task=bonuses</code></div>
						<p>Historique des primes accordées, avec leur montant total cumulé. Alimenté uniquement côté administrateur — aucune action possible ici pour l'employé au-delà de la consultation.</p>
					</div>

					<div class="doc-subblock">
						<div class="doc-subblock-title"><i class="fa fa-folder-open"></i> Fichiers <code class="doc-task">task=files</code></div>
						<p>Dossier personnel — CIN, contrat, et les documents propres au statut (Stagiaire, Titulaire, Période de test). Chaque document manquant obligatoire est listé nommément en haut, avec un bouton « + » pour l'ajouter directement.</p>
						<div class="doc-callout doc-condition">
							<strong>CONDITION</strong> Tant que les documents obligatoires du statut ne sont pas tous fournis, la demande de congé reste bloquée depuis l'onglet Demandes.
						</div>
					</div>

					<div class="doc-subblock">
						<div class="doc-subblock-title"><i class="fa fa-handshake doc-section-icon"></i> Parrainage client <code class="doc-task">task=parrainage</code></div>
						<p>Recommander un prospect à l'agence (nom, entreprise, e-mail) en échange d'une commission, versée une fois le client validé par l'administration — jamais automatique à l'envoi du formulaire.</p>
					</div>

					<div class="doc-subblock">
						<div class="doc-subblock-title"><i class="fa fa-user-edit"></i> Modifier mon profil <code class="doc-task">task=myProfileEdit</code></div>
						<p>Photo de profil, téléphone(s) et adresse — c'est tout. Le reste de la fiche (poste, salaire, statut, contrat) reste sous contrôle exclusif des ressources humaines.</p>
					</div>
				</section>

				<hr class="doc-part-sep">
				<div class="doc-part-header"><span class="doc-part-badge">PARTIE 02</span> <i class="fa fa-folder"></i> ADMIN</div>
				<h2 class="doc-part-title">Vente &amp; relation client</h2>
				<p class="text-muted">Le parcours complet d'une affaire — du premier contact jusqu'au paiement encaissé — tient sur huit postes reliés entre eux : un devis mène à un contrat, qui mène à une facture, qui mène à un paiement.</p>

				<section id="clients" class="doc-section">
					<h3><i class="fa fa-users doc-section-icon"></i> Clients <code class="doc-task">com_client</code></h3>
					<p class="doc-lede">La fiche de référence — tout ce qui concerne un client, croisé automatiquement avec ses devis, factures, paiements et réclamations.</p>
					<div class="doc-block-title">Comment l'utiliser</div>
					<ul>
						<li>La fiche centralise coordonnées, ICE/RC, ville et agence, plus l'historique croisé (devis, factures, paiements, réclamations) sans changer de page.</li>
						<li>Les identifiants réseaux sociaux du client sont chiffrés (AES-256) et accessibles via un lien temporaire de 24h, plutôt que stockés en clair dans un champ texte.</li>
						<li>Un résumé de collaboration généré par IA est disponible à la demande depuis la fiche.</li>
						<li>La recherche globale (voir plus bas) bascule automatiquement de session d'agence si le client trouvé appartient à une autre agence — pas besoin de changer d'agence à la main avant de cliquer.</li>
					</ul>
					<div class="doc-callout doc-attention">
						<strong>ATTENTION</strong> Un rappel de re-vérification des identifiants sociaux revient tous les 90 jours et alimente le centre d'alertes — ce n'est pas une notification ponctuelle qu'on peut ignorer une fois.
					</div>
				</section>

				<section id="devis" class="doc-section">
					<h3><i class="fa fa-file-signature doc-section-icon"></i> Devis <code class="doc-task">com_devis</code></h3>
					<p class="doc-lede">Créer un devis à partir d'une conversation IA ou d'un document déposé, puis le faire signer et basculer en facture.</p>
					<div class="doc-block-title">Comment l'utiliser</div>
					<ol>
						<li>Démarrer un devis via le chat IA (décrire le besoin) ou en déposant un document — client et prestations sont extraits automatiquement.</li>
						<li>Si deux services quasi identiques sont détectés, une fusion est proposée (quantités additionnées) plutôt que deux lignes dupliquées.</li>
						<li>Générer le PDF, l'envoyer par e-mail ; à l'acceptation, le devis peut être converti en facture depuis la même page.</li>
					</ol>
					<div class="doc-callout doc-condition">
						<strong>CONDITION</strong> Sélectionner un compte bancaire personnel (remboursement de frais) sur le devis coche et <strong>verrouille</strong> automatiquement la case Proforma — impossible à décocher tant que ce compte reste sélectionné.
					</div>
				</section>

				<section id="services" class="doc-section">
					<h3><i class="fa fa-tools doc-section-icon"></i> Services <code class="doc-task">com_service</code></h3>
					<p class="doc-lede">Le catalogue de prestations vendables, groupé par catégorie, avec indicateurs d'usage réel.</p>
					<div class="doc-block-title">Comment l'utiliser</div>
					<ul>
						<li>Chaque carte affiche le nombre de devis l'ayant utilisé et le chiffre d'affaires associé — pour repérer les prestations qui vendent vraiment.</li>
						<li>Le champ « intervenants » propose une sélection multiple avec suggestions basées sur l'historique.</li>
						<li>La description peut être réécrite par IA, ou générée automatiquement à partir d'un scan du site du client (utile pour un devis de refonte).</li>
					</ul>
				</section>

				<section id="facturation" class="doc-section">
					<h3><i class="fa fa-file-invoice doc-section-icon"></i> Facturation <code class="doc-task">com_facture</code></h3>
					<p class="doc-lede">La page centrale à onglets — Clients / Devis / Contrats / Factures / Paiements — pour ne jamais perdre le contexte en passant de l'un à l'autre.</p>
					<div class="doc-block-title">Comment l'utiliser</div>
					<ul>
						<li>Menu latéral — accès à tous les postes du CRM, toujours visible. Onglets Clients / Devis / Contrats / Factures / Paiements — cliquer bascule le tableau en dessous sans recharger la page.</li>
						<li>PDF et envoi par e-mail directement depuis la liste des factures ; le statut de paiement se met à jour au fil des règlements enregistrés.</li>
						<li>À la création d'une facture, le CRM vérifie automatiquement le dossier Drive du client (doublon), crée une carte Trello, et notifie le support par Slack et e-mail.</li>
						<li>Le récapitulatif Trello détaille désormais la <strong>durée</strong> de chaque prestation vendue, pas seulement son titre.</li>
					</ul>
				</section>

				<section id="contrats" class="doc-section">
					<h3><i class="fa fa-file-contract doc-section-icon"></i> Contrats <code class="doc-task">com_contract</code></h3>
					<p class="doc-lede">L'engagement écrit qui accompagne les prestations récurrentes — génération automatique, édition, export PDF/Word, signature en ligne. Détail complet en Partie 06 · Focus Contrats.</p>
					<p>Accessible directement depuis l'onglet Facturation → Contrats, ou depuis la fiche devis une fois le contrat généré. Utilisé comme référence en cas de renouvellement ou de litige.</p>
					<div class="doc-callout doc-mobile">
						<strong>MOBILE</strong> Les quatre pastilles de statut du parcours devis→contrat→facture→paiement sont désormais correctement centrées et espacées sur petit écran — elles se chevauchaient avant correction.
					</div>
				</section>

				<section id="cheques" class="doc-section">
					<h3><i class="fa fa-money-check doc-section-icon"></i> Chèques <code class="doc-task">com_cheque</code></h3>
					<p class="doc-lede">Le suivi des règlements par chèque — en attente, encaissé, ou rejeté.</p>
					<p>Accessible depuis le menu Facturation. Utile pour relancer un client dont le chèque traîne, ou pour justifier un rejet auprès de la comptabilité.</p>
				</section>

				<section id="reglements" class="doc-section">
					<h3><i class="fa fa-cash-register doc-section-icon"></i> Règlements <code class="doc-task">com_facture · task=paiement</code></h3>
					<p class="doc-lede">Un registre de paiements indépendant de la fiche facture — pour un rapprochement rapide en fin de journée.</p>
					<p>Enregistrer un paiement ici recalcule aussitôt le statut de la facture concernée (impayée → partiellement payée → payée) sans avoir à rouvrir la facture elle-même.</p>
				</section>

				<section id="relances" class="doc-section">
					<h3><i class="fa fa-bell doc-section-icon"></i> Relances <code class="doc-task">com_relance</code></h3>
					<p class="doc-lede">Les rappels de paiement automatiques envoyés par e-mail selon l'échéancier de chaque facture.</p>
					<p>Historique complet, par facture ou par client.</p>
					<div class="doc-callout doc-automatise">
						<strong>AUTOMATISÉ</strong> Envoyées par une tâche planifiée quotidienne, selon l'échéancier propre à chaque facture — aucune action manuelle n'est nécessaire pour qu'une relance parte.
					</div>
				</section>

				<hr class="doc-part-sep">
				<div class="doc-part-header"><span class="doc-part-badge">PARTIE 03</span> <i class="fa fa-folder"></i> ADMIN <i class="fa fa-dollar-sign ml-2"></i> COMPTABLE</div>
				<h2 class="doc-part-title">Finance &amp; conformité</h2>
				<p class="text-muted">Le module le plus surveillé, et celui qui a reçu le plus d'automatisation : charges, fournisseurs, cinq déclarations fiscales marocaines, et le rapprochement bancaire.</p>

				<section id="charges" class="doc-section">
					<h3><i class="fa fa-receipt doc-section-icon"></i> Charges <code class="doc-task">com_charge</code></h3>
					<p class="doc-lede">Toute dépense de l'agence — fixe, variable, ou hors Hello World — avec liaison automatique aux rappels d'échéance.</p>
					<div class="doc-block-title">Comment l'utiliser</div>
					<ul>
						<li>Trois natures possibles : fixe, variable, ou hors Hello World.</li>
						<li>Lier une charge à un service (domaine, hébergement, SSL) et à un client met à jour — ou crée — automatiquement le Rappel d'échéance correspondant, +365 jours à partir du règlement.</li>
						<li>Une même charge peut être répartie sur plusieurs fournisseurs.</li>
						<li>Import assisté d'un bulletin de paie par glisser-déposer, avec suggestion du nom de l'employé.</li>
					</ul>
					<div class="doc-callout doc-automatise">
						<strong>AUTOMATISÉ</strong> La synchronisation avec Rappels se fait sans jamais visiter la page Rappels — le lien se fait entièrement depuis la fiche charge.
					</div>
				</section>

				<section id="fournisseurs" class="doc-section">
					<h3><i class="fa fa-truck doc-section-icon"></i> Fournisseurs <code class="doc-task">com_fournisseur</code></h3>
					<p class="doc-lede">Le répertoire des prestataires de l'agence, groupé par catégorie.</p>
					<p>Vue en cartes ou en liste, au choix — hébergement, impression, freelances, assurance... Import assisté à partir d'un document existant plutôt qu'une saisie manuelle fournisseur par fournisseur — utile pour démarrer une nouvelle catégorie d'un coup.</p>
				</section>

				<section id="comptabilite" class="doc-section">
					<h3><i class="fa fa-chart-bar doc-section-icon"></i> Comptabilité — CNSS, TVA, Bilan, Taxe Pro, Impôts <code class="doc-task">com_accounting</code></h3>
					<p class="doc-lede">Cinq déclarations fiscales marocaines, pensées pour ne plus jamais dépendre du dossier Drive du comptable.</p>
					<div class="doc-block-title">Comment l'utiliser</div>
					<ul>
						<li>Une bannière signale tout mois ou toute année sans déclaration depuis la création de l'agence, sur les 5 pages — cliquable, pré-rempli.</li>
						<li>L'estimation TVA en temps réel (collectée/déductible) se calcule depuis les paiements et charges déjà dans le CRM, à comparer à la déclaration officielle du mois.</li>
						<li>Un dossier comptable complet (Excel multi-onglets + PDF des factures/reçus réellement comptés sur la période) se télécharge en un clic, en ZIP.</li>
						<li>Déposer un justificatif de paiement crée automatiquement la charge fixe correspondante.</li>
						<li>Chaque déclaration TVA indique quel(s) relevé(s) bancaire(s) couvrent son mois, pour la traçabilité.</li>
					</ul>
					<div class="doc-callout doc-attention">
						<strong>ATTENTION</strong> Un widget flottant d'échéance TVA s'affiche sur toutes les pages à l'approche de la date limite — son alerte visuelle s'intensifie plus la date se rapproche.
					</div>
				</section>

				<section id="bank-statement" class="doc-section">
					<h3><i class="fa fa-university doc-section-icon"></i> Bank statement — rapprochement bancaire <code class="doc-task">com_rapprochement</code></h3>
					<p class="doc-lede">Déposer un relevé bancaire et laisser le CRM proposer un rapprochement — sans jamais rien écrire en base sans une confirmation explicite.</p>
					<div class="doc-block-title">Comment l'utiliser</div>
					<ul>
						<li>Déposer le relevé (CSV ou PDF) — le compte concerné est identifié automatiquement (RIB/IBAN/nom de banque lus dans le document), pas besoin de le sélectionner à la main.</li>
						<li>Un aperçu s'affiche <strong>avant</strong> toute écriture : compte, période, statut ligne par ligne.</li>
						<li>Les crédits sont rapprochés aux factures par montant + nom, les débits fournisseurs récurrents sont reconnus, les commissions bancaires sont regroupées en une seule charge.</li>
						<li>Un paiement de TVA est détecté par mot-clé (y compris « paiement taxes en ligne », le libellé du portail marocain SIMPL) — mais demande toujours une confirmation manuelle de la déclaration exacte concernée.</li>
					</ul>
					<div class="doc-callout doc-attention">
						<strong>ATTENTION</strong> Rien n'est jamais rapproché automatiquement sans un clic de confirmation — le module suggère, il ne décide jamais à la place de l'agence. Un relevé (ou des lignes) déjà importé bloque toute nouvelle validation avec un message explicite — il faut supprimer l'ancien import avant de réimporter.
					</div>
				</section>

				<hr class="doc-part-sep">
				<div class="doc-part-header"><span class="doc-part-badge">PARTIE 04</span> <i class="fa fa-folder"></i> ADMIN</div>
				<h2 class="doc-part-title">Opérations &amp; ressources humaines</h2>
				<p class="text-muted">Ce qui ne doit jamais glisser entre les mailles — tâches d'équipe, échéances techniques, réclamations, recherche, et tout le cycle RH depuis l'offre d'emploi jusqu'au bulletin de paie.</p>

				<section id="assistant" class="doc-section">
					<h3><i class="fa fa-tasks doc-section-icon"></i> Assistant <code class="doc-task">com_assistant</code></h3>
					<p class="doc-lede">Le carnet de tâches, rendez-vous et suivis-clients de l'équipe.</p>
					<p>Séparé volontairement de Rappels (échéances techniques) pour ne jamais mélanger les deux logiques. Ce module gère les tâches humaines (RDV, relance commerciale, suivi client) — les échéances techniques automatisées (domaine, hébergement, SSL) vivent dans <strong>Rappels</strong>, un module distinct exprès.</p>
				</section>

				<section id="rappels" class="doc-section">
					<h3><i class="fa fa-history doc-section-icon"></i> Rappels <code class="doc-task">com_rappel</code></h3>
					<p class="doc-lede">Les échéances domaine, hébergement et SSL — synchronisées automatiquement depuis Charges.</p>
					<p>Historique d'envoi groupé par client.</p>
					<div class="doc-callout doc-automatise">
						<strong>AUTOMATISÉ</strong> Renouveler la charge liée met à jour l'échéance (+365 jours) sans visiter cette page. Des e-mails de rappel partent automatiquement à J-30, J-20, J-5 et le jour même, une seule fois par palier.
					</div>
				</section>

				<section id="reclamations" class="doc-section">
					<h3><i class="fa fa-exclamation-triangle doc-section-icon"></i> Réclamations <code class="doc-task">com_reclamation</code></h3>
					<p class="doc-lede">Le suivi des plaintes clients, rattaché à la fiche client.</p>
					<p>Une réclamation ouverte continue d'apparaître dans le centre d'alertes urgentes en haut du CRM jusqu'à sa clôture explicite — elle ne disparaît pas seule après un délai.</p>
				</section>

				<section id="recherche-globale" class="doc-section">
					<h3><i class="fa fa-search doc-section-icon"></i> Recherche globale <code class="doc-task">com_search</code></h3>
					<p class="doc-lede">La barre en haut de chaque page — cherche simultanément clients, factures, devis, et bascule automatiquement de session d'agence si le résultat trouvé appartient à une autre agence.</p>
					<div class="doc-callout doc-attention">
						<strong>À COMPLÉTER</strong> La suite de cette section, ainsi que « Personnel &amp; pointage (admin) », « Recrutement », la Partie 05 · Pilotage &amp; Admin, la Partie 06 · Focus Contrats, la Partie 07 · Journal Pointage et la Partie 08 · Notes développeur seront ajoutées à la prochaine mise à jour de ce document.
					</div>
				</section>

			</div>
		</div>

	</div>
</div>
<!-- /Page Wrapper -->

<style>
.doc-page{font-family:'Raleway',sans-serif;}
.doc-eyebrow{font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#8b6a22;font-weight:600;margin-bottom:8px;}
.doc-personas{display:flex;flex-wrap:wrap;gap:10px;margin-top:12px;}
.doc-persona{display:inline-flex;align-items:center;gap:6px;font-size:13px;background:#f7f5f2;border:1px solid rgba(0,0,0,.09);padding:6px 12px;color:#4b4640;}
.doc-persona i{color:#8b6a22;}
.doc-toc{position:sticky;top:20px;max-height:calc(100vh - 40px);overflow-y:auto;}
.doc-toc-title{font-weight:700;font-size:13px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;}
.doc-toc-group{font-size:11px;font-weight:700;color:#8b6a22;text-transform:uppercase;letter-spacing:.05em;margin:14px 0 4px;}
.doc-toc-group:first-child{margin-top:0;}
#docToc a{display:block;font-size:13px;color:#4b4640;padding:3px 0 3px 8px;border-left:2px solid transparent;text-decoration:none;}
#docToc a:hover,#docToc a.active{color:#8b6a22;border-left-color:#8b6a22;}
.doc-section{scroll-margin-top:20px;margin-bottom:30px;padding-bottom:6px;}
.doc-section h3{font-weight:700;margin-bottom:10px;}
.doc-section-icon{color:#8b6a22;margin-right:6px;}
.doc-lede{font-style:italic;color:#6b6460;}
.doc-block-title{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6b6460;margin:14px 0 6px;}
.doc-task{background:#edeae5;border-radius:0;padding:2px 8px;font-size:12px;color:#6b6460;margin-left:8px;font-weight:400;}
.doc-callout{border-left:4px solid;padding:12px 16px;margin:12px 0;font-size:14px;}
.doc-callout strong{display:inline-block;margin-right:6px;font-size:12px;letter-spacing:.03em;}
.doc-condition{background:#fdf6e6;border-left-color:#c99a2e;}
.doc-attention{background:#fdeceb;border-left-color:#d9534f;}
.doc-automatise{background:#e9f7ef;border-left-color:#2e9e5b;}
.doc-mobile{background:#e8f6f8;border-left-color:#2a9fb0;}
.doc-table{font-size:14px;}
.doc-table code{background:#edeae5;padding:2px 6px;}
.doc-part-sep{margin:36px 0 18px;border-color:rgba(0,0,0,.09);}
.doc-part-header{font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#8b6a22;margin-bottom:6px;}
.doc-part-badge{background:#8b6a22;color:#fff;padding:2px 8px;margin-right:6px;}
.doc-part-title{font-weight:700;margin-bottom:6px;}
.doc-subblock{margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid rgba(0,0,0,.06);}
.doc-subblock:last-child{border-bottom:none;}
.doc-subblock-title{font-weight:700;margin-bottom:4px;}
.doc-subblock-title i{color:#8b6a22;margin-right:6px;}
@media (max-width:991px){.doc-toc{position:static;max-height:none;margin-bottom:20px;}}
</style>

<script>
window.addEventListener('load', function () {
	var links = document.querySelectorAll('#docToc a');
	var sections = document.querySelectorAll('.doc-section');
	links.forEach(function (link) {
		link.addEventListener('click', function (e) {
			e.preventDefault();
			var target = document.querySelector(link.getAttribute('href'));
			if (target) {
				window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY - 20, behavior: 'smooth' });
			}
		});
	});
	function onScroll() {
		var pos = window.scrollY + 100;
		var current = null;
		sections.forEach(function (sec) {
			if (sec.offsetTop <= pos) current = sec;
		});
		links.forEach(function (l) { l.classList.remove('active'); });
		if (current) {
			var active = document.querySelector('#docToc a[href="#' + current.id + '"]');
			if (active) active.classList.add('active');
		}
	}
	window.addEventListener('scroll', onScroll);
	onScroll();
});
</script>
