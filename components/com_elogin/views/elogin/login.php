<!-- Espace Employé — page de connexion dédiée (verre + 3D). Style/scripts scopés à cette
     seule vue (top-login.php/bottom-login.php restent partagés avec com_login, donc
     inchangés, pour ne jamais affecter le login CRM normal). -->
<style>
	.elogin-bg {
		position: fixed;
		inset: 0;
		z-index: 0;
		overflow: hidden;
		background: #0b0c1a;
	}
	.elogin-bg span {
		position: absolute;
		border-radius: 50%;
		filter: blur(70px);
		opacity: 0.55;
	}
	.elogin-blob-1 { width: 50vw; height: 50vw; top: -16vw; left: -12vw; background: radial-gradient(circle, rgba(99,102,241,.6), transparent 70%); animation: elBlob1 22s ease-in-out infinite; }
	.elogin-blob-2 { width: 42vw; height: 42vw; bottom: -16vw; right: -10vw; background: radial-gradient(circle, rgba(20,184,166,.5), transparent 70%); animation: elBlob2 26s ease-in-out infinite; }
	.elogin-blob-3 { width: 36vw; height: 36vw; top: 30vh; right: 12vw; background: radial-gradient(circle, rgba(236,72,153,.4), transparent 70%); animation: elBlob3 30s ease-in-out infinite; }
	@keyframes elBlob1 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(6vw,8vh) scale(1.15); } }
	@keyframes elBlob2 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(-6vw,-6vh) scale(1.1); } }
	@keyframes elBlob3 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(-4vw,6vh) scale(1.08); } }

	.elogin-wrapper {
		position: relative;
		z-index: 1;
		min-height: 100vh;
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 24px;
	}
	.elogin-card {
		position: relative;
		width: 100%;
		max-width: 400px;
		background: rgba(255, 255, 255, 0.08);
		backdrop-filter: blur(26px) saturate(180%);
		-webkit-backdrop-filter: blur(26px) saturate(180%);
		border: 1px solid rgba(255, 255, 255, 0.18);
		border-radius: 26px;
		box-shadow: 0 30px 70px rgba(0, 0, 0, 0.45);
		padding: 40px 34px;
		transform-style: preserve-3d;
		transition: transform 0.4s ease;
	}
	.elogin-logo {
		display: block;
		height: 44px;
		width: auto;
		margin: 0 auto 18px;
		border-radius: 10px;
	}
	.elogin-title {
		text-align: center;
		color: #fff;
		font-weight: 800;
		font-size: 1.4rem;
		letter-spacing: -0.01em;
		margin: 0 0 4px;
	}
	.elogin-subtitle {
		text-align: center;
		color: rgba(255, 255, 255, 0.6);
		font-size: 0.85rem;
		margin: 0 0 28px;
	}
	.elogin-field {
		margin-bottom: 18px;
	}
	.elogin-label {
		display: block;
		color: rgba(255, 255, 255, 0.75);
		font-size: 0.78rem;
		font-weight: 700;
		margin-bottom: 6px;
		text-transform: uppercase;
		letter-spacing: 0.04em;
	}
	.elogin-input-wrap {
		position: relative;
	}
	.elogin-input-wrap i {
		position: absolute;
		left: 14px;
		top: 50%;
		transform: translateY(-50%);
		color: rgba(255, 255, 255, 0.4);
	}
	.elogin-input {
		width: 100%;
		padding: 13px 14px 13px 40px;
		border-radius: 14px;
		border: 1px solid rgba(255, 255, 255, 0.16);
		background: rgba(255, 255, 255, 0.07);
		color: #fff;
		font-size: 0.92rem;
	}
	.elogin-input::placeholder {
		color: rgba(255, 255, 255, 0.35);
	}
	.elogin-input:focus {
		outline: none;
		border-color: #8b8ff5;
		background: rgba(255, 255, 255, 0.12);
		box-shadow: 0 0 0 3px rgba(139, 143, 245, 0.25);
	}
	.elogin-remember {
		display: flex;
		align-items: center;
		gap: 8px;
		color: rgba(255, 255, 255, 0.6);
		font-size: 0.82rem;
		margin-bottom: 22px;
	}
	.elogin-submit {
		width: 100%;
		padding: 14px;
		border: none;
		border-radius: 999px;
		background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
		color: #fff;
		font-weight: 700;
		font-size: 0.95rem;
		cursor: pointer;
		box-shadow: 0 14px 34px rgba(99, 102, 241, 0.45);
		transition: transform 0.2s ease, box-shadow 0.2s ease;
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 8px;
	}
	.elogin-submit:hover {
		transform: translateY(-2px);
		box-shadow: 0 18px 40px rgba(99, 102, 241, 0.55);
	}
	.elogin-submit .loading {
		display: none;
	}
	.elogin-msgbox:empty { display: none; }
	.elogin-msgbox { margin-bottom: 16px; }
	.elogin-footer {
		text-align: center;
		color: rgba(255, 255, 255, 0.35);
		font-size: 0.72rem;
		margin-top: 26px;
	}
