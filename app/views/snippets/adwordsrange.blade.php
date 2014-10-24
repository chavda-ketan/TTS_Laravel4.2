@foreach($dates as $day => $data)
    <tr class="{{ $class }}">
        <td>
            <span title="Campaigns: {{ $data['campaigns'] }}">{{ $day }}</span>
            <p><i class="fa fa-mobile"></i></p>
        </td>

        <td>
            {{ $data['clicks'] }}
            <p>{{ $data['clicks_m'] }}</p>
        </td>

        <td>
            {{ $data['ctr'] }}%
            <p>{{ $data['ctr_m'] }}%</p>
        </td>

        <td>
            {{ $data['qs'] }}
            <p>{{ $data['qs_m'] }}</p>
        </td>

        <td>
            {{ $data['pos'] }}
            <p>{{ $data['pos_m'] }}</p>
        </td>

        <td>
            ${{ $data['cpc'] }}
            <p>${{ $data['cpc_m'] }}</p>
        </td>

        <td>{{ $data['rms_wo'] }}</td>


        <td>{{ @number_format(($data['clicks'] + $data['clicks_m']) / $data['rms_wo'], 1) }}</td>

        <td>${{ @number_format(($data['cost']+$data['cost_m']) / $data['rms_wo']) }}</td>

        <td>${{ @number_format(($data['cost']+$data['cost_m']) / $data['rms_sale'] * 100) }}</td>

        <td>${{ $data['cost'] }}
            <p>${{ $data['cost_m'] }}</p>
        </td>

        <td>${{ $data['rms_sale'] }}</td>

        <td>${{ @number_format($data['rms_sale'] / $data['rms_wo']) }}</td>

        <td>{{ $data['imp'] }}
            <p>{{ $data['imp_m'] }}</p>
        </td>
    </tr>
@endforeach