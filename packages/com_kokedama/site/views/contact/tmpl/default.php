<?php defined('_JEXEC') or die; ?>
<div class="kokedama-contact py-5">
	<div class="container">
		<div class="text-center mb-5">
			<h1 class="display-4 fw-light">Contatti</h1>
			<p class="lead text-muted">Siamo qui per te. Scrivici o vienici a trovare in atelier.</p>
		</div>
		<div class="row g-5">
			<div class="col-lg-5">
				<div class="card border-0 shadow-sm h-100">
					<div class="card-body p-4">
						<h4 class="mb-4">Kokedama & Sculture Naturali</h4>
						<ul class="list-unstyled">
							<li class="mb-3"><i class="fas fa-map-marker-alt text-kokedama me-2"></i><?php echo nl2br($this->escape($this->params->get('business_address', 'Piazza della Repubblica 11'))); ?><br><?php echo $this->escape($this->params->get('business_zip', '44121')); ?> <?php echo $this->escape($this->params->get('business_city', 'Ferrara')); ?></li>
							<li class="mb-3"><i class="fas fa-phone text-kokedama me-2"></i><a href="tel:<?php echo preg_replace('/\s+/', '', $this->params->get('business_phone', '0532242503')); ?>"><?php echo $this->escape($this->params->get('business_phone', '0532 242503')); ?></a></li>
							<li class="mb-3"><i class="fas fa-envelope text-kokedama me-2"></i><a href="mailto:<?php echo $this->escape($this->params->get('business_email', 'anda22@gmail.com')); ?>"><?php echo $this->escape($this->params->get('business_email', 'anda22@gmail.com')); ?></a></li>
						</ul>
						<hr>
						<div class="d-flex gap-3">
							<?php if ($this->params->get('business_facebook')) : ?>
								<a href="<?php echo $this->escape($this->params->get('business_facebook')); ?>" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="fab fa-facebook-f"></i></a>
							<?php endif; ?>
							<?php if ($this->params->get('business_instagram')) : ?>
								<a href="<?php echo $this->escape($this->params->get('business_instagram')); ?>" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="fab fa-instagram"></i></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-7">
				<div class="card border-0 shadow-sm">
					<div class="card-body p-4">
						<h4 class="mb-4">Scrivici</h4>
						<form action="index.php?option=com_kokedama&view=contact" method="post" id="contactForm" class="needs-validation" novalidate>
							<?php echo JHtml::_('form.token'); ?>
							<input type="hidden" name="task" value="contact.save">
							<div class="row g-3 mb-3">
								<div class="col-md-6">
									<label for="c_name" class="form-label">Nome *</label>
									<input type="text" name="jform[name]" id="c_name" class="form-control" required>
								</div>
								<div class="col-md-6">
									<label for="c_email" class="form-label">Email *</label>
									<input type="email" name="jform[email]" id="c_email" class="form-control" required>
								</div>
							</div>
							<div class="mb-3">
								<label for="c_phone" class="form-label">Telefono</label>
								<input type="tel" name="jform[phone]" id="c_phone" class="form-control">
							</div>
							<div class="mb-3">
								<label for="c_subject" class="form-label">Oggetto</label>
								<input type="text" name="jform[subject]" id="c_subject" class="form-control">
							</div>
							<div class="mb-3">
								<label for="c_message" class="form-label">Messaggio *</label>
								<textarea name="jform[message]" id="c_message" class="form-control" rows="5" required></textarea>
							</div>
							<div class="mb-3 form-check">
								<input type="checkbox" name="jform[consent_gdpr]" id="c_consent" class="form-check-input" value="1" required>
								<label class="form-check-label small" for="c_consent"><?php echo $this->params->get('gdpr_consent_text', 'Ho letto e accetto l\'informativa sulla privacy.'); ?> *</label>
							</div>
							<button type="submit" class="btn btn-kokedama"><i class="fas fa-paper-plane me-2"></i>Invia messaggio</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php if ($this->params->get('business_maps_embed')) : ?>
			<div class="mt-5 rounded overflow-hidden shadow-sm">
				<?php echo $this->params->get('business_maps_embed'); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
