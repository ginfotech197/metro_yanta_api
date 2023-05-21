<br>
<h1 style="align-content: center; text-align: center">30 DAYS STATEMENT</h1>
<br>

<h3>
    <b>Terminal: {{$transactions[0]->terminal_name}}</b><br>
    <b>From: {{$sentBy}}</b>
</h3>

<br><br>

<table class="table table-bordered">
    <thead>
    <tr>
        <th style="background-color:#0d3349;color:white; font-size:12px !important; width: 20px;">SL</th>
        <th style="background-color:#0e90d2;color:white; font-size:12px !important; width: 200px;">Description</th>
        <th style="background-color:#0e90d2;color:white; font-size:12px !important; width: 120px;">Date and Time</th>
        <th style="background-color:#0e90d2;color:white; font-size:12px !important; width: 200px;">Game Name</th>
        <th style="background-color:#0e90d2;color:white; font-size:12px !important; width: 200px;">Barcode</th>
        <th style="background-color:#0e90d2;color:white; font-size:12px !important; width: 200px;">Opening balance</th>
        <th style="background-color:#0e90d2;color:white; font-size:12px !important; width: 200px;">Recharged</th>
        <th style="background-color:#0e90d2;color:white; font-size:12px !important; width: 200px;">Played</th>
        <th style="background-color:#0e90d2;color:white; font-size:12px !important; width: 200px;">Prize</th>
        <th style="background-color:#0e90d2;color:white; font-size:12px !important; width: 200px;">Closing Balance</th>
    </tr>
    </thead>
    <tbody>
    @foreach($transactions as $data)
        <tr>
            <td style="text-align: center;">{{$loop->iteration}}</td>
            <td style="text-align: center;">{{$data->description}}</td>
            <td style="text-align: center;">{{$data->date}} ({{$data->time}})</td>
            <td style="text-align: center;">{{$data->game_name}}</td>
            <td style="text-align: center;">{{$data->barcode_number}}</td>
            <td style="text-align: center;">{{$data->old_amount}}</td>
            <td style="text-align: center;">{{$data->recharged_amount}}</td>
            <td style="text-align: center;">{{$data->played_amount}}</td>
            <td style="text-align: center;">{{$data->prize_amount}}</td>
            <td style="text-align: center;">{{$data->new_amount}}</td>

{{--            <td style="text-align: center;">{{$data}}</td>--}}
        </tr>
    @endforeach
    </tbody>
</table>
