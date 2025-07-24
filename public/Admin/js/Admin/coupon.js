$(document).ready(function () {

    var reviewTable = $('#coupon-datatable').DataTable({
        ajax: {
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            url: get_coupon_datatable
        },
        language: {
            paginate: {
                next: '>>',
                previous: '<<'
            }
        },
        processing: true,
        serverSide: true,
        responsive: true,
        columns: [
            // { responsivePriority: 1, targets: 0 },
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: true, searchable: true },
        ]
    });

    $(document).on('click', '.delete', function () {
        var _this = $(this);
        var gallery_cat_id = _this.attr('id');
        var Table_row = $(this).closest("tr");

        Swal.fire({
            title: 'Delete Coupon',
            text: 'Are you sure you want to delete this Coupon ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: coupon_delete,
                    data: {'id': gallery_cat_id},
                    success: function (data) {
                        if (data.code === 200) {
                            Swal.fire({
                                title: data.message,
                                confirmButtonColor: "#66BB6A",
                                icon: "success",
                                confirmButtonText: 'OK',
                            }).then(function () {
                                reviewTable.row(Table_row).remove().draw(false);
                            });
                        } else {
                            Swal.fire({
                                title: data.message,
                                confirmButtonColor: "#ef5350",
                                icon: "error",
                                confirmButtonText: 'OK',
                            }).then(function () {

                            });
                        }
                    }
                });
            }
        })
    });

    $(document).on('click', '.statusUpdate', function() {
        let _this = $(this);
        let status = $(this).data('status');
        const id = $(this).data('id');

        if (status == 'INACTIVE') {
            status = 'ACTIVE';
        }
         else {
            status = 'INACTIVE';
        }
        var current_status = _this.attr('current');
        if (current_status === 'PENDING') {
        Swal.fire({
            title: 'Coupon ACTIVE',
            text: 'Are you sure want to ACTIVE this Coupon' ,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ACTIVE',
            allowOutsideClick: false,
          }).then((result) => {
            if (result.isConfirmed) {
                ajaxCallForChangeStatus(_this, 'ACTIVE', id);
            }
          })
        }
        else if (current_status === 'INACTIVE'){
            Swal.fire({
                title: 'Coupon Active',
                text: "Are you sure want to ACTIVE this Coupon",
                icon:'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ACTIVE',
                allowOutsideClick: false,
              }).then((result) => {
                if (result.isConfirmed) {
                    ajaxCallForChangeStatus(_this, 'ACTIVE', id);
                }
              })
        }
        else{
            Swal.fire({
                title: 'Coupon InActive',
                text: "Are you sure want to InActive this Coupon",
                icon:'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'INACTIVE',
                allowOutsideClick: false,
              }).then((result) => {
                if (result.isConfirmed) {
                    ajaxCallForChangeStatus(_this, 'INACTIVE', id);
                }
              })
        }
    });

    function ajaxCallForChangeStatus(_this, status, opportunity_id) {

        $.ajax({
            type: "POST",
            dataType: "json",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: coupon_status_update,
            data: { 'status': status, 'id' : opportunity_id},

            success: function(data) {

                if (data.code == 200) {
                    Swal.fire({
                        title: data.message,
                        confirmButtonColor: "#66BB6A",
                        type: "success",
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonClass: 'btn btn-success',
                        customClass: 'swal_custom_class min-max-w-420'
                    }).then(function () {
                        var remove_Class = add_Class = opp_status = '';
                        if (status === 'ACTIVE') {
                            remove_Class = 'rounded-pill  bg-label-danger';
                            add_Class = 'rounded-pill bg-label-success';
                            opp_status = "INACTIVE";
                            new_title = 'Inactivate';
                        } else if (status === 'INACTIVE') {
                            remove_Class = 'rounded-pill bg-label-success';
                            add_Class = 'rounded-pill  bg-label-danger';
                            opp_status = "ACTIVE";
                            new_title = 'Activate';
                        } else if (status === 'REJECTED') {
                            remove_Class = 'rounded-pill bg-label-success';
                            add_Class = 'rounded-pill  bg-label-danger';
                            opp_status = "ACTIVE";
                            new_title = 'ACTIVE';
                        }
                        $(_this).parents('tr').find('.statusUpdate').removeClass('warning-pills').removeClass(remove_Class).addClass(add_Class).text(status).attr('title', 'INACTIVE').attr('current', status).attr('status', opp_status);
                    });

                }else {
                    Swal.fire({
                        title: data.message,
                        confirmButtonColor: "#ef5350",
                        type: "error",
                        confirmButtonText: 'OK',
                        confirmButtonClass: 'btn btn-danger',
                    }).then(function () {

                    });
                }
            }
    });
    }

});
