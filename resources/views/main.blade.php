<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <title>DWP BERCA</title>
</head>

<style>
    .w-100 {
        width: 100%;
    }

    .fw-600 {
        font-weight: 600;
    }

    .b {
        border: 1px solid;
    }

    .bb {
        border-bottom: 2px solid;
    }
</style>

<body>
    <div class="row p-4">
        <div class="col-2 b">
            <span class="fw-600">Marketing</span>
        </div>
        <div class="col-2 b">
            <span class="fw-600">Bulan</span>
        </div>
        <div class="col-3 b">
            <span class="fw-600">Omzet</span>
        </div>
        <div class="col-2 b">
            <span class="fw-600">Komisi %</span>
        </div>
        <div class="col-3 b">
            <span class="fw-600">Komisi Nasional</span>
        </div>
        <div id="data-table">
            <!-- APPEND BY JS -->
        </div>
    </div>
    <div class="row p-4">
        <div class="col-6">
            <span class="fw-600">Total Balance</span>
        </div>
        <div class="col-2">
            <span class="fw-600">:</span>
        </div>
        <div class="col-4">
            <span id="balance" class="fw-600"></span>
        </div>
        <div class="col-6 mb-1">
            <span class="fw-600">Cargo Fee</span>
        </div>
        <div class="col-2 mb-1">
            <span class="fw-600">:</span>
        </div>
        <div class="col-4 mb-1">
            <span id="fee" class="fw-600"></span>
        </div>
        <div class="col-12 bb"></div>
        <div class="col-6 mt-1">
            <span class="fw-600">Cargo Fee</span>
        </div>
        <div class="col-2 mt-1">
            <span class="fw-600">:</span>
        </div>
        <div class="col-4 mt-1">
            <span id="total" class="fw-600"></span>
        </div>
        <div class="col-12 mt-3">
            <button class="btn btn-primary w-100" onclick="payment()">Pay</button>
        </div>
    </div>
</body>

</html>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function showOmzet() {
        var tokenVal = $('meta[name="csrf-token"]').attr('content');

        var fd = new FormData();

        fd.append("_token", tokenVal);

        $.ajax({
            type: "GET",
            url: "/get_omzet",
            data: fd,
            enctype: 'multipart/form-data',
            cache: false,
            processData: false,
            async: false,
            contentType: false,
            success: function(response) {
                console.log(response);

                for (var i = 0; i <= response.length; i++) {
                    var html = `<div class="row">
                        <div class="col-2 b">
                            <span class="fw-600">` + response[i].nama + `</span>
                        </div>
                        <div class="col-2 b">
                            <span class="fw-600">` + response[i].bulan + `</span>
                        </div>
                        <div class="col-3 b">
                            <span class="fw-600">` + response[i].omzet2 + `</span>
                        </div>
                        <div class="col-2 b">
                            <span class="fw-600">` + response[i].komisi + `%</span>
                        </div>
                        <div class="col-3 b">
                            <span class="fw-600">` + response[i].komisi_nominal + `</span>
                        </div>
                    </div>`;

                    $("#data-table").append(html);
                }
            },
            error: function(response) {
                alert("GAGAL MENAMPILKAN OMZET!");
            }
        });
    }

    showOmzet();
</script>

<script>
    let balance = 100000000;
    let fee = 10000;
    let total = balance + fee;
    let newBalance = balance.toLocaleString('id-ID');
    let newFee = fee.toLocaleString('id-ID');
    let newTotal = total.toLocaleString('id-ID');

    $("#balance").text(newBalance);
    $("#fee").text(newFee);
    $("#total").text(newTotal);

    function payment() {
        let id_card = generateRandomNumber();
        var tokenVal = $('meta[name="csrf-token"]').attr('content');

        var fd = new FormData();

        fd.append("id_card", id_card);
        fd.append("total_payment", total);

        fd.append("id", 14);
        fd.append("transaction_number", 0);
        fd.append("marketing_id", 4);
        fd.append("date", "2024-03-07");
        fd.append("cargo_fee", fee);
        fd.append("total_balance", balance);
        fd.append("grand_total", total);

        fd.append("_token", tokenVal);

        $.ajax({
            type: "POST",
            url: "/insert_payment",
            data: fd,
            enctype: 'multipart/form-data',
            cache: false,
            processData: false,
            async: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    title: 'PAYMENT SUCCESS!',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer) {
                        window.location.reload();
                    }
                });
            },
            error: function(response) {
                alert("PAYMENT FAILED!");
            }
        });
    }

    function generateRandomNumber() {
        return Math.floor(Math.random() * Math.pow(10, 10));
    }
</script>