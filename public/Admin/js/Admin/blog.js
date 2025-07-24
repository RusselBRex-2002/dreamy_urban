$(document).ready(function () {

    var reviewTable = $('#blog-datatable').DataTable({
        ajax: {
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            url: get_blog_datatable
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
            { data:'full_hero_image' , name:'hero_image',"render": function (data, type, full, meta) {
                return "<img src=\"" + data + "\" class=\"curve-img\" height=\"60\" width=\"60\"/>";
            },},
            { data: 'title', name: 'title' },
            { data: 'date', name: 'date' },
            { data: 'action', name: 'action', orderable: true, searchable: true },
        ]
    });

    $(document).on('click', '.delete', function () {
        var _this = $(this);
        var gallery_cat_id = _this.attr('id');
        var Table_row = $(this).closest("tr");

        Swal.fire({
            title: 'Delete Blog',
            text: 'Are you sure you want to delete this Blog ?',
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
                    url: blog_delete,
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

});
