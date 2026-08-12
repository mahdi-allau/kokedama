<?php defined('_JEXEC') or die; ?>
<div class="kokedama-dashboard">
	<h1 class="mb-4">Dashboard Kokedama</h1>

	<div class="row g-4 mb-5">
		<div class="col-md-3">
			<div class="card text-center border-0 shadow-sm p-4">
				<div class="display-4 text-kokedama"><?php echo (int)$this->stats->pending_bookings; ?></div>
				<div class="text-muted">Prenotazioni da gestire</div>
				<a href="index.php?option=com_kokedama&view=bookings&filter[status]=pending" class="stretched-link"></a>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-center border-0 shadow-sm p-4">
				<div class="display-4 text-kokedama"><?php echo (int)$this->stats->new_messages; ?></div>
				<div class="text-muted">Messaggi nuovi</div>
				<a href="index.php?option=com_kokedama&view=messages&filter[status]=new" class="stretched-link"></a>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-center border-0 shadow-sm p-4">
				<div class="display-4 text-kokedama"><?php echo (int)$this->stats->upcoming_events; ?></div>
				<div class="text-muted">Prossimi eventi</div>
				<a href="index.php?option=com_kokedama&view=events" class="stretched-link"></a>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-center border-0 shadow-sm p-4">
				<div class="display-4 text-kokedama"><?php echo (int)$this->stats->published_services; ?></div>
				<div class="text-muted">Servizi attivi</div>
				<a href="index.php?option=com_kokedama&view=services" class="stretched-link"></a>
			</div>
		</div>
	</div>

	<div class="row g-4">
		<div class="col-lg-6">
			<div class="card border-0 shadow-sm">
				<div class="card-header bg-white fw-bold">Ultime Prenotazioni</div>
				<div class="card-body p-0">
					<?php if (empty($this->stats->recent_bookings)) : ?>
						<p class="p-3 text-muted mb-0">Nessuna prenotazione.</p>
					<?php else : ?>
						<ul class="list-group list-group-flush">
							<?php foreach ($this->stats->recent_bookings as $b) : ?>
								<li class="list-group-item d-flex justify-content-between align-items-center">
									<div>
										<strong><?php echo $this->escape($b->first_name . ' ' . $b->last_name); ?></strong>
										<div class="small text-muted"><?php echo $this->escape($b->email); ?> — <?php echo $this->escape($b->booking_date); ?></div>
									</div>
									<span class="badge bg-<?php echo $b->status === 'pending' ? 'warning' : ($b->status === 'confirmed' ? 'success' : 'secondary'); ?>">
										<?php echo ucfirst($this->escape($b->status)); ?>
									</span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="card border-0 shadow-sm">
				<div class="card-header bg-white fw-bold">Ultimi Messaggi</div>
				<div class="card-body p-0">
					<?php if (empty($this->stats->recent_messages)) : ?>
						<p class="p-3 text-muted mb-0">Nessun messaggio.</p>
					<?php else : ?>
						<ul class="list-group list-group-flush">
							<?php foreach ($this->stats->recent_messages as $m) : ?>
								<li class="list-group-item d-flex justify-content-between align-items-center">
									<div>
										<strong><?php echo $this->escape($m->name); ?></strong>
										<div class="small text-muted"><?php echo \Joomla\CMS\HTML\HTMLHelper::_('string.truncate', $this->escape($m->message), 60); ?></div>
									</div>
									<?php if ($m->status === 'new') : ?>
										<span class="badge bg-danger">Nuovo</span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
