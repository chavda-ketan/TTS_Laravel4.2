@foreach($records as $record)
    <tr>
        <td>{{ $record->Time }}</td>
        <td>{{ $record->LastUpdated }}</td>
        <td>{{ $record->Name }}</td>
        <td>{{ $record->Description }}</td>
        <td>{{ $record->Comment }}</td>
   </tr>
@endforeach