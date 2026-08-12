<?php defined('_JEXEC') or die; ?>
<form action="index.php?option=com_kokedama&view=messages" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<?php echo \Joomla\CMS\Layout\LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
			<?php if (empty($this->items)) : ?>
				<div class="alert alert-info"><?php echo \Joomla\CMS\Language\Text::_('COM_KOKEDAMA_NO_ITEMS'); ?></div>
			<?php else : ?>
				<table class="table table-striped" id="messagesList">
					<thead>
						<tr>
							<th width="1%"><?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.checkall'); ?></th>
							<th><?php echo \Joomla\CMS\Language\Text::_('COM_KOKEDAMA_FIELD_TITLE'); ?></th>
							<th><?php echo \Joomla\CMS\Language\Text::_('COM_KOKEDAMA_FIELD_EMAIL'); ?></th>
							<th><?php echo \Joomla\CMS\Language\Text::_('COM_KOKEDAMA_FIELD_SUBJECT'); ?></th>
							<th><?php echo \Joomla\CMS\Language\Text::_('COM_KOKEDAMA_HEADING_STATUS'); ?></th>
							<th><?php echo \Joomla\CMS\Language\Text::_('COM_KOKEDAMA_HEADING_CREATED'); ?></th>
							<th width="1%"><?php echo \Joomla\CMS\Language\Text::_('JGRID_HEADING_ID'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($this->items as $i => $item) : ?>
							<tr class="<?php echo $item->status === 'new' ? 'table-warning' : ''; ?>">
								<td><?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $item->id); ?></td>
								<td>
									<a href="index.php?option=com_kokedama&task=message.edit&id=<?php echo $item->id; ?>">
										<strong><?php echo $this->escape($item->name); ?></strong>
									</a>
								</td>
								<td><?php echo $this->escape($item->email); ?></td>
								<td><?php echo $this->escape($item->subject); ?></td>
								<td>
									<span class="badge bg-<?php
										echo $item->status === 'new' ? 'danger' :
											($item->status === 'read' ? 'warning text-dark' :
											($item->status === 'replied' ? 'success' : 'secondary'));
									?>">
										<?php echo \Joomla\CMS\Language\Text::_('COM_KOKEDAMA_MESSAGE_' . strtoupper($item->status)); ?>
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