</style>

<div class="elogin-bg" aria-hidden="true">
	<span class="elogin-blob-1"></span>
	<span class="elogin-blob-2"></span>
	<span class="elogin-blob-3"></span>
</div>

<div class="elogin-wrapper">
	<div class="elogin-card" id="eloginCard">
		<img class="elogin-logo" src="images/config/<?php echo $config->getLogo(); ?>" alt="<?php echo $config->getNom(); ?>">
		<h1 class="elogin-title">Espace Employé</h1>
		<p class="elogin-subtitle">Connectez-vous pour accéder à votre espace personnel</p>

		<!-- Action relative - même correctif que components/com_login/views/login/login.php (le
		     même bug de connexion mobile touchait aussi cette page). -->
		<form method="post" id="eloginForm" action="components/com_elogin/controleurs/router.php?task=login">
			<div class="elogin-msgbox msgbox"></div>

			<div class="elogin-field">
				<label class="elogin-label">E-mail</label>
				<div class="elogin-input-wrap">
					<i class="fa fa-envelope"></i>
					<input type="text" name="email" class="elogin-input" placeholder="vous@helloworld-agency.com">
				</div>
			</div>

			<div class="elogin-field">
				<label class="elogin-label">Mot de passe</label>
				<div class="elogin-input-wrap">
					<i class="fa fa-lock"></i>
					<input type="password" name="password" class="elogin-input pass-input" placeholder="••••••••">
				</div>
			</div>

			<label class="elogin-remember">
				<input type="checkbox" id="cb1"> Se rappeler de moi
			</label>

			<button class="elogin-submit" type="submit">
				<span class="spinner-border spinner-border-sm loading" role="status"></span>
				<span>Se connecter</span>
				<i class="fa fa-arrow-right"></i>
			</button>
		</form>

		<p class="elogin-footer">Espace réservé aux collaborateurs HelloWorld</p>
	</div>
</div>

<script>
	(function () {
		var card = document.getElementById('eloginCard');
		if (card) {
			card.addEventListener('mousemove', function (e) {
				var rect = card.getBoundingClientRect();
				var px = (e.clientX - rect.left) / rect.width - 0.5;
				var py = (e.clientY - rect.top) / rect.height - 0.5;
				card.style.transform = 'perspective(1000px) rotateY(' + (px * 6) + 'deg) rotateX(' + (py * -6) + 'deg)';
			});
			card.addEventListener('mouseleave', function () {
				card.style.transform = 'perspective(1000px) rotateY(0) rotateX(0)';
			});
		}
		if (typeof gsap !== 'undefined') {
			gsap.from('.elogin-card', { y: 30, opacity: 0, scale: 0.96, duration: 0.7, ease: 'power2.out' });
			gsap.from('.elogin-field, .elogin-remember, .elogin-submit', {
				y: 16, opacity: 0, duration: 0.5, delay: 0.2,
				stagger: { amount: 0.35, from: 'start' }, ease: 'power2.out'
			});
		}
	})();
</script>
