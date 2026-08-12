<?php
/**
 * @package     Kokedama
 * @subpackage  tpl_kokedama
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

$app   = Factory::getApplication();
$doc   = $app->getDocument();
$user  = $app->getIdentity();
$this->language  = $doc->language;
$this->direction = $doc->getDirection();

// Load Bootstrap and template assets
$wa = $this->getWebAssetManager();
$wa->useStyle('template.kokedama');
$wa->useScript('template.kokedama');

// Google Fonts
$doc->addStyleSheet('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Lato:wght@300;400;700&display=swap');

// Font Awesome 6
$doc->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css');

// Template params
$params = $this->params;
$logoText = $params->get('logo_text', 'Kokedama');
$showBrand = $params->get('show_brand', 1);

// Component params for business info
$componentParams = Factory::getApplication()->getParams('com_kokedama');
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<jdoc:include type="head" />
</head>
<body class="site <?php echo $this->direction === 'rtl' ? 'rtl' : ''; ?>">

	<!-- Navbar -->
	<nav class="navbar navbar-expand-lg navbar-kokedama sticky-top">
		<div class="container">
			<a class="navbar-brand d-flex align-items-center" href="<?php echo Uri::root(); ?>">
				<?php if ($params->get('logo')) : ?>
					<img src="<?php echo $this->escape($params->get('logo')); ?>" alt="<?php echo $this->escape($logoText); ?>" height="45" class="me-2">
				<?php endif; ?>
				<?php if ($showBrand) : ?>
					<span class="brand-text"><?php echo $this->escape($logoText); ?></span>
					<small class="brand-sub ms-2 d-none d-md-inline">& Sculture Naturali</small>
				<?php endif; ?>
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="mainNav">
				<jdoc:include type="modules" name="menu" style="none" />
				<div class="ms-lg-auto d-flex align-items-center gap-3">
					<a href="tel:<?php echo preg_replace('/\s+/', '', $componentParams->get('business_phone', '0532242503')); ?>" class="nav-phone d-none d-lg-block">
						<i class="fas fa-phone me-1"></i><?php echo $this->escape($componentParams->get('business_phone', '0532 242503')); ?>
					</a>
					<a href="<?php echo Uri::root(); ?>index.php?option=com_kokedama&view=booking" class="btn btn-kokedama btn-sm">
						<i class="fas fa-calendar-check me-1"></i>Prenota
					</a>
				</div>
			</div>
		</div>
	</nav>

	<!-- Hero / Top Modules -->
	<?php if ($this->countModules('hero')) : ?>
		<section class="hero-section">
			<jdoc:include type="modules" name="hero" style="none" />
		</section>
	<?php endif; ?>

	<?php if ($this->countModules('top')) : ?>
		<section class="top-modules py-4">
			<div class="container">
				<jdoc:include type="modules" name="top" style="card" />
			</div>
		</section>
	<?php endif; ?>

	<!-- Main Content -->
	<main class="main-content py-5">
		<div class="container">
			<?php if ($this->countModules('main-top')) : ?>
				<div class="main-top mb-4">
					<jdoc:include type="modules" name="main-top" style="card" />
				</div>
			<?php endif; ?>

			<div class="row">
				<?php if ($this->countModules('sidebar-left')) : ?>
					<aside class="col-lg-3 sidebar-left mb-4">
						<jdoc:include type="modules" name="sidebar-left" style="card" />
					</aside>
				<?php endif; ?>

				<?php
				$mainClass = 'col-lg';
				if ($this->countModules('sidebar-left') && $this->countModules('sidebar-right')) {
					$mainClass = 'col-lg-6';
				} elseif ($this->countModules('sidebar-left') || $this->countModules('sidebar-right')) {
					$mainClass = 'col-lg-9';
				}
				?>
				<div class="<?php echo $mainClass; ?>">
					<jdoc:include type="message" />
					<jdoc:include type="component" />
				</div>

				<?php if ($this->countModules('sidebar-right')) : ?>
					<aside class="col-lg-3 sidebar-right mb-4">
						<jdoc:include type="modules" name="sidebar-right" style="card" />
					</aside>
				<?php endif; ?>
			</div>

			<?php if ($this->countModules('main-bottom')) : ?>
				<div class="main-bottom mt-4">
					<jdoc:include type="modules" name="main-bottom" style="card" />
				</div>
			<?php endif; ?>
		</div>
	</main>

	<!-- Footer -->
	<footer class="site-footer">
		<?php if ($this->countModules('footer')) : ?>
			<div class="footer-widgets py-5">
				<div class="container">
					<div class="row g-4">
						<jdoc:include type="modules" name="footer" style="none" />
					</div>
				</div>
			</div>
		<?php endif; ?>
		<div class="footer-bottom py-4">
			<div class="container">
				<div class="row align-items-center">
					<div class="col-md-6 text-center text-md-start">
						<p class="mb-0 small">&copy; <?php echo date('Y'); ?> <?php echo $this->escape($componentParams->get('business_name', 'Kokedama & Sculture Naturali')); ?> — <?php echo $this->escape($componentParams->get('business_city', 'Ferrara')); ?></p>
					</div>
					<div class="col-md-6 text-center text-md-end">
						<div class="social-links">
							<?php if ($componentParams->get('business_facebook')) : ?>
								<a href="<?php echo $this->escape($componentParams->get('business_facebook')); ?>" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
							<?php endif; ?>
							<?php if ($componentParams->get('business_instagram')) : ?>
								<a href="<?php echo $this->escape($componentParams->get('business_instagram')); ?>" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
							<?php endif; ?>
						</div>
						<?php if ($this->countModules('copyright')) : ?>
							<jdoc:include type="modules" name="copyright" style="none" />
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</footer>

	<!-- Back to top -->
	<a href="#" id="backToTop" class="back-to-top" aria-label="Torna su"><i class="fas fa-arrow-up"></i></a>

	<!-- Cookie Banner -->
	<div id="cookieBanner" class="cookie-banner" style="display:none;">
		<div class="container d-flex flex-wrap align-items-center justify-content-between gap-2">
			<p class="mb-0 small">Questo sito utilizza cookie tecnici e, con il tuo consenso, cookie di profilazione. <a href="<?php echo Uri::root(); ?>index.php?option=com_content&view=article&id=<?php echo (int)$componentParams->get('gdpr_privacy_article', 1); ?>" class="text-decoration-underline">Leggi l'informativa</a>.</p>
			<div class="d-flex gap-2">
				<button class="btn btn-kokedama btn-sm" onclick="acceptCookies()">Accetta</button>
				<button class="btn btn-outline-light btn-sm" onclick="rejectCookies()">Rifiuta</button>
			</div>
		</div>
	</div>

	<jdoc:include type="modules" name="offcanvas" style="none" />
</body>
</html>
