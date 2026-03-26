<div style="text-align: center; line-height: 0.55em;">
    <h1>Black Diamond Carwash</h1>
    <p>2932 Finlandia St. Lorem Ipsum Address</p>
    <p>VAT REG. TIN: XXX-XXX-XXX-XXXXX</p>
</div><br>
<div style="text-align: left; line-height: 0.55em;">
    <p>Date and Time Generated:<b>
            @php
                echo \Carbon\Carbon::now()->format('F j, Y h:i A');
            @endphp
        </b>
    </p>
    <p>Issued by: <b>{{ Auth::user()->name }}</b></p>
</div><br>
