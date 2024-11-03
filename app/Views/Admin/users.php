<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-primary" role="alert">
            <strong>INFO</strong> &middot; Search specify user by their (username, fullname, saldo or uplink).
        </div>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('msgSuccess')) : ?>
            <div class="alert alert-success fade show" role="alert">
                <?= session()->getFlashdata('msgSuccess') ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header bg-primary fw-bold h6 p-3">
                <div class="row">
                    <div class="col pt-1 text-white">
                        Manage <?= $title ?>
                    </div>
                    <div class="col text-end">
                        <!-- <a class="btn btn-sm btn-outline-light" href="#" data-bs-toggle="modal" data-bs-target="#createUser"><i class="bi bi-person-plus-fill"></i> ADD USER</a> -->
                    </div>
                </div>
                <div class="modal fade" id="createUser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-primary">
                                <h5 class="modal-title text-white fw-bold" id="exampleModalLabel">Create User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="/admin/create-user" method="post">
                                <?= csrf_field() ?>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" name="username" id="username" class="form-control" placeholder="" aria-describedby="help-username" value="<?= old('username') ?>">
                                            <?php if ($validation->hasError('username')) : ?>
                                                <small id="help-username" class="form-text text-danger"><?= $validation->getError('username') ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="fullname" class="form-label">Fullname</label>
                                            <input type="text" name="fullname" id="fullname" class="form-control" placeholder="" aria-describedby="help-fullname" value="<?= old('fullname') ?>">
                                            <?php if ($validation->hasError('fullname')) : ?>
                                                <small id="help-fullname" class="form-text text-danger"><?= $validation->getError('fullname') ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="level" class="form-label">Roles</label>
                                            <?php $sel_level = ['' => '&mdash; Select Roles &mdash;', '1' => 'Admin', '2' => 'Reseller',]; ?>
                                            <?= form_dropdown(['class' => 'form-select', 'name' => 'level', 'id' => 'level'], $sel_level) ?>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="status" class="form-label">Status</label>
                                            <?php $sel_status = ['' => '&mdash; Select Status &mdash;', '0' => 'Banned/Block', '1' => 'Active',]; ?>
                                            <?= form_dropdown(['class' => 'form-select', 'name' => 'status', 'id' => 'status'], $sel_status) ?>
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <label for="saldo" class="form-label">Saldo</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">Rp</span>
                                                <input type="number" name="saldo" id="saldo" class="form-control" placeholder="" aria-describedby="help-saldo" value="<?= old('saldo') ?>">
                                                <?php if ($validation->hasError('saldo')) : ?>
                                                    <small id="help-saldo" class="form-text text-danger"><?= $validation->getError('saldo') ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-danger" value="Save changes">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- <?php if ($user_list) : ?> -->

                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered table-hover text-center" style="width:100%">
                        <thead>
                            <tr>
                                <th scope="row">#</th>
                                <th>Username</th>
                                <th>Fullname</th>
                                <th>Level</th>
                                <th>Saldo</th>
                                <th>Status</th>
                                <th>Uplink</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <!-- <?php endif; ?> -->

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
            ajax: "<?= site_url('admin/api/users') ?>",
            columns: [{
                    data: 'id',
                },
                {
                    data: 'username',
                },
                {
                    data: 'fullname',
                    render: function(data, type, row, meta) {
                        return (row.fullname ? row.fullname : '~');
                    }
                },
                {
                    data: 'level',
                },
                {
                    data: 'saldo',
                    render: function(data, type, row, meta) {
                        var textc = (row.level === 'Admin' ? 'primary' : 'dark');
                        var saldo = (row.level === 'Admin' ? '&mstpos;' : row.saldo);
                        return `<span class="badge text-${textc}">${saldo}</span>`;
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data, type, row, meta) {
                        var act = `<span class="text-success">Active</span>`;
                        var ban = `<span class="text-danger">Banned</span>`;
                        return (row.status == 1 ? act : ban);
                    }
                },
                {
                    data: 'uplink',
                },
                {
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<a href="${window.location.origin}/admin/user/${row.id}" class="btn btn-dark btn-sm">EDIT</a>`;
                    }
                }
            ]
        });
    });
</script>

<?= $this->endSection() ?>