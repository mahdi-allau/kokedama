<?php defined('_JEXEC') or die; ?>
<?php if (!empty($items)) : ?>
<section class="module-services py-5">
	<div class="container">
		<div class="text-center mb-5">
			<h2 class="display-5 fw-light">I nostri servizi</h2>
			<p class="text-muted">Creazioni naturali fatte a mano con amore e cura.</p>
		</div>
		<div class="row g-4">
			<?php foreach ($items as $item) : ?>
				<div class="col-md-6 col-lg-4">
					<div class="card h-100 border-0 shadow-sm service-card">
						<?php if ($item->image) : ?>
							<img src="<?php echo $item->image; ?>" class="card-img-top" alt="<?php echo $item->title; ?>" loading="lazy">
						<?php endif; ?>
						<div class="card-body d-flex flex-column">
							<h5 class="card-title"><?php echo $item->title; ?></h5>
							<p class="card-text text-muted flex-grow-1"><?php echo \Joomla\CMS\HTML\HTMLHelper::_('string.truncate', strip_tags($item->short_desc), 100); ?></p>
							<?php if ($params->get('show_price', 1) && $item->price) : ?>
								<div class="fw-bold text-kokedama mb-2">€ <?php echo number_format($item->price, 2, ',', '.'); ?></div>
							<?php endif; ?>
							<a href="index.php?option=com_kokedama&view=service&id=<?php echo $item->id; ?>&alias=<?php echo $item->alias; ?>" class="btn btn-outline-kokedama btn-sm">Scopri di più</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="text-center mt-4">
			<a href="index.php?option=com_kokedama&view=services" class="btn btn-kokedama">Vedi tutti i servizi</a>
		</div>
	</div>
</section>
<?php endif; ?>
