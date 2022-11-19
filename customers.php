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
                    <h1 class="m-0 text-dark">Categories</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Categories </li>
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
                                <div class="col-md-10">


                                </div>
                                <div class="col-md-2">

                                    <a href="customer_edit.php" class="btn btn-success">Add New Customer</a>
                                </div>



                            </div>

                        </div>
                        <div class="card-body">

                            <div id="alert_message"></div>
                            <table class="table table-hover " id="cTable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Address</th>
                                        <th>Contact</th>

                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal fade" id="addform">
                <div class="modal-dialog ">
                    <div class="modal-content ">

                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">Add New Category</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body">
                            <form class="form-horizontal">
                                <div class="form-group row">
                                    <label for="question" class="col-sm-4 col-md-4 control-label"> Name </label>

                                    <div class="col-sm-7 col-md-7">
                                        <input type="text" class="form-control-sm" id="name">

                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="question" class="col-sm-4 col-md-4 control-label">Please select image</label>

                                    <div class="col-sm-7 col-md-7">
                                        <input type="file" class="form-control-sm" id="cat_img">

                                    </div>
                                </div>




                            </form>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btnclose btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                            <button type="button" id="BtnAdd" class="btn btn-primary btn-flat"><i class="fa fa-save"></i>
                                Save</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    $(document).ready(function() {
        fetch_data();

        function fetch_data() {
            var dataTable = $('#cTable').DataTable({
                "processing": true,
                "serverSide": true,
                "searching": false,
                "order": [],
                "scrollX": true,
                "ajax": {
                    url: "api/customers_crud.php",
                    type: "POST",
                    data: function(data) {
                        data.type = 'getall';
                        return data;
                    }
                },
                columns: [{
                        data: "Name",
                        render: function(data, type, row, meta) {
                            //console.log(row);


                            return '<a href="customer_edit.php?mode=E&id=' + row.Customer_code + '">' + data + '</a>';
                        }

                    },
                    {
                        data: "Type",
                        render: function(data, type, row, meta) {
                            //console.log(row);


                            return data;

                        }

                    },
                    {
                        data: "Location",
                        render: function(data, type, row, meta) {
                            return data;
                        }

                    },
                    {
                        data: "Address",
                        render: function(data, type, row, meta) {
                            return data;
                        }

                    },


                    {
                        data: "Contact",
                        render: function(data, type, row, meta) {
                            //console.log(row);


                            return data;
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
        $(document).on('click', '#add', function() {
            $('#addform').modal('toggle');
        });

        $(document).on('click', '#BtnAdd', function() {
            let name = $('#name').val();
            let files = $('#cat_img')[0].files;
            let fd = new FormData();
            fd.append('image', files[0]);
            fd.append('name', name);
            fd.append('type', 'add');
            $('#addform').modal('toggle');
            $.ajax({
                url: 'api/categories_crud.php',
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    Swal.fire({
                        title: 'Success!',
                        text: response,
                        type: 'success'
                    });
                    $('#cTable').DataTable().destroy();
                    fetch_data();
                }
            });

            setInterval(function() {
                $('#alert_message').html('');
            }, 5000);



        });

        $(document).on('click', '.delete', function() {
            var id = $(this).data("id");
            //alert(id);
            $.ajax({
                url: "api/categories_crud.php",
                method: "POST",
                data: {
                    id: id,
                    type: 'delete'

                },
                success: function(data) {

                    $('#alert_message').html('<div class="alert alert-success">' + data +
                        '</div>');
                    $('#cTable').DataTable().destroy();
                    fetch_data();
                }
            });
            setInterval(function() {
                $('#alert_message').html('');
            }, 5000);
        });
    });
</script>
<?php include 'footer.php' ?>