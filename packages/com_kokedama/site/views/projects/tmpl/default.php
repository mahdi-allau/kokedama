<?php defined('_JEXEC') or die; ?>
<div class="kokedama-projects py-5">
	<div class="container">
		<div class="text-center mb-5">
			<h1 class="display-4 fw-light">Portfolio</h1>
			<p class="lead text-muted">Le nostre creazioni, ognuna unica come la natura che la ispira.</p>
		</div>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-info">Nessun progetto disponibile.</div>
		<?php else : ?>
			<div class="row g-4">
				<?php foreach ($this->items as $item) : ?>
					<div class="col-md-6 col-lg-4">
						<div class="card border-0 shadow-sm h-100 project-card">
							<?php if ($item->image) : ?>
								<img src="<?php echo $this->escape($item->image); ?>" class="card-img-top" alt="<?php echo $this->escape($item->title); ?>" loading="lazy">
							<?php endif; ?>
							<div class="card-body">
								<h5 class="card-title"><?php echo $this->escape($item->title); ?></h5>
								<?php if ($item->plant_type) : ?>
									<span class="badge bg-kokedama-light text-kokedama-dark"><?php echo $this->escape($item->plant_type); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
