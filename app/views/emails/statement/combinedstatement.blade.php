@foreach ($statements as $statement)
{{ $statement }}
@endforeach

<table class="body-wrap" bgcolor="#f6f6f6">
    <tr>
        <td></td>
        <td class="container" bgcolor="#FFFFFF">

            <!-- remittance -->
            <div class="content">
                <p style="font-size: 15px">
                    <h3>Total Amount Owing: ${{ $balance }}</h3><br/>
                    Please remit payment to:<br/>
                    The TechKnow Space Inc.<br/>
                    33 City Centre Dr Unit #142<br/>
                    Mississauga, ON L5B 2N5<br/>
                </p>
            </div>
            <!-- /remittance -->

        </td>
        <td></td>
    </tr>
</table>


<table class="body-wrap" bgcolor="#f6f6f6">
    <tr>
        <td></td>
        <td class="container" bgcolor="#FFFFFF">

            <!-- remittance -->
            <div class="content">
                <p style="font-size: 15px">
                    We're pleased to attach our statement for the month of {{ $month }}. This included transactions completed at all our locations.<br/>
                    Please remit your payment in the amount of ${{ $balance }} before {{ $month }} 15th.<br/>
                </p>
            </div>
            <!-- /remittance -->

        </td>
        <td></td>
    </tr>
</table>