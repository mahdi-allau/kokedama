<?php defined('_JEXEC') or die; use Joomla\CMS\Router\Route; ?>
<div class="kokedama-booking py-5">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-8">
				<div class="text-center mb-5">
					<h1 class="display-4 fw-light">Prenota</h1>
					<p class="lead text-muted">Prenota un servizio o un workshop. Ti confermeremo la disponibilità entro 24 ore.</p>
				</div>
				<div class="card border-0 shadow-sm">
					<div class="card-body p-4 p-md-5">
						<form action="<?php echo Route::_('index.php?option=com_kokedama&view=booking'); ?>" method="post" id="bookingForm" class="needs-validation" novalidate>
							<?php echo JHtml::_('form.token'); ?>
							<input type="hidden" name="task" value="booking.save">
							<input type="hidden" name="option" value="com_kokedama">
							<input type="hidden" name="view" value="booking">

							<div class="mb-4">
								<label class="form-label fw-bold">Cosa vuoi prenotare?</label>
								<div class="btn-group w-100" role="group">
									<input type="radio" class="btn-check" name="jform[booking_type]" id="typeService" value="servizio" checked onchange="toggleBookingType()">
									<label class="btn btn-outline-kokedama" for="typeService"><i class="fas fa-leaf me-2"></i>Servizio</label>
									<input type="radio" class="btn-check" name="jform[booking_type]" id="typeEvent" value="evento" onchange="toggleBookingType()">
									<label class="btn btn-outline-kokedama" for="typeEvent"><i class="fas fa-calendar-day me-2"></i>Evento/Workshop</label>
								</div>
							</div>

							<div class="mb-3" id="serviceSelect">
								<label for="service_id" class="form-label">Servizio *</label>
								<select name="jform[service_id]" id="service_id" class="form-select">
									<option value="">Seleziona...</option>
									<?php foreach ($this->services as $s) : ?>
										<option value="<?php echo $s->id; ?>"><?php echo $this->escape($s->title); ?></option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="mb-3 d-none" id="eventSelect">
								<label for="event_id" class="form-label">Evento *</label>
								<select name="jform[event_id]" id="event_id" class="form-select">
									<option value="">Seleziona...</option>
									<?php foreach ($this->events as $e) : ?>
										<option value="<?php echo $e->id; ?>"><?php echo $this->escape($e->title); ?> (<?php echo date('d/m/Y', strtotime($e->event_date)); ?>)</option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="row g-3 mb-3">
								<div class="col-md-6">
									<label for="booking_date" class="form-label">Data preferita *</label>
									<input type="date" name="jform[booking_date]" id="booking_date" class="form-control" required min="<?php echo date('Y-m-d', strtotime('+2 days')); ?>">
								</div>
								<div class="col-md-6">
									<label for="booking_time" class="form-label">Orario preferito</label>
									<input type="time" name="jform[booking_time]" id="booking_time" class="form-control">
								</div>
							</div>

							<div class="mb-3">
								<label for="participants" class="form-label">Numero partecipanti *</label>
								<input type="number" name="jform[participants]" id="participants" class="form-control" min="1" max="100" value="1" required>
							</div>

							<div class="row g-3 mb-3">
								<div class="col-md-6">
									<label for="first_name" class="form-label">Nome *</label>
									<input type="text" name="jform[first_name]" id="first_name" class="form-control" required>
								</div>
								<div class="col-md-6">
									<label for="last_name" class="form-label">Cognome *</label>
									<input type="text" name="jform[last_name]" id="last_name" class="form-control" required>
								</div>
							</div>

							<div class="row g-3 mb-3">
								<div class="col-md-6">
									<label for="email" class="form-label">Email *</label>
									<input type="email" name="jform[email]" id="email" class="form-control" required>
								</div>
								<div class="col-md-6">
									<label for="phone" class="form-label">Telefono</label>
									<input type="tel" name="jform[phone]" id="phone" class="form-control">
								</div>
							</div>

							<div class="mb-3">
								<label for="notes" class="form-label">Note</label>
								<textarea name="jform[notes]" id="notes" class="form-control" rows="3"></textarea>
							</div>

							<div class="mb-4 form-check">
								<input type="checkbox" name="jform[consent_gdpr]" id="consent_gdpr" class="form-check-input" value="1" required>
								<label class="form-check-label small" for="consent_gdpr">
									<?php echo $this->params->get('gdpr_consent_text', 'Ho letto e accetto l\'informativa sulla privacy.'); ?> *
								</label>
							</div>

							<button type="submit" class="btn btn-kokedama btn-lg w-100">
								<i class="fas fa-paper-plane me-2"></i>Invia prenotazione
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
function toggleBookingType() {
	const isService = document.getElementById('typeService').checked;
	document.getElementById('serviceSelect').classList.toggle('d-none', !isService);
	document.getElementById('eventSelect').classList.toggle('d-none', isService);
	if (isService) { document.getElementById('event_id').value = ''; } else { document.getElementById('service_id').value = ''; }
}
</script>
