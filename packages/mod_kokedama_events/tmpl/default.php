<?php defined('_JEXEC') or die; ?>
<?php if (!empty($items)) : ?>
<section class="module-events py-5 bg-light">
	<div class="container">
		<div class="text-center mb-5">
			<h2 class="display-5 fw-light">Prossimi eventi</h2>
			<p class="text-muted">Workshop, laboratori e momenti speciali.</p>
		</div>
		<div class="row g-4">
			<?php foreach ($items as $item) : ?>
				<div class="col-md-6 col-lg-4">
					<div class="card h-100 border-0 shadow-sm event-card">
						<?php if ($item->image) : ?>
							<img src="<?php echo $item->image; ?>" class="card-img-top" alt="<?php echo $item->title; ?>" loading="lazy">
						<?php endif; ?>
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-start mb-2">
								<span class="badge bg-kokedama"><?php echo ucfirst($item->event_type); ?></span>
								<?php if ($item->price) : ?>
									<span class="fw-bold text-kokedama">€ <?php echo number_format($item->price, 2, ',', '.'); ?></span>
								<?php endif; ?>
							</div>
							<h5 class="card-title"><?php echo $item->title; ?></h5>
							<p class="text-muted small"><i class="far fa-calendar me-1"></i><?php echo date('d/m/Y', strtotime($item->event_date)); ?> <i class="far fa-clock ms-2 me-1"></i><?php echo substr($item->event_time, 0, 5); ?></p>
							<a href="index.php?option=com_kokedama&view=event&id=<?php echo $item->id; ?>&alias=<?php echo $item->alias; ?>" class="btn btn-outline-kokedama btn-sm">Dettagli e prenotazione</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="text-center mt-4">
			<a href="index.php?option=com_kokedama&view=events" class="btn btn-kokedama">Tutti gli eventi</a>
		</div>
	</div>
</section>
<?php endif; ?>
