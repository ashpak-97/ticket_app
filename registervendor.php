<?php include 'includes/common.php' ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Quote Master</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet" href="plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="plugins/toastr/toastr.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>

<body>
    <div>
        <!-- Main content -->
        <section class="content mt-2">
            <div class="container-fluid">


                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title"><strong>Lets Define Your Coverage.</strong> </h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="state">Please select the state from the dropdown. </label>
                                    <select name="state" class="form-control" id="state">
                                        <?php
                                        $q = "SELECT DISTINCT(state) FROM `areas`";
                                        $r = sql_query($q);
                                        while ($a = sql_fetch_assoc($r)) {
                                            echo '<option value="' . $a['state'] . '">' . $a['state'] . '</option>';
                                        }

                                        ?>

                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="state">Click on counties you want to exclude from your coverage </label>
                                    <select name="county_name[]" class="duallistbox" multiple="multiple" id="county_name">


                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="state">Click on cities you want to excude from your coverage</label>
                                    <select name="city[]" class="duallistbox" multiple="multiple" id="city">


                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Click on zips you want to exclude from your coverage </label>
                                    <select class="duallistbox" multiple="multiple" id="zips" name="zips[]">

                                    </select>
                                </div>
                                <!-- /.form-group -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.card-body -->

                </div>
                <!-- /.card -->


                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


    <!-- Control Sidebar -->

    <!-- /.control-sidebar -->

    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 -->
    <script src="plugins/select2/js/select2.full.min.js"></script>
    <!-- Bootstrap4 Duallistbox -->
    <script src="plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
    <!-- InputMask -->
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
    <!-- date-range-picker -->
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap color picker -->
    <script src="plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Toastr -->
    <script src="plugins/toastr/toastr.min.js"></script>
    <!-- Bootstrap Switch -->
    <script src="plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>
    <!-- Page script -->
    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            //Datemask dd/mm/yyyy
            $('#datemask').inputmask('dd/mm/yyyy', {
                'placeholder': 'dd/mm/yyyy'
            })
            //Datemask2 mm/dd/yyyy
            $('#datemask2').inputmask('mm/dd/yyyy', {
                'placeholder': 'mm/dd/yyyy'
            })
            //Money Euro
            $('[data-mask]').inputmask()

            //Date range picker
            $('#reservation').daterangepicker()
            //Date range picker with time picker
            $('#reservationtime').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
                locale: {
                    format: 'MM/DD/YYYY hh:mm A'
                }
            })
            //Date range as a button
            $('#daterange-btn').daterangepicker({
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function(start, end) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
                }
            )

            //Timepicker
            $('#timepicker').datetimepicker({
                format: 'LT'
            })

            //Bootstrap Duallistbox
            $('.duallistbox').bootstrapDualListbox()

            //Colorpicker
            $('.my-colorpicker1').colorpicker()
            //color picker with addon
            $('.my-colorpicker2').colorpicker()

            $('.my-colorpicker2').on('colorpickerChange', function(event) {
                $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
            });

            $("input[data-bootstrap-switch]").each(function() {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });

        })
        $(document).ready(function() {

            $(document).on('change', '#state', function() {
                let state = $('#state').val();
                $.ajax({
                    url: 'api/get_countys.php',
                    method: 'POST',
                    data: {
                        state: state

                    },
                    success: function(res) {
                        console.log(res);
                        var dataObj = res;
                        $('#county_name').empty();

                        for (i = 0; i < dataObj.length; i++) {
                            var x = document.getElementById("county_name");
                            var option = document.createElement("option");
                            option.text = dataObj[i].county_name;
                            option.value = dataObj[i].county_name;
                            x.add(option);
                        }
                        $('.duallistbox').bootstrapDualListbox('refresh', true);

                    },
                    error: function(err) {
                        console.log(err);

                    }
                });

                $.ajax({
                    url: 'api/get_citys.php',
                    method: 'POST',
                    data: {

                        state: state

                    },
                    success: function(res) {
                        console.log(res);
                        var dataObj = res;
                        $('#city').empty();

                        for (i = 0; i < dataObj.length; i++) {
                            var x = document.getElementById("city");
                            var option = document.createElement("option");
                            option.text = dataObj[i].city;
                            option.value = dataObj[i].city;
                            x.add(option);

                        }
                        $('.duallistbox').bootstrapDualListbox('refresh', true);

                    },
                    error: function(err) {
                        console.log(err);

                    }
                });

                $.ajax({
                    url: 'api/get_zips.php',
                    method: 'POST',
                    data: {

                        state: state


                    },
                    success: function(res) {
                        console.log(res);
                        var dataObj = res;
                        $('#zips').empty();

                        for (i = 0; i < dataObj.length; i++) {
                            var x = document.getElementById("zips");
                            var option = document.createElement("option");
                            option.text = dataObj[i].zip;
                            option.value = dataObj[i].id;
                            x.add(option);
                        }
                        $('.duallistbox').bootstrapDualListbox('refresh', true);

                    },
                    error: function(err) {
                        console.log(err);

                    }
                });




            });

            $(document).on('change', '#county_name', function() {
                let county_name = $('#county_name').val();
                let state = $('#state').val();
                //console.log(county_name);
                $.ajax({
                    url: 'api/get_citys.php',
                    method: 'POST',
                    data: {
                        county_name: county_name,
                        state: state

                    },
                    success: function(res) {
                        console.log(res);
                        var dataObj = res;
                        $('#city').empty();

                        for (i = 0; i < dataObj.length; i++) {
                            var x = document.getElementById("city");
                            var option = document.createElement("option");
                            option.text = dataObj[i].city;
                            option.value = dataObj[i].city;
                            x.add(option);

                        }
                        $('.duallistbox').bootstrapDualListbox('refresh', true);

                    },
                    error: function(err) {
                        console.log(err);

                    }
                });


                $.ajax({
                    url: 'api/get_zips.php',
                    method: 'POST',
                    data: {
                        state: state,
                        county_name: county_name

                    },
                    success: function(res) {
                        console.log(res);
                        var dataObj = res;
                        $('#zips').empty();

                        for (i = 0; i < dataObj.length; i++) {
                            var x = document.getElementById("zips");
                            var option = document.createElement("option");
                            option.text = dataObj[i].zip;
                            option.value = dataObj[i].id;
                            x.add(option);
                        }
                        $('.duallistbox').bootstrapDualListbox('refresh', true);

                    },
                    error: function(err) {
                        console.log(err);

                    }
                });

            });

            $(document).on('change', '#city', function() {
                let city = $('#city').val();
                let county_name = $('#county_name').val();
                let state = $('#state').val();
                $.ajax({
                    url: 'api/get_zips.php',
                    method: 'POST',
                    data: {
                        city: city,
                        state: state,
                        county_name: county_name

                    },
                    success: function(res) {
                        console.log(res);
                        var dataObj = res;
                        $('#zips').empty();

                        for (i = 0; i < dataObj.length; i++) {
                            var x = document.getElementById("zips");
                            var option = document.createElement("option");
                            option.text = dataObj[i].zip;
                            option.value = dataObj[i].id;
                            x.add(option);
                        }
                        $('.duallistbox').bootstrapDualListbox('refresh', true);

                    },
                    error: function(err) {
                        console.log(err);

                    }
                });
            });

        });
    </script>
</body>

</html>