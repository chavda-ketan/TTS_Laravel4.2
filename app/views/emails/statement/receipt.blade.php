<pre>
The TechKnow Space Inc.
Accounts Receivable
33 City Centre Dr Unit #142
Mississauga, ON L5B 2N5
905 897 9860

INVOICE #:		{{ $transactionNumber }}
@if(isset($orderEntry))
WORK ORDER #:		{{ $orderEntry[0]->OrderID }}
@endif
ACCOUNT #:		{{ $account->AccountNumber }}

Date:			{{ $transaction->Time }}
Bill To:		{{ $account->FirstName }} {{ $account->LastName }} - {{ $account->Company }}
			{{ $account->PhoneNumber }} - {{ $account->EmailAddress }}

DESCRIPTION &amp; COMMENTS						AMOUNT

@if(isset($orderEntry))
@foreach($orderEntry as $lineItem)
{{ $lineItem->Description }}						      ${{ $lineItem->Price - 0 }}
@if($lineItem->Comment)
   {{ $lineItem->Comment }}
@endif

@endforeach
@endif

@if(isset($lineItems))
@foreach($lineItems as $lineItem)
{{ $lineItem->Description }}						      ${{ $lineItem->Price - 0 }}
@if($lineItem->Comment)
   {{ $lineItem->Comment }}
@endif

@endforeach
@endif
======================================================================
						Subtotal      ${{ $transaction->Total - $transaction->SalesTax }}
						 HST 13%      ${{ $transaction->SalesTax - 0 }}
						   Total      ${{ $transaction->Total - 0 }}


			Thank you for choosing
			  The TechKnow Space
		   All Digital Repairs Under 1 Roof
		       http://techknowspace.com


</pre>
