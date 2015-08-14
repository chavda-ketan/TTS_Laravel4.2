
<table class="body-wrap" bgcolor="#fff">
    <tr>
        <td></td>
        <td class="container" bgcolor="#FFFFFF">

            <!-- remittance -->
            <div class="content head">
                <table>
                    <tr>
                        <td style="width: 50%">
                            <h2 style="font-size: 160%;">The Techknow Space Inc.</h2>
                            <p>
                                33 City Centre Dr. Unit #142<br/>
                                Mississauga, Ontario  L5B 2N5<br/>
                                905-897-9860<br/>
                                <br/>
                            </p>

{{--                             <p>
                                {{ $account->Company }}<br/>
                                {{ $account->Address }}<br/>
                                {{ $account->City }}, {{ $account->State }}<br/>
                                {{ $account->Zip }}
                            </p> --}}
                        </td>

                        <td class="headright">
                            <h2 style="font-size: 160%;">Account Statement</h2>
                            <p>
                                {{ $account->Company }}<br/>
                                Balance: ${{ number_format((float) $account->AccountBalance, 2, '.', '') }}<br/>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- /remittance -->

            <div class="content">
                <table>
                    <tbody>
                        <tr>
                            <td>
                                <h2 style="font-size: 160%;">Account Summary - {{ $location }}</h2>
                                <p>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="realtable">
                    <tr><th>Summary Information</th></tr>
                    <tr>
                        <td>
                            <table class="summarytable">
                                <tr>
                                    <td style="width: 25%">
                                        Account Number: <br/>
                                        Name:
                                    </td>
                                    <td style="width: 25%">
                                        {{ $account->AccountNumber }}<br/>
                                        {{ $account->Company }}<br/>
                                        {{ $account->Address }}<br/>
                                        {{ $account->City }}, {{ $account->State }}<br/>
                                        {{ $account->Zip }}
                                    </td>

                                    <td style="width: 25%">
                                        Previous Balance: <br/>
                                        Add New Charges: <br/>
                                        Less Credits / Payments: <br/>
                                        =======================<br/>
                                        New Balance: <br/>
                                    </td>

                                    <td class="headright" style="width: 25%">
                                        ${{ number_format((float) $account->AccountBalance - $newCharges, 2, '.', '') }}<br/>
                                        ${{ $newCharges }}<br/>
                                        ${{ $newPayments }}<br/>
                                        =======================<br/>
                                        ${{ number_format((float) $account->AccountBalance, 2, '.', '') }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="content">
                <table>
                    <tbody>
                        <tr>
                            <td>
                                <h2 style="font-size: 160%;">Transaction Activity - {{ $location }}</h2>
                                <p>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="realtable">
                    <thead>
                        <tr>
                            <th>Transaction</th>
                            <th>Due Date</th>
                            <th>Details</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($transactions as $transactionId => $transaction)
                        <tr>
                            <td>TR-{{ $transactionId }}<br/>
                                &nbsp;&nbsp;&nbsp;{{ $transaction['entry']->FormattedDate }}</td>
                            <td>15/{{ date("m/Y", strtotime("+1 month", strtotime($transaction['entry']->Date))) }}</td>
                            <td>
                                @foreach($transaction['orderentry'] as $lineItem)
                                    {{ $lineItem->Description }}<br/>
                                    @if($lineItem->Comment)
                                        &nbsp;&nbsp;-&nbsp;{{ $lineItem->Comment }}<br/>
                                    @endif
                                @endforeach

                                @foreach($transaction['items'] as $lineItem)
                                    {{ $lineItem->Description }}<br/>
                                    @if($lineItem->Comment)
                                        &nbsp;&nbsp;-&nbsp;{{ $lineItem->Comment }}<br/>
                                    @endif
                                @endforeach
                            </td>
                            <td>${{ number_format((float) $transaction['entry']->Amount, 2, '.', '') }}</td>
                            <td></td>
                            <td>${{ number_format((float) $transaction['newBalance'], 2, '.', '') }}</td>
                        </tr>
                    @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <td style="border-left: none; border-right: none;"></td>
                            <td style="border-left: none; border-right: none;"></td>
                            <td style="border-left: none; text-align: right; padding-right: 5px;">Total</td>
                            <td>${{ number_format((float) $account->AccountBalance, 2, '.', '') }}</td>
                            <td>$0.00</td>
                            <td>${{ number_format((float) $account->AccountBalance, 2, '.', '') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </td>
        <td></td>
    </tr>
</table>
