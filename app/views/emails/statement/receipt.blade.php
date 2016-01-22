<div>
<pre style="max-width: 500px;">
			The TechKnow Space Inc.
			  Accounts Receivable
		      33 City Centre Dr Unit #142
			Mississauga, ON L5B 2N5
			     905 897 9860

			   HST#: 81032-4079

<b>ACCOUNT #:</b>		{{ $account->AccountNumber }}
<b>INVOICE #:</b>		{{ $transactionNumber }}
@if(isset($orderEntry))
<b>WORK ORDER #:</b>		{{ $orderEntry[0]->OrderID }}
@endif

<b>Bill To:</b>		{{ $account->FirstName }} {{ $account->LastName }} - {{ $account->Company }}
@if(isset($account->CustomField1))
		ATT: {{ $account->CustomField1 }}
@endif
<b>Date:</b>			{{ $transaction->Time }}

<b>DESCRIPTION &amp; COMMENTS</b>					<b>AMOUNT</b>

@if(isset($orderEntry))
@foreach($orderEntry as $lineItem)
{{ $lineItem->Description }}		<span style="display: inline-block; float: right;">${{ number_format((float) $lineItem->Price, 2, '.', '') }}</span>
@if($lineItem->Comment)
   {{ $lineItem->Comment }}
@endif

@endforeach
@endif

@if(isset($lineItems))
@foreach($lineItems as $lineItem)
{{ $lineItem->Description }}			<span style="display: inline-block; float: right;">${{ number_format((float) $lineItem->Price, 2, '.', '') }}</span>
@if($lineItem->Comment)
   {{ $lineItem->Comment }}
@endif

@endforeach
@endif
=====================================================================
			<b>Subtotal</b>      ${{ number_format((float) $order->Total - $order->Tax, 2, '.', '') }}
			 <b>HST 13%</b>      ${{ number_format((float) $order->Tax, 2, '.', '') }}
			   <b>Total</b>      ${{ number_format((float) $order->Total, 2, '.', '') }}
			{{-- <b>Tendered</b>      ${{ number_format((float) $tender[0]->Amount, 2, '.', '') }} - {{ $tender[0]->Description }} --}}
		   {{-- <b>Remaining</b> --}}

			Thank you for choosing
			  The TechKnow Space
		   All Digital Repairs Under 1 Roof
		       http://techknowspace.com

</pre>
</div>