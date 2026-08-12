<?php
defined('_JEXEC') or die;
use Joomla\CMS\Router\Route;
?>
<div class="kokedama-service-detail py-5">
	<div class="container">
		<?php if ($this->item) : ?>
			<div class="row g-5">
				<div class="col-lg-6">
					<?php if ($this->item->image) : ?>
						<img src="<?php echo $this->escape($this->item->image); ?>" class="img-fluid rounded shadow" alt="<?php echo $this->escape($this->item->title); ?>">
					<?php endif; ?>
					<?php if (!empty($this->item->gallery)) : ?>
						<div class="row g-2 mt-3">
							<?php foreach ($this->item->gallery as $img) : ?>
								<div class="col-4"><img src="<?php echo $this->escape($img); ?>" class="img-fluid rounded" loading="lazy"></div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="col-lg-6">
					<h1 class="display-5 fw-light"><?php echo $this->escape($this->item->title); ?></h1>
					<div class="lead text-muted mb-4"><?php echo $this->item->description; ?></div>
					<div class="d-flex gap-3 mb-4">
						<?php if ($this->item->price) : ?>
							<div class="fs-3 text-kokedama fw-bold">€ <?php echo number_format($this->item->price, 2, ',', '.'); ?></div>
						<?php endif; ?>
						<?php if ($this->item->duration) : ?>
							<div class="fs-5 text-muted"><i class="far fa-clock me-2"></i><?php echo (int)$this->item->duration; ?> minuti</div>
						<?php endif; ?>
					</div>
					<a href="<?php echo Route::_('index.php?option=com_kokedama&view=booking&service_id=' . $this->item->id); ?>" class="btn btn-kokedama btn-lg">
						<i class="fas fa-calendar-check me-2"></i>Prenota ora
					</a>
					<a href="<?php echo Route::_('index.php?option=com_kokedama&view=services'); ?>" class="btn btn-outline-secondary btn-lg ms-2">Torna ai servizi</a>
				</div>
			</div>
		<?php else : ?>
			<div class="alert alert-warning">Servizio non trovato.</div>
		<?php endif; ?>
	</div>
</div>
