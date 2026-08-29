<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<style>
    @page {
        size: A5 portrait;
        margin: 0.3in; /* top right bottom left */
    }

    * {
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: "Times New Roman", serif;
        font-size: 13pt;
        color: #000;
    }

    .page {
        width: 100%;
        position: relative;
    }

    /* HEADER */
    .header {
        text-align: center;
        border-bottom: 1.5px double #000;
        padding-bottom: 7px;
        margin-top: 2px;
        margin-bottom: 9px;
    }

    .doctor-name {
        font-size: 15pt;
        font-weight: bold;
        margin-bottom: 3px;
    }

    .doctor-specialization {
        font-size: 11.5pt;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .clinic-info {
        text-align: center;
        font-size: 11.5pt;
    }

    .clinic-info p {
        margin: 0;
    }

    /* PATIENT INFO */
    .patient-section {
        margin-bottom: 9px;
    }

    .patient-row {
        display: table;
        width: 100%;
        margin-bottom: 5px;
        table-layout: fixed;
        
    }

    .left {
        display: table-cell;
        width: 65%;
        vertical-align: top;
        padding-right: 8px;
    }

    .right {
        display: table-cell;
        width: 35%;
        vertical-align: top;
        text-align: right;
    }

    .label {
        font-weight: bold;
        font-size: 12pt;
    }

    .value {
        display: inline-block;
        font-size: 12pt;
        border-bottom: 0.75px solid #000;
        min-width: 90px;
        padding-bottom: 1px;
        margin-left: 3px;
        word-wrap: break-word;
    }

    .small-value {
        min-width: 33px;
        word-wrap: break-word;
    }

    /* RX */
    .rx-symbol {
        font-size: 18.5pt;
        font-weight: bold;
        margin: 7px 0;
    }

    /* MEDICATIONS */
    .medications-container {
        padding-bottom: 0.55in;
    }

    .medication-block {
        margin-bottom: 11px;
        padding-bottom: 9px;
        border-bottom: 1px solid #ccc;
        page-break-inside: avoid;
    }

    .medication-name {
        font-size: 13.5pt;
        font-weight: bold;
        margin-bottom: 3px;
        word-wrap: break-word;
        line-height: 1.3;
    }

    .medication-detail {
        font-size: 12.5pt;
        margin-left: 12px;
        margin-bottom: 2px;
        line-height: 1.3;
    }

    .medication-note {
        font-size: 12pt;
        font-style: italic;
        margin-left: 12px;
        line-height: 1.3;
    }

    /* SIGNATURE FIXED BOTTOM RIGHT */
    .signature-section {
        position: absolute;
        bottom: 0.25in;
        right: 0;
        text-align: center;
        width: 2.2in;
    }

    .signature-line {
        width: 100%;
        border-top: 1px solid #000;
        margin-bottom: 3px;
    }

    .signature-name {
        font-size: 13pt;
        font-weight: bold;
    }

    .signature-lic {
        font-size: 12pt;
    }

    /* PAGE BREAK */
    .page-break {
        page-break-before: always;
    }
</style>
</head>
<body>

@php
    $perPage = 7;
    $chunks = $prescriptions->chunk($perPage);
@endphp

@foreach ($chunks as $chunkIndex => $chunk)
<div class="page {{ $chunkIndex > 0 ? 'page-break' : '' }}">

    <!-- HEADER -->
    <div class="header">
        <div class="doctor-name">{{ $doctor['name'] }}, M.D.</div>
        <div class="doctor-specialization">{{ $doctor['specialization'] }}</div>

        <div class="clinic-info">
            <p><strong>{{ $clinic['name'] }}</strong></p>

            @if($clinic['address'])
                <p>{{ $clinic['address'] }}</p>
            @endif

            @if($clinic['phone'])
                <p>Tel #: {{ $clinic['phone'] }}</p>
            @endif
        </div>
    </div>

    <!-- PATIENT INFO -->
    <div class="patient-section">

        <div class="patient-row">
            <div class="left">
                <span class="label">Patient Name.:</span>
                <span class="value">{{ $patient['name'] }}</span>
            </div>

            <div class="right">
                <span class="label">Date:</span>
                <span class="value small-value">{{ $consultation['date'] }}</span>
            </div>
        </div>

        <div class="patient-row">
            <div class="left">
                <span class="label">Address:</span>
                <span class="value">{{ $patient['address'] ?? '' }}</span>
            </div>

            <div class="right">
                <span class="label">Age:</span>
                <span class="value small-value">{{ $patient['age'] }}</span>

                <span class="label">Sex:</span>
                <span class="value small-value">{{ $patient['gender'] }}</span>
            </div>
        </div>

    </div>

    <!-- RX SYMBOL -->
    <div class="rx-symbol">Rx</div>

    <!-- MEDICATIONS -->
    <div class="medications-container">
        @foreach ($chunk as $rx)
        <div class="medication-block">
            <div class="medication-name">
                {{ $rx['generic_name'] }}
                @if(!empty($rx['brand_name']))
                    ({{ $rx['brand_name'] }})
                @endif
                {{ $rx['dosage'] }}
            </div>

            <div class="medication-detail">
                {{ $rx['frequency'] }} for {{ $rx['duration'] }}
            </div>

            @if(!empty($rx['instructions']))
            <div class="medication-note">
                {{ $rx['instructions'] }}
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- SIGNATURE -->
    <div class="signature-section">
        <div class="signature-line"></div>
        <div class="signature-name">{{ $doctor['name'] }}, M.D.</div>
        <div class="signature-lic">Lic No.: {{ $doctor['prc_id'] }}</div>
    </div>

</div>
@endforeach

</body>
</html>