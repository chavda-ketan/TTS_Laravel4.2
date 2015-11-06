<pre>
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
{{ $lineItem->Description }}					      ${{ number_format((float) $lineItem->Price, 2, '.', '') }}
@if($lineItem->Comment)
   {{ $lineItem->Comment }}
@endif

@endforeach
@endif

@if(isset($lineItems))
@foreach($lineItems as $lineItem)
{{ $lineItem->Description }}					      ${{ number_format((float) $lineItem->Price, 2, '.', '') }}
@if($lineItem->Comment)
   {{ $lineItem->Comment }}
@endif

@endforeach
@endif
======================================================================
						<b>Subtotal</b>      ${{ number_format((float) $transaction->Total - $transaction->SalesTax, 2, '.', '') }}
						 <b>HST 13%</b>      ${{ number_format((float) $transaction->SalesTax, 2, '.', '') }}
						   <b>Total</b>      ${{ number_format((float) $transaction->Total, 2, '.', '') }}


			Thank you for choosing
			  The TechKnow Space
		   All Digital Repairs Under 1 Roof
		       http://techknowspace.com

</pre>
