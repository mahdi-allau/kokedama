<?php defined('_JEXEC') or die; use Joomla\CMS\Router\Route; ?>
<div class="kokedama-events py-5">
	<div class="container">
		<div class="text-center mb-5">
			<h1 class="display-4 fw-light">Eventi & Workshop</h1>
			<p class="lead text-muted">Partecipa ai nostri laboratori ed eventi speciali.</p>
		</div>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-info">Nessun evento programmato.</div>
		<?php else : ?>
			<div class="row g-4">
				<?php foreach ($this->items as $item) : ?>
					<div class="col-md-6 col-lg-4">
						<div class="card h-100 border-0 shadow-sm event-card">
							<?php if ($item->image) : ?>
								<img src="<?php echo $this->escape($item->image); ?>" class="card-img-top" alt="<?php echo $this->escape($item->title); ?>" loading="lazy">
							<?php endif; ?>
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-start mb-2">
									<span class="badge bg-kokedama"><?php echo ucfirst($this->escape($item->event_type)); ?></span>
									<?php if ($item->price) : ?>
										<span class="fw-bold text-kokedama">€ <?php echo number_format($item->price, 2, ',', '.'); ?></span>
									<?php endif; ?>
								</div>
								<h5 class="card-title"><?php echo $this->escape($item->title); ?></h5>
								<p class="text-muted small"><i class="far fa-calendar me-1"></i><?php echo date('d/m/Y', strtotime($item->event_date)); ?> <i class="far fa-clock ms-2 me-1"></i><?php echo substr($item->event_time, 0, 5); ?></p>
								<p class="card-text text-muted"><?php echo \Joomla\CMS\HTML\HTMLHelper::_('string.truncate', strip_tags($item->description), 120); ?></p>
								<a href="<?php echo Route::_('index.php?option=com_kokedama&view=event&id=' . $item->id . '&alias=' . $item->alias); ?>" class="btn btn-outline-kokedama btn-sm">Dettagli e prenotazione</a>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
