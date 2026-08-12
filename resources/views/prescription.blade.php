<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<style>
    @page {
        size: letter portrait;
        margin: 0.5in;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: "Times New Roman", serif;
        font-size: 17pt;
        color: #000;
    }

    .page {
        width: 5.5in;
        height: 9in;
        margin: 0 auto;
        padding: 0.15in;
        position: relative;
    }
    /* HEADER */
    .header {
        text-align: center;
        border-bottom: 2px double #000;
        padding-bottom: 10px;
        margin-top: 10px;
        margin-bottom: 15px;
    }

    .doctor-name {
        font-size: 23pt;
        font-weight: bold;
        margin-bottom: 3px;
    }

    .doctor-specialization {
        font-size: 15pt;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .clinic-info {
        text-align: left;
        font-size: 15pt;
        line-height: 1.4;
    }

    /* PATIENT INFO */
    .patient-section {
        margin-bottom: 15px;
    }

    .patient-row {
        width: 100%;
        margin-bottom: 8px;
        clear: both;
    }

    .left {
        float: left;
        width: 68%;
    }

    .right {
        float: right;
        width: 30%;
        text-align: right;
    }

    .label {
        font-weight: bold;
        font-size: 15pt;
    }

    .value {
        display: inline-block;
        font-size: 15pt;
        border-bottom: 1px solid #000;
        min-width: 180px;
        padding-bottom: 2px;
        margin-left: 5px;
    }

    .small-value {
        min-width: 50px;
    }

    /* RX */
    .rx-symbol {
        font-size: 30pt;
        font-weight: bold;
        margin: 15px 0;
    }

    /* MEDICATIONS */
    .medications-container {
        padding-bottom: 1.5in;
    }

    .medication-block {
        margin-bottom: 22px;
        padding-bottom: 16px;
        border-bottom: 1px solid #ccc;
        page-break-inside: avoid;
    }

    .medication-name {
        font-size: 17pt;
        font-weight: bold;
        margin-bottom: 8px;
        word-wrap: break-word;
        line-height: 1.4;
    }

    .medication-detail {
        font-size: 16pt;
        margin-left: 20px;
        margin-bottom: 6px;
        line-height: 1.5;
    }

    .medication-note {
        font-size: 16pt;
        font-style: italic;
        margin-left: 20px;
        line-height: 1.5;
    }

    /* SIGNATURE FIXED BOTTOM RIGHT */
    .signature-section {
        position: absolute;
        bottom: 0.3in;
        right: 0.1in;
        text-align: center;
        width: 2.7in;
    }

    .signature-line {
        width: 100%;
        border-top: 1px solid #000;
        margin-bottom: 5px;
    }

    .signature-name {
        font-size: 15pt;
        font-weight: bold;
    }

    .signature-lic {
        font-size: 14pt;
    }

    /* PAGE BREAK */
    .page-break {
        page-break-before: always;
    }

    .clearfix {
        clear: both;
    }
</style>
</head>
<body>

@php
    $perPage = 5;
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

        <div class="clearfix"></div>

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

        <div class="clearfix"></div>

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