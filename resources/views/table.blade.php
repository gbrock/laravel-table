<table class="{{ $class or 'table' }}">
    @if(count($columns))
	<thead>
		<tr>
        @foreach($columns as $c)
            <th>{{ $c }}</th>
        @endforeach

		</tr>
	</thead>
    @endif
	<tbody>
        @if(count($rows))
            @foreach($rows as $r)

        <tr>
            @foreach($columns as $c)
                <td>{{ $r->{$c} }}</td>
            @endforeach

        </tr>

            @endforeach
        @endif
	</tbody>
</table>
