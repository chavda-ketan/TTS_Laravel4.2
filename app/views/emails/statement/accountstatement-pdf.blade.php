
<table class="body-wrap" bgcolor="#fff" >
    <tr style="page-break-after: always;">
        <td></td>
        <td class="container" bgcolor="#FFFFFF">

            <!-- remittance -->
            <div class="content head">
                <table>
                    <tr>
                        <td style="width: 50%">
                            <h2 style="font-size: 160%;">The TechKnow Space Inc.</h2>
                            <p>
                                33 City Centre Dr. Unit #142<br/>
                                Mississauga, Ontario  L5B 2N5<br/>
                                905-897-9860<br/>
                                HST# 81032-4079<br/>
                            </p>
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
                                <h2 style="font-size: 160%;">Account Summary - {{ date('F j Y', strtotime("yesterday")) }} - {{ $location }}</h2>
                                <p>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="realtable" cellpadding="5">
                    <tr><th>Summary Information</th></tr>
                    <tr>
                        <td>
                            <table class="summarytable">
                                <tr>
                                    <td style="width: 25%">
                                        Account Number: <br/>
                                        Company:
                                    </td>
                                    <td style="width: 25%">
                                        {{ $account->AccountNumber }}<br/>
                                        {{ $account->Company }}<br/>
                                        {{ $account->Address }}<br/>
                                        {{ $account->City }}, {{ $account->State }}<br/>
                                        {{ $account->Zip }}
                                    </td>

                                    <td style="width: 25%">
                                        Balance {{ date('M j Y', strtotime("last day of previous month")) }}: <br/>
                                        {{ date('F Y', strtotime("yesterday")) }} Charges: <br/>
                                        Less Credits / Payments: <br/>
                                        =======================<br/>
                                        New Balance: <br/>
                                    </td>

                                    <td class="headright" style="width: 25%">
                                        ${{ number_format((float) $account->AccountBalance - $newCharges + $newPayments, 2, '.', '') }}<br/>
                                        ${{ $newCharges }}<br/>
                                        ${{ $newPayments }}<br/>
                                        =======================<br/>
                                        ${{ number_format((float) $account->AccountBalance, 2, '.', '') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-top: 1px solid #ccc; border-right: 1px solid #ccc;width: 25%"><br/><b>Accounts Payable</b></td>
                                    <td style="border-top: 1px solid #ccc; border-right: 1px solid #ccc;width: 25%"><br/>Name: {{ $account->CustomText1 }}</td>
                                    <td style="border-top: 1px solid #ccc; border-right: 1px solid #ccc;width: 25%"><br/>Phone: {{ $account->CustomText2 }}</td>
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

                <table class="realtable" cellpadding="5" style="border-collapse: collapse">
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
                    @if($account->AccountBalance - $newCharges + $newPayments != 0)
                        <tr>
                            <td style="border-bottom: 1px solid #ccc"></td>
                            <td style="border-bottom: 1px solid #ccc"></td>
                            <td style="border-bottom: 1px solid #ccc"><b>Previous Balance</b></td>
                            <td style="border-bottom: 1px solid #ccc"></td>
                            <td style="border-bottom: 1px solid #ccc"></td>
                            <td style="border-bottom: 1px solid #ccc"><b>${{ number_format((float) $account->AccountBalance - $newCharges + $newPayments, 2, '.', '') }}</b></td>
                        </tr>
                    @endif

                    @foreach($transactions as $transactionId => $transaction)
                        <tr>
                            <td>TR-{{ $transactionId }}<br/>
                                &nbsp;&nbsp;&nbsp;{{ $transaction['entry']->FormattedDate }}</td>
                            <td>15/{{ date("m/Y", strtotime("+1 month", strtotime($transaction['entry']->Date))) }}</td>
                            <td>
                                Invoice Total<br/>
                                {{--  @foreach($transaction['orderentry'] as $lineItem)
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
                                @endforeach --}}
                            </td>
                            <td>${{ number_format((float) $transaction['entry']->Amount, 2, '.', '') }}</td>
                            <td></td>
                            <td>${{ number_format((float) $transaction['newBalance'], 2, '.', '') }}</td>
                        </tr>
                    @endforeach

                    @foreach($payments as $date => $payment)
                        <tr style="padding-top:100px">
                            <td>Payment<br/>
                                &nbsp;&nbsp;&nbsp;{{ $date }}</td>
                            <td></td>
                            <td>Payment received -- Thank You</td>
                            <td></td>
                            <td>${{ $payment['amount'] }}</td>
                            <td>$-{{ $payment['amount'] }}</td>
                            {{-- <td>${{ number_format((float) $account->AccountBalance, 2, '.', '') }}</td> --}}
                            {{-- <td>${{ $totalCharges - $payment['amount'] }}</td> --}}
                        </tr>
                    @endforeach

                    </tbody>

                    <tfoot>
                        <tr>
                            <td style="border-left: none; border-right: none;"></td>
                            <td style="border-left: none; border-right: none;"></td>
                            <td style="border-left: none; border-right: none;"></td>
                            <td style="border-left: none; border-right: none;"></td>
                            <td style="border-left: none; text-align: right; padding-right: 5px;">Total</td>
                            {{-- <td>${{ number_format((float) $totalCharges, 2, '.', '') }}</td> --}}
                            {{-- <td>${{ number_format((float) $newPayments, 2, '.', '') }}</td> --}}
                            <td>${{ number_format((float) $account->AccountBalance, 2, '.', '') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </td>
        <td></td>
    </tr>
</table>
