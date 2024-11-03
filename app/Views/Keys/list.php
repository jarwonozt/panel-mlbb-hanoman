<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="row">
        <div class="col-lg-12">
            <?= $this->include('Layout/msgStatus') ?>
        </div>
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="row">
                        <div class="col-6 col-md-6 col-lg-6 col-xl-6 pt-1 fw-bold">
                            Keys Registered
                        </div>
                        <div class="col-6 col-md-6 col-lg-6 col-xl-6 text-end d-flex justify-content-end">
                            <div class="dropdown">
                                <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-list"></i> MENU
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li><a class="dropdown-item" href="<?= site_url('keys/generate') ?>"><i class="bi bi-key"></i> Create Key</a></li>
                                    <?php if ($user->level == 1) : ?>
                                        <li><a class="dropdown-item" href="<?= site_url('keys/generate_trial_key') ?>"><i class="bi bi-hourglass-top"></i> Create Trial Key</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="competationKey()"><i class="bi bi-filetype-key"></i> Competation Key</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="deleteAllKeyExp()"><i class="bi bi-trash-fill"></i> Delete Expired Key</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="deleteAllKeyNULL()"><i class="bi bi-trash"></i> Delete Expired Null</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="resetAllKey()"><i class="bi bi-bootstrap-reboot"></i> Reset All Key</a></li>
                                    <?php endif; ?>

                                </ul>
                            </div>
                            <button class="btn btn-dark btn-sm ms-1" id="blur-out" data-bs-toggle="tooltip" data-bs-placement="top" title="Eye Protect"><i class="bi bi-eye-slash"></i></button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($keylist) : ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center" style="width:100%" id="datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Game</th>
                                        <th>User Keys</th>
                                        <!-- <th>User Pass</th> -->
                                        <th>Devices</th>
                                        <th>Duration</th>
                                        <th>Expired</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    <?php else : ?>
                        <p class="text-center">Nothing keys to show</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<?= link_tag("https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css") ?>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<?= script_tag("https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js") ?>

