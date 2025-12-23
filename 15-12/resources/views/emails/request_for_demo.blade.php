<html>

<body>
    <style>
        table td {
            border-collapse: collapse !important;
        }
    </style>

    <section class="section-pad">
        <center>

            <table style="width: 40%; border-collapse: collapse !important;">
                <tr>
                    <td style="text-align: center; border: 1px solid #1f3762;">
                        <img style="margin-bottom: 10px;" height="100px" src="https://salexo.in/assets/images/logo.png"
                            alt="Salexpo" />
                    </td>
                </tr>
            </table>

            <table style="width: 40%; border-collapse: collapse !important;">
                <tr style="border: 1px solid #1f3762;">
                    <td style="padding: 6px 5px; width: 35%; color: #1f3762;">Company Name</td>
                    <td style="width: 3%; color: #1f3762;">:</td>
                    <td style="width: 72%; color: #1f3762;">{{ $data['company_name'] }}</td>
                </tr>
                <tr style="border: 1px solid #1f3762;">
                    <td style="padding: 6px 5px; width: 25%; color: #1f3762;">Contact Person</td>
                    <td style="width: 3%; color: #1f3762;">:</td>
                    <td style="width: 72%; color: #1f3762;">{{ $data['contact_person_name'] }}</td>
                </tr>
                <tr style="border: 1px solid #1f3762;">
                    <td style="padding: 6px 5px; width: 25%; color: #1f3762;">Mobile</td>
                    <td style="color: #1f3762;">:</td>
                    <td style="color: #1f3762;">{{ $data['mobile'] }}</td>
                </tr>
                <tr style="border: 1px solid #1f3762;">
                    <td style="padding: 6px 5px; width: 25%; color: #1f3762;">Situable time</td>
                    <td style="color: #1f3762;">:</td>
                    <td style="color: #1f3762;">{{ $data['situable_time'] }}</td>
                </tr>
            </table>

        </center>
    </section>
</body>

</html>
