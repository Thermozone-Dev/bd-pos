<div style="text-align: center; line-height: 0.55em;">
    <h1>Thermozone Philippines Corporation</h1>
    <p>2286 Marconi St. San Isidro Makati City</p>
    <p>VAT REG. TIN: 223-661-818-00000</p>
</div><br>
<div style="text-align: left; line-height: 0.55em;">
    <p>Software Name: <b>POS Sikat v1.0</b></p>
    <p>Serial No: <b>XXXXXXXXXX</b></p>
    <p>MIN: <b>XXXXXXXXXX</b></p>
    <p>POS Terminal Number: <b>XXX</b></p>

    <p>Date and Time Generated:<b>
            @php
                echo \Carbon\Carbon::now()->format('F j, Y h:i A');
            @endphp
        </b>
    </p>
    <p>Issued by: <b>{{ Auth::user()->name }}</b></p>
</div><br>
