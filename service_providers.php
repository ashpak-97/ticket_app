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
                    <h1 class="m-0 text-dark">Service Providers</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Service Providers </li>
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

                                    <!-- <button type="button" id="add" class="btn btn-success">Add New Category</button> -->
                                </div>



                            </div>

                        </div>
                        <div class="card-body">

                            <div id="alert_message"></div>
                            <table class="table table-hover " id="cTable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Date</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Company Name</th>
                                        <th>Address</th>
                                        <th>Phone</th>
                                        <th>Email Address</th>

                                        <th>Licence </th>
                                        <th>Date Of Expiry</th>
                                        <th>Insurance </th>
                                        <th>Date Of Expiry</th>
                                        <th>Email Verify</th>
                                        <th>Approve</th>

                                        <th>Action</th>
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
                        data: "pdf_file",
                        render: function(data, type, row, meta) {
                            //console.log(row);


                            if (data != '') {
                                return '<a href="<?php echo url2 ?>' + data + '" class="btn btn-success">View</a>';

                            } else {
                                return '<span>File Not Uploaded</span>';

                            }

                        }

                    },
                    {
                        data: "dDate_of_expiry",
                        render: function(data, type, row, meta) {
                            //console.log(row);


                            return data;

                        }

                    }, {
                        data: "insurance_file",
                        render: function(data, type, row, meta) {
                            //console.log(row);
                            if (data != '') {
                                return '<a href="<?php echo url2 ?>' + data + '" class="btn btn-success">View</a>';

                            } else {
                                return '<span>File Not Uploaded</span>';

                            }



                        }

                    },
                    {
                        data: "dDate_insurance",
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
                            if (row.approve == 0) {
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
                url: "api/service_providers_crud.php",
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