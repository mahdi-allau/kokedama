<?php defined('_JEXEC') or die; ?>
<div class="kokedama-gallery py-5">
	<div class="container">
		<div class="text-center mb-5">
			<h1 class="display-4 fw-light">Galleria</h1>
			<p class="lead text-muted">Immagini dei nostri kokedama, workshop e momenti speciali.</p>
		</div>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-info">Nessuna immagine nella galleria.</div>
		<?php else : ?>
			<div class="row g-3" data-masonry='{"percentPosition": true}'>
				<?php foreach ($this->items as $item) : ?>
					<div class="col-6 col-md-4 col-lg-3">
						<a href="<?php echo $this->escape($item->image); ?>" class="d-block gallery-item" data-fancybox="gallery" data-caption="<?php echo $this->escape($item->title); ?>">
							<img src="<?php echo $this->escape($item->thumbnail ?: $item->image); ?>" class="img-fluid rounded shadow-sm" alt="<?php echo $this->escape($item->title); ?>" loading="lazy">
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
