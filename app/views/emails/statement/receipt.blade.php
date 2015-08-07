<pre>
The TechKnow Space
905 897 9860

INVOICE #:		{{ $transactionNumber }}
WORK ORDER #:		{{ $orderEntry[0]->OrderID }}
ACCOUNT #:		{{ $account->AccountNumber }}

Date:			{{ $transaction->Time }}
Bill To:		{{ $account->FirstName }} {{ $account->LastName }} - {{ $account->Company }}
			{{ $account->PhoneNumber }} - {{ $account->EmailAddress }}

DESCRIPTION &amp; COMMENTS						AMOUNT

@foreach($orderEntry as $lineItem)
{{ $lineItem->Description }}						      ${{ $lineItem->Price - 0 }}
@if($lineItem->Comment)
   {{ $lineItem->Comment }}
@endif

@endforeach
======================================================================
						Subtotal      ${{ $order->Total - $order->Tax }}
						 HST 13%      ${{ $order->Tax - 0 }}
						   Total      ${{ $order->Total - 0 }}
						{{-- Tendered --}}



			Thank you for choosing
			  The TechKnow Space
		   All Digital Repairs Under 1 Roof
		       http://techknowspace.com


</pre>
