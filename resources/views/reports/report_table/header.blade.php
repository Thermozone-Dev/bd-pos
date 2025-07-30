<div style="text-align: center; line-height: 0.55em;">
    <h1>Clark Nature Park, Inc.</h1>
    <p>Gil Puyat Avenue, Clark Civil Aviation Complex, Clark Freeport Zone, Pampanga, Philippines</p>
    <p>TIN: 007 287 877 000</p>
</div><br>
<div style="text-align: left; line-height: 0.55em;">
    <p>Software Name: <b>POS Sikat v1.0</b></p>
    <p>Serial Number: <b>XXXXXXXXXX</b></p>
    <p>Machine Identification Number: <b>XXXXXXXXXX</b></p>
    <p>POS Terminal Number: <b>XXX</b></p>

    <p>Date and Time Generated:<b>
            @php
                echo \Carbon\Carbon::now()->format('F j, Y h:i A');
            @endphp
        </b>
    </p>
    <p>Issued by: <b>{{ Auth::user()->name }}</b></p>
</div><br>
