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
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background-color: #f1f1f1;
        }

        #regForm {
            background-color: #ffffff;
            margin: 100px auto;
            font-family: Raleway;
            padding: 40px;
            width: 70%;
            min-width: 300px;
        }

        h1 {
            text-align: center;
        }

        input {
            padding: 10px;
            width: 100%;
            font-size: 17px;
            font-family: Raleway;
            border: 1px solid #aaaaaa;
        }

        /* Mark input boxes that gets an error on validation: */
        input.invalid {
            background-color: #ffdddd;
        }

        /* Hide all steps by default: */
        .tab {
            display: none;
        }

        button {
            background-color: #04AA6D;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 17px;
            font-family: Raleway;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.8;
        }

        #prevBtn {
            background-color: #bbbbbb;
        }

        /* Make circles that indicate the steps of the form: */
        .step {
            height: 15px;
            width: 15px;
            margin: 0 2px;
            background-color: #bbbbbb;
            border: none;
            border-radius: 50%;
            display: inline-block;
            opacity: 0.5;
        }

        .step.active {
            opacity: 1;
        }

        /* Mark the steps that are finished and valid: */
        .step.finish {
            background-color: #04AA6D;
        }
    </style>
</head>

<body>
    <div>
        <!-- Main content -->
        <section class="content mt-2">
            <div class="container-fluid">


                <div class="card card-default">
                    <div class="card-header">


                        <a href="../index.php" class="btn btn-primary">Back To Home</a>


                        <!-- The Modal -->
                        <div class="modal" id="myModal">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h4 class="modal-title">Confirmation</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class="modal-body">
                                        <p class="login-box-msg"><strong>Below is a summary of the coverage you have selected , you will receive leads from these areas </strong></p>

                                        <form id="form2">
                                            <p style="text-align: center;font-weight: bold;">State</p>
                                            <table class="table table-striped" id="statetable">

                                                <tbody>

                                                </tbody>
                                            </table>
                                            <p style="text-align: center;font-weight: bold;">County</p>
                                            <table class="table table-striped" id="countytable">

                                                <tbody>

                                                </tbody>
                                            </table>
                                            <p style="text-align: center;font-weight: bold;">City</p>
                                            <table class="table table-striped" id="citytable">

                                                <tbody>

                                                </tbody>
                                            </table>
                                            <p style="text-align: center;font-weight: bold;">Zips</p>
                                            <table class="table table-striped" id="ziptable">

                                                <tbody>

                                                </tbody>
                                            </table>
                                        </form>
                                    </div>

                                    <!-- Modal footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                        <button type="button" id="submitconfirm" class="btn btn-primary">Confirm coverage selection</button>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <!-- <div class="alert alert-warning alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <strong>Warning!</strong> This alert box could indicate a warning that might need attention.
                                </div> -->
                                <form id="regForm" action="register.php" method="POST" onsubmit="return false;" enctype="multipart/form-data">

                                    <h1>Get Leads and Contact Customers</h1>

                                    <!-- One "tab" for each step in the form: -->
                                    <div class="tab">

                                        <p class="login-box-msg"><b>Personal Details</b></p>
                                        <div class="input-group mb-3">
                                            <input type="text" name="first_name" class="form-control" oninput="this.className = 'form-control'" placeholder="First name" autocomplete="off" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-user"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input-group mb-3">
                                            <input type="text" name="last_name" oninput="this.className = 'form-control'" class="form-control" placeholder="Last name" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-user"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input-group mb-3">
                                            <input type="text" name="c_name" oninput="this.className = 'form-control'" class="form-control" placeholder="Company name" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-user"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input-group mb-3">
                                            <input type="number" name="phone" oninput="this.className = 'form-control'" class="form-control" placeholder="Phone" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-user"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="comment">Company Address (Kindly provide actual address and not a post box address )</label>
                                            <textarea rows="4" cols="50" class="form-control " placeholder="Company Address" name="address" id="address" required>
                                            </textarea>
                                        </div>
                                        <!-- <p><input name="first_name" placeholder="First name..." oninput="this.className = ''"></p>
                                        <p><input placeholder="Last name..." oninput="this.className = ''"></p> -->
                                    </div>



                                    <div class="tab">
                                        <p class="login-box-msg"><b>Login Info</b></p>
                                        <div class="input-group mb-3">
                                            <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-envelope"></span>
                                                </div>
                                            </div>

                                        </div>
                                        <h2 id="result"></h2>
                                        <div class="input-group mb-3">
                                            <input type="password" name="password1" id="password1" class="form-control" placeholder="Password" required>
                                            <div class="input-group-append">

                                                <div class="input-group-text">

                                                    <span class="fas fa-lock"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input-group mb-3">
                                            <input type="password" name="password2" id="password2" class="form-control" placeholder="Retype password" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-lock"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <p><input placeholder="dd" oninput="this.className = ''"></p>
                                        <p><input placeholder="mm" oninput="this.className = ''"></p>
                                        <p><input placeholder="yyyy" oninput="this.className = ''"></p> -->
                                    </div>

                                    <div class="tab">
                                        <p class="login-box-msg"><b>Define Your Coverage</b></p>
                                        <!-- <p><input placeholder="Username..." oninput="this.className = ''"></p>
                                        <p><input placeholder="Password..." oninput="this.className = ''"></p> -->
                                        <div class="form-group">
                                            <label for="state">Please select the state from the dropdown. </label>
                                            <select name="state[]" class="form-control select2" data-placeholder="Select a State" class="" multiple="multiple" id="state">

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
                                            <label for="state">Click on counties you want to include in your coverage </label>
                                            <select name="county_name[]" class="form-control select2" multiple="multiple" data-placeholder="Select a County" id="county_name">


                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="state">Click on cities you want to include in your coverage</label>
                                            <select name="city[]" class="form-control select2" data-placeholder="Select a City" multiple="multiple" id="city">


                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label style="color:red ;">Click on zips you want to exclude from your coverage </label>
                                            <select class="form-control select2" multiple="multiple" id="zips" data-placeholder="Select a Zip code that you want to exclude" name="zips[]">

                                            </select>
                                        </div>
                                    </div>

                                    <div style="overflow:auto;">
                                        <div style="float:right;">
                                            <button type="button" id="prevBtn" class="btn btn-success" onclick="nextPrev(-1)">Previous</button>
                                            <button type="button" id="nextBtn" class="btn btn-primary" onclick="nextPrev(1)">Next</button>
                                            <button type="submit" id="submitbtn" class="btn btn-primary" style="visibility: hidden;">Submit</button>
                                        </div>
                                    </div>

                                    <!-- Circles which indicates the steps of the form: -->
                                    <div style="text-align:center;margin-top:40px;">
                                        <span class="step"></span>

                                        <span class="step"></span>
                                        <span class="step"></span>
                                    </div>

                                    <a href="login.php" class="text-center">I already have a membership? Login</a>
                                </form>
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
        const validateEmail = (email) => {
            return email.match(
                /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            );
        };

        const validate = () => {
            const $result = $('#result');
            const email = $('#email').val();
            $result.text('');

            if (validateEmail(email)) {
                $result.text(email + ' is valid :)');
                $result.css('color', 'green');
            } else {
                $result.text(email + ' is not valid :(');
                $result.css('color', 'red');
            }
            return false;
        }

        function myFunction(elem) {
            var x = document.getElementById("password1");
            console.log(elem);
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
        var currentTab = 0; // Current tab is set to be the first tab (0)
        showTab(currentTab); // Display the current tab

        function showTab(n) {
            // This function will display the specified tab of the form...
            var x = document.getElementsByClassName("tab");
            x[n].style.display = "block";
            //... and fix the Previous/Next buttons:
            if (n == 0) {
                document.getElementById("prevBtn").style.display = "none";
            } else {
                document.getElementById("prevBtn").style.display = "inline";
            }
            if (n == (x.length - 1)) {
                document.getElementById("nextBtn").innerHTML = "Submit";
                document.getElementById("nextBtn").style.visibility = "hidden";
                document.getElementById("submitbtn").style.visibility = "visible";


            } else {
                document.getElementById("nextBtn").innerHTML = "Next";
                document.getElementById("nextBtn").style.visibility = "visible";
                document.getElementById("submitbtn").style.visibility = "hidden";
            }
            //... and run a function that will display the correct step indicator:
            fixStepIndicator(n)
        }

        function nextPrev(n) {
            // This function will figure out which tab to display
            var x = document.getElementsByClassName("tab");
            //console.log(x.length);
            // Exit the function if any field in the current tab is invalid:
            if (n == 1 && !validateForm()) return false;
            // Hide the current tab:
            x[currentTab].style.display = "none";
            // Increase or decrease the current tab by 1:
            currentTab = currentTab + n;
            // if you have reached the end of the form...
            if (currentTab >= x.length) {
                // ... the form gets submitted:
                // document.getElementById("regForm").submit();
                // return false;
            }
            // Otherwise, display the correct tab:
            showTab(currentTab);
        }

        function validateForm() {
            //console.log(currentTab);
            // This function deals with validation of the form fields
            var x, y, i, valid = true;
            if (currentTab == 0) {
                let address = $('#address').val();
                console.log(address.trim());
                if (address.trim().length <= 0) {
                    toastr.error('Please fill out the address Field');
                    //console.log('I am here');
                    valid = false;

                }

            }

            if (currentTab != 2) {
                x = document.getElementsByClassName("tab");
                y = x[currentTab].getElementsByTagName("input");
                // A loop that checks every input field in the current tab:
                for (i = 0; i < y.length; i++) {
                    // If a field is empty...
                    if (y[i].value == "") {
                        // add an "invalid" class to the field:
                        y[i].className += " invalid";
                        // and set the current valid status to false
                        valid = false;
                    }
                }



            }
            if (currentTab == 1) {
                let passwd1 = $('#password1').val();
                let passwd2 = $('#password2').val();
                if (passwd1 != passwd2) {
                    toastr.error('Both the password should match');
                    valid = false;
                }
                let email = $('#email').val();
                $.ajax({
                    url: 'api/chekmail.php',
                    method: 'POST',
                    async: false,
                    data: {
                        email: email
                    },
                    success: function(res) {
                        // console.log(res);
                        if (res == 1) {
                            toastr.info('Email address already registered with Quote Master please choose another email');
                            valid = false;

                        }

                    }

                });


            }
            if (currentTab == 2) {
                toastr.success('Please select the zip codes that you want to exclude.');
            }
            // If the valid status is true, mark the step as finished and valid:
            //console.log(valid);
            if (valid) {
                document.getElementsByClassName("step")[currentTab].className += " finish";
            }
            return valid; // return the valid status
        }

        function fixStepIndicator(n) {
            // This function removes the "active" class of all steps...
            var i, x = document.getElementsByClassName("step");
            for (i = 0; i < x.length; i++) {
                x[i].className = x[i].className.replace(" active", "");
            }
            //... and adds the "active" class on the current step:
            x[n].className += " active";
        }
    </script>
    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()



            //Bootstrap Duallistbox
            $('.duallistbox').bootstrapDualListbox()



        })
        $(document).ready(function() {
            $('#email').on('input', validate);
            $("#regForm").submit(function() {
                event.preventDefault();
                err = 0;
                err_arr = new Array();
                ret_val = true;
                let state = $('#state').val();
                let county = $('#county_name').val();
                let city = $('#city').val();
                if (state.length < 1) {
                    toastr.error('Please select the State');
                    $('#state').focus();

                } else if (county.length < 1) {
                    toastr.error('Please select the County');
                    $('#county_name').focus();

                } else if (city.length < 1) {
                    toastr.error('Please select the City');
                    $('#city').focus();

                } else {
                    //toastr.info('Please select the Zip codes that you want to exclude');
                    $('#myModal').modal('toggle');
                    //console.log($('#zips option:not(:selected)'));
                    let str = '';
                    $('#zips option:not(:selected)').each(function() {
                        str += `<tr><td>${this.text}</td></tr>`;
                    });
                    $('#ziptable tbody').html(str);
                    let str1 = '';
                    $('#state option:selected').each(function() {
                        str1 += `<tr><td>${this.text}</td></tr>`;
                    });
                    $('#statetable tbody').html(str1);
                    let str2 = '';
                    $('#county_name option:selected').each(function() {
                        str2 += `<tr><td>${this.text}</td></tr>`;
                    });
                    $('#countytable tbody').html(str2);
                    let str3 = '';
                    $('#city option:selected').each(function() {
                        str3 += `<tr><td>${this.text}</td></tr>`;
                    });
                    $('#citytable tbody').html(str3);



                    //console.log(str);

                }

                if (err > 0) {
                    err_arr[0].focus();
                    ret_val = false;
                }

                e.prev


                return false;
            });



            toastr.success('Welcome To Quote Master');

            $(document).on('click', '#submitconfirm', function() {
                //alert('Hiii');
                //$("form#regForm").submit();
                document.getElementById("regForm").submit();


            });


            $(document).on('change', '#state', function() {
                let state = $('#state').val();
                $.ajax({
                    url: 'api/get_countys.php',
                    method: 'POST',
                    data: {
                        state: state

                    },
                    success: function(res) {
                        //console.log(res);
                        var dataObj = res;
                        $('#county_name').empty();

                        for (i = 0; i < dataObj.length; i++) {
                            var x = document.getElementById("county_name");
                            var option = document.createElement("option");
                            option.text = dataObj[i].county_name;
                            option.value = dataObj[i].county_name;
                            x.add(option);
                        }
                        //$('.duallistbox').bootstrapDualListbox('refresh', true);

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
                        // console.log(res);
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
                        //console.log(res);
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
                        //console.log(res);
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