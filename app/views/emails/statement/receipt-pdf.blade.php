<div style="font-family: Helvetica, Arial, sans-serif">
    <div style="text-align: center;">
        The TechKnow Space Inc.<br>
        Accounts Receivable<br>
        33 City Centre Dr Unit #142<br>
        Mississauga, ON L5B 2N5<br>
        905 897 9860<br><br>

        HST#: 81032-4079<br>
    </div>
    <br>
    <br>

    <b>ACCOUNT #:</b> {{ $account->AccountNumber }}<br>
    <b>INVOICE #:</b> {{ $transactionNumber }}<br>
    @if(isset($orderEntry))
        <b>WORK ORDER #:</b> {{ $orderEntry[0]->OrderID }}<br>
    @endif
    <br>

    <b>Bill To:</b> {{ $account->FirstName }} {{ $account->LastName }} - {{ $account->Company }}<br>
    @if(isset($account->CustomField1))
        ATT: {{ $account->CustomField1 }}<br>
    @endif
    <b>Date:</b> {{ $transaction->Time }}<br>
    <br>

    <b>DESCRIPTION &amp; COMMENTS</b><br>
    <hr>
    <br>
    <table>
        @if(isset($orderEntry))
            @foreach($orderEntry as $lineItem)
                <tr style="width: 100%;">
                    <td style="width:600px;">
                        <b>{{ $lineItem->Description }}</b><br>
                        @if($lineItem->Comment)
                            {{ $lineItem->Comment }}
                        @endif
                    </td>
                    <td style="float: right">
                        <b>${{ number_format((float) $lineItem->Price, 2, '.', '') }}</b>
                    </td>
                </tr>
                <br>
            @endforeach
        @endif

        @if(isset($lineItems))
            @foreach($lineItems as $lineItem)
                <tr style="width: 100%;">
                    <td style="width:600px;">
                        <b>{{ $lineItem->Description }}</b><br>
                        @if($lineItem->Comment)
                            {{ $lineItem->Comment }}
                        @endif
                    </td>
                    <td>
                        <b>${{ number_format((float) $lineItem->Price, 2, '.', '') }}</b>
                    </td>
                </tr>
                <br>
            @endforeach
        @endif
    </table>
    <hr>
    <table>
        <tr style="width: 100%;">
            <td style="width: 600px;">
                <b>Subtotal</b><br>
                <b>HST 13%</b><br>
                <b>Total</b><br>
            </td>
            <td>
                ${{ number_format((float) $order->Total - $order->Tax, 2, '.', '') }}<br>
                ${{ number_format((float) $order->Tax, 2, '.', '') }}<br>
                ${{ number_format((float) $order->Total, 2, '.', '') }}<br>
            </td>
        </tr>

    {{-- <b>Tendered:</b>      ${{ number_format((float) $tender[0]->Amount, 2, '.', '') }} - {{ $tender[0]->Description }}<br> --}}
    {{-- <b>Pending Charges:</b>  $575.00<br> --}}
    </table>

    <br>
    <br>

    <div style="text-align: center;">
        Thank you for choosing<br>
        The TechKnow Space<br>
        All Digital Repairs Under 1 Roof<br>
        http://techknowspace.com<br>
    </div>
</div>