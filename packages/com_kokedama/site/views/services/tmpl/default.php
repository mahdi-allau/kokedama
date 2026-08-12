<?php
/**
 * @package     Kokedama
 * @subpackage  com_kokedama
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
?>

<div class="kokedama-services">
	<div class="container">
		<div class="row mb-5">
			<div class="col-lg-8 mx-auto text-center">
				<h1 class="display-4 fw-light text-kokedama-dark"><?php echo $this->escape($this->params->get('business_name')); ?> — Servizi</h1>
				<p class="lead text-muted">Scopri le nostre creazioni e i workshop dedicati all'arte del kokedama.</p>
			</div>
		</div>

		<?php if (empty($this->items)) : ?>
			<div class="alert alert-info">Al momento non ci sono servizi disponibili.</div>
		<?php else : ?>
			<div class="row g-4">
				<?php foreach ($this->items as $item) : ?>
					<div class="col-md-6 col-lg-4">
						<div class="card h-100 border-0 shadow-sm service-card">
							<?php if ($item->image) : ?>
								<img src="<?php echo $this->escape($item->image); ?>" class="card-img-top" alt="<?php echo $this->escape($item->title); ?>" loading="lazy">
							<?php else : ?>
								<div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 220px;">
									<i class="fas fa-leaf fa-3x text-muted"></i>
								</div>
							<?php endif; ?>
							<div class="card-body d-flex flex-column">
								<h5 class="card-title"><?php echo $this->escape($item->title); ?></h5>
								<p class="card-text text-muted flex-grow-1"><?php echo $this->escape($item->short_desc); ?></p>
								<div class="d-flex justify-content-between align-items-center mt-3">
									<?php if ($item->price) : ?>
										<span class="badge bg-kokedama text-white fs-6">€ <?php echo number_format($item->price, 2, ',', '.'); ?></span>
									<?php endif; ?>
									<?php if ($item->duration) : ?>
										<small class="text-muted"><i class="far fa-clock me-1"></i><?php echo (int)$item->duration; ?> min</small>
									<?php endif; ?>
								</div>
								<a href="<?php echo Route::_('index.php?option=com_kokedama&view=service&id=' . $item->id . '&alias=' . $item->alias); ?>" class="btn btn-outline-kokedama mt-3">
									Scopri di più <i class="fas fa-arrow-right ms-1"></i>
								</a>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
