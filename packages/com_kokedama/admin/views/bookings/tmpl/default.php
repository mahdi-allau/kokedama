<?php defined('_JEXEC') or die; ?>
<form action="index.php?option=com_kokedama&view=bookings" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<?php echo \Joomla\CMS\Layout\LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
			<?php if (empty($this->items)) : ?>
				<div class="alert alert-info"><?php echo \Joomla\CMS\Language\Text::_('COM_KOKEDAMA_NO_ITEMS'); ?></div>
			<?php else : ?>
				<table class="table table-striped" id="bookingsList">
					<thead>
						<tr>
							<th width="1%"><?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.checkall'); ?></th>
							<th><?php echo \Joomla\CMS\Language\Text::_('COM_KOKEDAMA_HEADING_TITLE'); ?></th>
							<th><?php echo \Joomla\CMS\Language\Text::_('COM_KOKEDAMA_FIELD_BOOKING_TYPE'); ?></th>
							<th><?php echo \Joomla\CMS\Language\Text::_('COM_KOKEDAMA_FIELD_DATE'); ?></th>
							<th><?php echo \Joomla\CMS\Language\Text::_('COM_KOKEDAMA_FIELD_PARTICIPANTS'); ?></th>
							<th><?php echo \Joomla\CMS\Language\Text::_('COM_KOKEDAMA_HEADING_STATUS'); ?></th>
							<th><?php echo \Joomla\CMS\Language\Text::_('COM_KOKEDAMA_HEADING_CREATED'); ?></th>
							<th width="1%"><?php echo \Joomla\CMS\Language\Text::_('JGRID_HEADING_ID'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($this->items as $i => $item) : ?>
							<tr>
								<td><?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $item->id); ?></td>
								<td>
									<a href="index.php?option=com_kokedama&task=booking.edit&id=<?php echo $item->id; ?>">
										<strong><?php echo $this->escape($item->first_name . ' ' . $item->last_name); ?></strong>
									</a>
									<div class="small text-muted"><?php echo $this->escape($item->email); ?></div>
								</td>
								<td><?php echo ucfirst($this->escape($item->booking_type)); ?><br><small class="text-muted"><?php echo $this->escape($item->service_title ?: $item->event_title); ?></small></td>
								<td><?php echo $this->escape($item->booking_date); ?><br><small><?php echo $item->booking_time ? substr($item->booking_time, 0, 5) : ''; ?></small></td>
								<td><?php echo (int)$item->participants; ?></td>
								<td>
									<span class="badge bg-<?php
										echo $item->status === 'pending' ? 'warning text-dark' :
											($item->status === 'confirmed' ? 'success' :
											($item->status === 'rejected' ? 'danger' :
											($item->status === 'completed' ? 'info' : 'secondary')));
									?>">
										<?php echo \Joomla\CMS\Language\Text::_('COM_KOKEDAMA_STATUS_' . strtoupper($item->status)); ?>
									</span>
								</td>
								<td><?php echo \Joomla\CMS\HTML\HTMLHelper::_('date', $item->created, \Joomla\CMS\Language\Text::_('DATE_FORMAT_LC5')); ?></td>
								<td><?php echo (int)$item->id; ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php echo $this->pagination->getListFooter(); ?>
			<?php endif; ?>
		</div>
	</div>
	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0">
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
</form>
