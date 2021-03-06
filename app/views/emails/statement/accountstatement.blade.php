<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style>
/* -------------------------------------
        GLOBAL
------------------------------------- */
* {
    margin: 0;
    padding: 0;
    font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
    font-size: 100%;
    line-height: 1.4;
}

img {
    max-width: 100%;
}

body {
    -webkit-font-smoothing: antialiased;
    -webkit-text-size-adjust: none;
    width: 100%!important;
    height: 100%;
}


/* -------------------------------------
        ELEMENTS
------------------------------------- */
a {
    color: #348eda;
}

.btn-primary {
    text-decoration: none;
    color: #FFF;
    background-color: #348eda;
    border: solid #348eda;
    border-width: 10px 20px;
    line-height: 2;
    font-weight: bold;
    margin-right: 10px;
    text-align: center;
    cursor: pointer;
    display: inline-block;
    border-radius: 25px;
}

.btn-secondary {
    text-decoration: none;
    color: #FFF;
    background-color: #aaa;
    border: solid #aaa;
    border-width: 10px 20px;
    line-height: 2;
    font-weight: bold;
    margin-right: 10px;
    text-align: center;
    cursor: pointer;
    display: inline-block;
    border-radius: 25px;
}

.last {
    margin-bottom: 0;
}

.first {
    margin-top: 0;
}

.padding {
    padding: 10px 0;
}


/* -------------------------------------
        BODY
------------------------------------- */
table.body-wrap {
    width: 100%;
    padding: 20px;
}

table.body-wrap .container {
    border: 1px solid #f0f0f0;
}

table.body-wrap .logo {
    border: none;
}


/* -------------------------------------
        FOOTER
------------------------------------- */
table.footer-wrap {
    width: 100%;
    clear: both!important;
}

.footer-wrap .container p {
    font-size: 12px;
    color: #666;

}

table.footer-wrap a {
    color: #999;
}


/* -------------------------------------
        TYPOGRAPHY
------------------------------------- */
h1, h2, h3 {
    font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
    color: #000;
    margin: 10px 0 10px;
    line-height: 1.2;
    font-weight: 200;
}

h1 {
    font-size: 36px;
}
h2 {
    font-size: 28px;
}
h3 {
    font-size: 22px;
}

p, ul, ol {
    margin-bottom: 10px;
    font-weight: normal;
    font-size: 14px;
}

ul li, ol li {
    margin-left: 5px;
    list-style-position: inside;
}

/* ---------------------------------------------------
        RESPONSIVENESS
        Nuke it from orbit. It's the only way to be sure.
------------------------------------------------------ */

/* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
.container {
    display: block!important;
    max-width: 1200px!important;
    margin: 0 auto!important; /* makes it centered */
    clear: both!important;
    min-width: 800px !important;
}

/* Set the padding on the td rather than the div for Outlook compatibility */
.body-wrap .container {
    padding: 20px;
}

/* This should also be a block element, so that it will fill 100% of the .container */
.content {
    max-width: 1200px;
    margin: 0 auto;
    display: block;
}

/* Let's make sure tables in the content area are 100% wide */
.content table {
    width: 100%;
}

.head {
    border-bottom: 1px dashed #ddd;
}

td {
    vertical-align: top;
}

.realtable {
    border-spacing: 0px;
    border-collapse: collapse;
    font-size: 13px;
}
.realtable, .realtable td, .realtable th {
    text-align: left;
    font-weight: normal;
    border: 1px solid #ccc;
    border-collapse: collapse;
    border-spacing: 0px;
}
.summarytable, .summarytable td {
    border: none !important;
}
.summarytable {
}

.headright {
    text-align: right !important;
    padding: 0 !important;
}

.realtable tbody tr td {
    border-top: none;
    border-bottom: none;
}
@media print {
    * {
        font-size: 12px;
    }
}
</style>
</head>

<body bgcolor="#f6f6f6">
<!-- body -->
<table class="body-wrap" bgcolor="#f6f6f6">
    <tr>
        <td></td>
        <td class="container" bgcolor="#FFFFFF">

            <!-- remittance -->
            <div class="content head">
                <table>
                    <tr>
                        <td style="width: 50%">
                            <h2>The Techknow Space Inc.</h2>
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
                            <h2>Account Statement - {{ $location }}</h2>
                            <p>
                                Account Number: {{ $account->AccountNumber }}<br/>
                                Balance: ${{ $account->AccountBalance - 0}}<br/>
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
                                <h2>Account Summary</h2>
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
                                        New Charges: <br/>
                                        Credits / Payments: <br/>
                                        =======================<br/>
                                        New Balance: <br/>
                                    </td>

                                    <td class="headright" style="width: 25%">
                                        ${{ $account->AccountBalance - $newCharges }}<br/>
                                        ${{ $newCharges }}<br/>
                                        ${{ $newPayments }}<br/>
                                        =======================<br/>
                                        ${{ $account->AccountBalance - 0 }}
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
                                <h2>Transaction Activity</h2>
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

                <tfoot>
                    <tr>
                        <td style="border-left: none; border-right: none;"></td>
                        <td style="border-left: none; border-right: none;"></td>
                        <td style="border-left: none; text-align: right; padding-right: 5px;">Total</td>
                        <td>$0.00</td>
                        <td>$0.00</td>
                        <td>${{ $account->AccountBalance - 0 }}</td>
                    </tr>
                </tfoot>

                <tbody>
                    @foreach($transactions as $transactionId => $transaction)
                        <tr>
                            <td>TR-{{ $transactionId }}<br/>
                                &nbsp;&nbsp;&nbsp;{{ $transaction['entry']->FormattedDate }}</td>
                            <td></td>
                            <td>
                                @foreach($transaction['orderentry'] as $lineItem)
                                    {{ $lineItem->Description }}<br/>
                                    @if($lineItem->Comment)
                                        &nbsp;&nbsp;-&nbsp;{{ $lineItem->Comment }}<br/>
                                    @endif
                                @endforeach
                            </td>
                            <td>${{ $transaction['entry']->Amount - 0 }}</td>
                            <td></td>
                            <td>${{ $transaction['newBalance'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>

        </td>
        <td></td>
    </tr>
</table>
<!-- /body -->

</body>
</html>