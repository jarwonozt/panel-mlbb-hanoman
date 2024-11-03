<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-12">
        <?= $this->include('Layout/msgStatus') ?>
    </div>
    <div class="col-lg-4 mb-3">
        <div class="card">
            <div class="card-header bg-primary text-white p-3">
                <?= $title ?>
            </div>
            <div class="card-body">
                <?= form_open() ?>

                <div class="form-check mb-3">
                    <label class="form-check-label" data-bs-toggle="tooltip" data-bs-placement="top">
                        <input type="checkbox" class="form-check-input" name="status_not" id="status_not" value="1" <?php if ($status==1) echo 'checked' ?>>
                        Status Notification
                    </label>
                </div>

                <div class="form-group mb-3">
                    <label for="set_saldo">Text Notification</label>
                    <div class="input-group mt-2">
                        <textarea class="form-control" name="txt_not" id="txt_not" rows="5"><?= $notification ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-outline-dark">Set Notification</button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>