<?php defined('_JEXEC') or die; use Joomla\CMS\Router\Route; ?>
<div class="kokedama-event-detail py-5">
	<div class="container">
		<?php if ($this->item) : ?>
			<div class="row g-5">
				<div class="col-lg-7">
					<?php if ($this->item->image) : ?>
						<img src="<?php echo $this->escape($this->item->image); ?>" class="img-fluid rounded shadow mb-4" alt="<?php echo $this->escape($this->item->title); ?>">
					<?php endif; ?>
					<h1><?php echo $this->escape($this->item->title); ?></h1>
					<div class="lead"><?php echo $this->item->description; ?></div>
				</div>
				<div class="col-lg-5">
					<div class="card border-0 shadow-sm p-4 sticky-top" style="top: 100px;">
						<h4 class="mb-3">Dettagli evento</h4>
						<ul class="list-unstyled mb-4">
							<li class="mb-2"><i class="far fa-calendar text-kokedama me-2"></i><?php echo date('d/m/Y', strtotime($this->item->event_date)); ?></li>
							<li class="mb-2"><i class="far fa-clock text-kokedama me-2"></i><?php echo substr($this->item->event_time, 0, 5); ?><?php echo $this->item->end_time ? ' - ' . substr($this->item->end_time, 0, 5) : ''; ?></li>
							<?php if ($this->item->location) : ?>
								<li class="mb-2"><i class="fas fa-map-marker-alt text-kokedama me-2"></i><?php echo $this->escape($this->item->location); ?></li>
							<?php endif; ?>
							<?php if ($this->item->max_participants) : ?>
								<li class="mb-2"><i class="fas fa-users text-kokedama me-2"></i>Max <?php echo (int)$this->item->max_participants; ?> partecipanti</li>
							<?php endif; ?>
						</ul>
						<?php if ($this->item->price) : ?>
							<div class="fs-3 fw-bold text-kokedama mb-3">€ <?php echo number_format($this->item->price, 2, ',', '.'); ?> <small class="text-muted fs-6">/ persona</small></div>
						<?php endif; ?>
						<a href="<?php echo Route::_('index.php?option=com_kokedama&view=booking&event_id=' . $this->item->id); ?>" class="btn btn-kokedama btn-lg w-100">
							<i class="fas fa-calendar-check me-2"></i>Prenota ora
						</a>
					</div>
				</div>
			</div>
		<?php else : ?>
			<div class="alert alert-warning">Evento non trovato.</div>
		<?php endif; ?>
	</div>
</div>
