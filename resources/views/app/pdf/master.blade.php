<!--mpdf
<htmlpageheader name="header">
    <div class="header-wrap">
      <table class="header">
        <tbody>
          <tr>
            <td class="header-company">
              <div class="header-company_name">{{$company_name}}</div>
              <div class="header-company_details">
                @if(isset($company_address))
                <pre>{{$company_address}}</pre>
                @endif
                @if(isset($company_email))
                    <p>Email: {{$company_email}}</p>
                @endif
                @if(isset($company_telephone))
                    <p>Tel: {{$company_telephone}}</p>
                @endif
                @if(isset($company_fax))
                    <p>Fax: {{$company_fax}}</p>
                @endif
                @if(isset($tax_enable) && $tax_enable == 1)
                    <p>{{$registration_label}}: {{$company_tax_id}}</p>
                @endif
              </div>
            </td>
            @if(isset($company_logo))
            <td class="header-logo">
                <img src="{{url('storage/'.$company_logo)}}" class="brand-logo" />
            </td>
            @endif
          </tr>
        </tbody>
      </table>
    </div>
</htmlpageheader>

<sethtmlpageheader name="header" show-this-page="1" />
mpdf-->
<style>
    .w-1 {
      width: 4.166667%;
    }

    .w-2 {
      width: 8.333334%;
    }

    .w-3 {
      width: 12.500001%;
    }

    .w-4 {
      width: 16.666668%;
    }

    .w-5 {
      width: 20.833335%;
    }

    .w-6 {
      width: 25.000002%;
    }

    .w-7 {
      width: 29.166669%;
    }

    .w-8 {
      width: 33.333336%;
    }

    .w-9 {
      width: 37.500003%;
    }

    .w-10 {
      width: 41.66667%;
    }

    .w-11 {
      width: 45.833337%;
    }

    .w-12 {
      width: 50.000004%;
    }

    .w-13 {
      width: 54.166671%;
    }

    .w-14 {
      width: 58.333338%;
    }

    .w-15 {
      width: 62.500005%;
    }

    .w-16 {
      width: 66.666672%;
    }

    .w-17 {
      width: 70.833339%;
    }

    .w-18 {
      width: 75.000006%;
    }

    .w-19 {
      width: 79.166673%;
    }

    .w-20 {
      width: 83.33334%;
    }

    .w-21 {
      width: 87.500007%;
    }

    .w-22 {
      width: 91.666674%;
    }

    .w-23 {
      width: 95.833341%;
    }

    .w-24 {
      width: 100.000008%;
    }

    .align-right {
      text-align: right;
    }

    .align-center {
      text-align: center;
    }

    .align-left {
      text-align: left;
    }

    table {
      border-collapse: collapse;
      vertical-align: top;
    }
    p {
        margin: 0;
        padding: 0;
    }
    .t-img {
      display: block;
      max-width: 100%;
    }
    body {
        font-family: sans-serif;
        font-size: 9pt;
        color: #484746;
    }
    pre {
        font-family: sans-serif;
    }

    table {
        border-spacing: 0;
        width: 100%;
        border-collapse: collapse;
    }
    th,
    td{
        font-weight: normal;
        vertical-align: top;
        text-align: left;
    }
    .header-wrap {
        z-index: 22;
        padding: 50px;
    }
    .header-logo {
        width: 25%;
        text-align: right;
    }
    .brand-logo {
        padding: 1px;
        max-height: 70px;
    }
    .header-company_name {
        font-size: 16pt;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .content {
        padding: 0px 25px;
    }
    .content-title {
        text-transform: uppercase;
        margin-bottom: 12px;
        padding: 0px;
        text-align: center;
        font-size: 12pt;
        font-weight: bold;
        border: 0.1mm solid #484746;
    }

    .document-blue {
        color: #3aa3e3;
    }

    .document-orange {
        color: #FF9800;
    }

    .document-red {
        color: #E75650;
    }

    .document-blue_light {
        color: #48606f;
    }
    .document-green {
        color: #66bb6a;
    }
    .summary-address {
        width: 33.333%;
    }
    .summary-empty {
        width: 33.333%;
    }
    .summary-info {
        width: 33.333%;
    }
    .info td {
        text-align: right;
    }
    .info td:nth-child(2n) {
        padding-left: 15px;
    }
    .items {
        margin-top: 12px;
        border: 0.1mm solid #484746;
    }
    .items thead th {
        padding: 6px 3px;
        background: #f1f1f1;
        border: 0.1mm solid #484746;
    }
    .items tbody td {
        border: 0.1mm solid #484746;
        padding: 3px;
    }

    .items tfoot td {
        border: 0.1mm solid #484746;
        text-align: right;
        padding: 4px 3px;
    }
    .item-empty {

    }
    .ar {
        text-align: right;
    }
    .ac {
        text-align: center;
    }
    .terms {
        margin-top: 20px;
    }
    .terms-description {
        width: 70%;
    }
    .footer-wrap {
        padding: 50px;
    }
    .footer {
        padding: 3px 0;
        text-align: center;
        border: 0.1mm solid #484746;
    }
    .sign {
    	margin-top: 15px;
    	border-collapse: collapse;
    	border: 0.1mm solid #484746;
    }
    .sign td {
    	border: 0.1mm solid #484746;
    }
    .sign-table {
    	margin: 0;
    	width: 100%;
    	border-collapse: collapse;
    }
    .sign-table td {
    	border: none;
    }

</style>
<div class="content">
    @yield('content')
</div>