<?= script_tag("https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js") ?>
<script>
    $(document).ready(function() {
        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, "desc"]
            ],
            ajax: "<?= site_url('keys/api') ?>",
            columns: [{
                    data: 'id',
                    name: 'id_keys'
                },
                {
                    data: 'game',
                },
                {
                    data: 'user_key',
                    render: function(data, type, row, meta) {
                        var is_valid = (row.status == 'Active') ? "text-success" : "text-danger";
                        return `<span class="${is_valid} keyBlur key-sensi" id="${(row.user_key ? row.user_key : '&mdash;')}">${(row.user_key ? row.user_key : '&mdash;')}</span>&nbsp;&nbsp;<a href="javascript:void(0)" onClick="myCopy('${(row.user_key ? row.user_key : '&mdash;')}')"><i class="bi bi-copy"><i></a>`;
                    }
                },
                /*{
                    data: 'user_pass',
                    render: function(data, type, row, meta) {
                        var is_valid = (row.status == 'Active') ? "text-success" : "text-danger";
                        return `<span class="${is_valid} keyBlur key-sensi">${(row.user_pass ? row.user_pass : '&mdash;')}</span> `;
                    }
                },*/
                {
                    data: 'devices',
                    render: function(data, type, row, meta) {
                        var totalDevice = (row.devices ? row.devices : 0);
                        return `<span id="devMax-${row.user_key}">${totalDevice}/${row.max_devices}</span>`;
                    }
                },
                {
                    data: 'duration',
                    render: function(data, type, row, meta) {
                        if (row.duration == 180 || row.duration == 300 || row.duration == 600 || row.duration == 900) {
                            return (row.duration / 60) + " Hours"
                        } else {
                            return row.duration + " Days";
                        };
                    }
                },
                {
                    data: 'expired',
                    name: 'expired_date',
                    render: function(data, type, row, meta) {
                        return row.expired ? `<span class="badge text-dark">${row.expired}</span>` : '(not started yet)';
                    }
                },
                {
                    data: null,
                    render: function(data, type, row, meta) {
                        if (row.duration != 180 && row.duration != 300 && row.duration != 600 && row.duration != 900) {
                            var btnReset = `<button class="btn btn-outline-warning btn-sm" onclick="resetUserKey('${row.user_key}')"
                            data-bs-toggle="tooltip" data-bs-placement="left" title="Reset key?"><i class="bi bi-bootstrap-reboot"></i></button>`;
                        } else {
                            var btnReset = ``;
                        }

                        var btnDelete = `<button class="btn btn-outline-danger btn-sm ms-lg-1 ms-xl-1" onclick="deleteUserKey('${row.user_key}')"
                        data-bs-toggle="tooltip" data-bs-placement="left" title="Delete key?"><i class="bi bi-trash"></i></button>`;
                        if (row.duration != 180 && row.duration != 300 && row.duration != 600 && row.duration != 900) {
                            var btnEdits = `<a href="${window.location.href}/${row.id}" class="btn btn-outline-dark btn-sm"
                            data-bs-toggle="tooltip" data-bs-placement="left" title="Edit key information?"><i class="bi bi-pencil"></i></a>`;
                        } else {
                            var btnEdits = ``;
                        }

                        return `<div class="d-grid gap-2 d-md-block">${btnReset} ${btnEdits}${btnDelete}</div>`;
                    }
                }
            ]
        });

        $("#blur-out").click(function() {
            if ($(".keyBlur").hasClass("key-sensi")) {
                $(".keyBlur").removeClass("key-sensi");
                $("#blur-out").html(`<i class="bi bi-eye"></i>`);
            } else {
                $(".keyBlur").addClass("key-sensi");
                $("#blur-out").html(`<i class="bi bi-eye-slash"></i>`);
            }
        });
    });

    function resetUserKey(keys) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reset'
        }).then((result) => {
            if (result.isConfirmed) {
                Toast.fire({
                    icon: 'info',
                    title: 'Please wait...'
                })

                var base_url = window.location.href;
                var api_url = `${base_url}/reset`;
                $.getJSON(api_url, {
                        userkey: keys,
                        reset: 1
                    },
                    function(data, textStatus, jqXHR) {
                        if (textStatus == 'success') {
                            if (data.registered) {
                                if (data.isAdmin == 1) {
                                    if (data.reset) {
                                        $(`#devMax-${keys}`).html(`0/${data.devices_max}`);
                                        Swal.fire(
                                            'Reset!',
                                            'Your device key has been reset.',
                                            'success'
                                        )
                                    } else {
                                        Swal.fire(
                                            'Failed!',
                                            data.devices_total ? "You don't have any access to this user." : "User key devices already reset.",
                                            data.devices_total ? 'error' : 'warning'
                                        )
                                    }
                                } else {
                                    Swal.fire(
                                        'Failed!',
                                        "You not admin cant reset key.",
                                        'error'
                                    )
                                }
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    "User key no longer exists.",
                                    'error'
                                )
                            }
                        }
                    }
                );
            }
        });
    }

    function deleteUserKey(keys) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete'
        }).then((result) => {
            if (result.isConfirmed) {
                Toast.fire({
                    icon: 'info',
                    title: 'Please wait...'
                })

                var base_url = window.location.href;
                var api_url = `${base_url}/delete`;
                $.getJSON(api_url, {
                        userkey: keys,
                        delete: 1
                    },
                    function(data, textStatus, jqXHR) {
                        if (textStatus == 'success') {
                            if (data.registered) {
                                if (data.delete) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Your device key has been deleted.',
                                        'success'
                                    )
                                    $('#datatable').DataTable().ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Failed!',
                                        "You don't have any access to this user.",
                                        'error'
                                    )
                                }
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    "User key no longer exists.",
                                    'error'
                                )
                            }
                        }
                    }
                );
            }
        });
    }

    function competationKey() {
        const csrfName = '<?php echo csrf_token(); ?>';
        const csrfHash = '<?php echo csrf_hash(); ?>';

        Swal.fire({
            title: 'Duration',
            html: `
            <input type="number" id="durationInput" class="form-control" placeholder="ex: 1 Hour">
        `,
            focusConfirm: false,
            preConfirm: () => {
                const durationInput = document.getElementById('durationInput').value;
                if (!durationInput) {
                    Swal.showValidationMessage('You need to enter a duration');
                }
                return {
                    duration: durationInput
                };
            },
            confirmButtonText: 'Submit'
        }).then((result) => {
            if (result.isConfirmed) {
                const submittedDuration = result.value.duration;
                $.ajax({
                    url: '/keys/addcompe',
                    type: 'POST',
                    data: {
                        duration: submittedDuration,
                        [csrfName]: csrfHash
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'UPDATED',
                            text: response.message,
                            toast: false,
                            position: 'center',
                            timer: 3000, 
                            timerProgressBar: true,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred: ' + xhr.responseText,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }

    function deleteAllKeyNULL() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You will delete unused keys!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete'
        }).then((result) => {
            if (result.isConfirmed) {
                Toast.fire({
                    icon: 'info',
                    title: 'Please wait...'
                })
                var base_url = window.location.href;
                var api_url = `${base_url}/delallnull`;
                $.getJSON(api_url, {
                        deleteallnull: 1
                    },
                    function(data, textStatus, jqXHR) {
                        if (textStatus == 'success') {
                            if (data.registered) {
                                if (data.deleteallnull) {
                                    Swal.fire(
                                        'Deleted!',
                                        data.totaldel + ' keys have been deleted.',
                                        'success'
                                    )
                                    $('#datatable').DataTable().ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Failed!',
                                        "There are no unused keys.",
                                        'error'
                                    )
                                }
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    "There are no unused keys.",
                                    'error'
                                )
                            }
                        }
                    }
                );
            }
        });
    }

    function deleteAllKeyExp() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You will delete expired keys!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete'
        }).then((result) => {
            if (result.isConfirmed) {
                Toast.fire({
                    icon: 'info',
                    title: 'Please wait...'
                })
                var base_url = window.location.href;
                var api_url = `${base_url}/delallexp`;
                $.getJSON(api_url, {
                        deleteallexp: 1
                    },
                    function(data, textStatus, jqXHR) {
                        if (textStatus == 'success') {
                            if (data.registered) {
                                if (data.deleteallexp) {
                                    Swal.fire(
                                        'Deleted!',
                                        data.totaldel + ' keys have been deleted.',
                                        'success'
                                    )
                                    $('#datatable').DataTable().ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Failed!',
                                        "There are no expired keys.",
                                        'error'
                                    )
                                }
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    "There are no expired keys.",
                                    'error'
                                )
                            }
                        }
                    }
                );
            }
        });
    }

    function resetAllKey() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You will reset all keys!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reset'
        }).then((result) => {
            if (result.isConfirmed) {
                Toast.fire({
                    icon: 'info',
                    title: 'Please wait...'
                })
                var base_url = window.location.href;
                var api_url = `${base_url}/resetall`;
                $.getJSON(api_url, {
                        resetallkey: 1
                    },
                    function(data, textStatus, jqXHR) {
                        if (textStatus == 'success') {
                            if (data.registered) {
                                if (data.resetallkey) {
                                    Swal.fire(
                                        'Deleted!',
                                        data.totaldel + ' keys have been reset.',
                                        'success'
                                    )
                                    $('#datatable').DataTable().ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Failed!',
                                        "All keys are no longer used!.",
                                        'error'
                                    )
                                }
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    "All keys are no longer used!!.",
                                    'error'
                                )
                            }
                        }
                    }
                );
            }
        });
    }
</script>
<script>
    function myCopy(value) {
        var text = $('#' + value).text();
        var dummy = $('<input>').val(text);

        $('body').append(dummy);
        dummy.select();
        document.execCommand('copy');
        dummy.remove();

        Swal.fire({
            title: 'Berhasil!',
            text: 'Key berhasil disalin',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    }
</script>

<?= $this->endSection() ?>