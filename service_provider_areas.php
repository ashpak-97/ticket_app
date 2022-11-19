<?php
session_start();
include 'includes/common.php';
if (!isset($_SESSION['admin'])) {
    header('location: ' . url1 . '/adminlogin.php');
}
?>
<?php include 'header.php' ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Service Providers Areas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Service Providers Areas</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="form-group">
                                    <label for="sel1">Select Service Providers</label>
                                    <select class="form-control" id="service_provider_id">
                                        <option value="0">Please select the Service Providers</option>
                                        <?php
                                        $q = "SELECT * FROM `service_providers`";
                                        $r = sql_query($q);
                                        while ($row = mysqli_fetch_assoc($r)) {
                                            echo ' <option value="' . $row['id'] . '">' . $row['First_name'] . ' ' . $row['Last_name'] . '</option>';
                                        }

                                        ?>

                                    </select>
                                </div>

                            </div>
                            <div class="card-body">

                                <div id="alert_message"></div>
                                <table class="table table-hover " id="cTable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Zip</th>
                                            <th>Zip Code Name</th>
                                            <th>City</th>
                                            <th>State</th>
                                            <th>County Name</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>


            </div>
    </section>
</div>
<script>
    $(document).ready(function() {
        // fetch_data();

        function fetch_data() {
            var dataTable = $('#cTable').DataTable({
                "processing": true,
                "serverSide": true,
                "searching": true,
                "order": [],
                "scrollX": true,
                "ajax": {
                    url: "api/service_providers_crud.php",
                    type: "POST",
                    data: function(data) {
                        data.type = 'getall';
                        return data;
                    }
                },
                columns: [{
                        data: "id",
                        render: function(data, type, row, meta) {
                            //console.log(row);


                            return data;
                        }

                    },
                    {
                        data: "dDate",
                        render: function(data, type, row, meta) {
                            //console.log(row);


                            return data;
                        }

                    },
                    {
                        data: "First_name",
                        render: function(data, type, row, meta) {
                            //console.log(row);


                            return data;

                        }

                    },
                    {
                        data: "Last_name",
                        render: function(data, type, row, meta) {
                            //console.log(row);


                            return data;

                        }

                    },
                    {
                        data: "company_name",
                        render: function(data, type, row, meta) {
                            //console.log(row);


                            return data;

                        }

                    },
                    {
                        data: "address",
                        render: function(data, type, row, meta) {
                            //console.log(row);


                            return data;

                        }

                    },
                    {
                        data: "phone",
                        render: function(data, type, row, meta) {
                            //console.log(row);


                            return data;

                        }

                    },
                    {
                        data: "email_address",
                        render: function(data, type, row, meta) {
                            //console.log(row);


                            return data;

                        }

                    },
                    {
                        data: "license_number",
                        render: function(data, type, row, meta) {
                            //console.log(row);


                            return data;

                        }

                    },
                    {
                        data: "pdf_file",
                        render: function(data, type, row, meta) {
                            //console.log(row);


                            return data;

                        }

                    },
                    {
                        data: "dDate_of_expiry",
                        render: function(data, type, row, meta) {
                            //console.log(row);


                            return data;

                        }

                    },
                    {
                        data: "email_verify",
                        render: function(data, type, row, meta) {
                            console.log(row);
                            if (row.email_verify == 0) {
                                return '<div><input type="checkbox" id="chekbox' + row.id +
                                    '" data-id="' + row.id + '" class="approve"></div>';

                            } else {
                                return '<div><input type="checkbox" data-id="' + row.id +
                                    '" checked></div>';

                            }
                        }

                    },
                    {
                        data: "approve",
                        render: function(data, type, row, meta) {
                            console.log(row);
                            if (row.status == 0) {
                                return '<div><input type="checkbox" id="chekbox' + row.id +
                                    '" data-id="' + row.id + '" class="approve"></div>';

                            } else {
                                return '<div><input type="checkbox" data-id="' + row.id +
                                    '" checked></div>';

                            }
                        }

                    },

                    {
                        data: "id",
                        render: function(data, type, row, meta) {
                            //console.log(row);


                            return '<div><button data-id="' + row.id +
                                '" type="button" name="delete" class="btn btn-danger btn-xs delete" id="' +
                                row.id +
                                '"><i class="fas fa-trash"></i></button> <a href="edit_categories.php?catid=' +
                                row.id +
                                '" class="btn btn-success btn-xs edit" id="' +
                                row.id + '" data-id="' + row.id +
                                '"><i class="fas fa-edit"></i></a></div>';
                        }

                    }
                ],
                footerCallback: function(tfoot, data, start, end, display) {
                    var api = this.api();
                    //console.log(api);
                    //console.log(api);


                }


            });
            //console.log(dataTable);

        }
        $(document).on('change', '#service_provider_id', function() {
            console.log($('#service_provider_id').val());
            let id = $('#service_provider_id').val();
            $.ajax({
                url: 'api/get_areas.php',
                method: 'POST',
                data: {
                    id: id
                },
                success: function(res) {
                    console.log(res);
                    let str = '';
                    for (let i = 0; i < res.length; i++) {
                        str += `<tr>
                                    <td>${res[i].zip}</td>
                                    <td>${res[i].zipcode_name}</td>
                                    <td>${res[i].city}</td>
                                    <td>${res[i].state}</td>
                                    <td>${res[i].County_name}</td>
                                </tr>`;


                    }
                    $('#cTable tbody').html(str);

                }

            });


        });


    });
</script>
<?php include 'footer.php' ?>