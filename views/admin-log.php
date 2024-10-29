<div class="wrap developer-tools-logger">

	<h3><?php _e('Log', 'developer-tools'); ?></h3>

	<table class="widefat">
		<thead>
		<tr>
			<th scope="col">
				Content
			</th>
			<th scope="col">
				Backtrace
			</th>
			<th scope="col">
				Time
			</th>
		</tr>
		</thead>

		<tfoot>
		<tr>
			<th scope="col">
				Content
			</th>
			<th scope="col">
				Backtrace
			</th>
			<th scope="col">
				Time
			</th>
		</tr>
		</tfoot>

		<tbody>
		</tbody>
	</table>

	<div class="templates" style="display: none;">
		<table>
			<tr class="no-entries-message">
				<td colspan="3"><?php _e('No log entries available', 'developer-tools'); ?></td>
			</tr>
			<tr class="entry">
				<td class="column-content"></td>
				<td class="column-backtrace"></td>
				<td class="column-time"></td>
			</tr>
		</table>
	</div>
</div>