<?php
	$enable = config('meridian.pdf.disable_empty_row_border');
?>
@foreach(getBlankLines($lines) as $line)
	<tr>
		@foreach(range(1, $td) as $l)
			@if($enable)
				<td style="border: none!important;">&nbsp;</td>
			@else
				<td>&nbsp;</td>
			@endif
		@endforeach
	</tr>
@endforeach