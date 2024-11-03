<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <?= $this->include('Layout/msgStatus') ?>
        <?php if (session()->getFlashdata('user_key')) : ?>
            <div class="alert alert-success" role="alert">
                Game : <?= session()->getFlashdata('game') ?> / <?= session()->getFlashdata('duration') / 60 ?> Hours<br>
                Available for <?= session()->getFlashdata('max_devices') ?> Devices<br>
                <small>
                    <i>Duration will start when license login.</i><br>
                    <i class="bi bi-wallet"></i> Balance Reduce :
                    <span class="text-danger">-<?= session()->getFlashdata('fees') ?></span>
                    (Total left Rp <?= $user->saldo ?>)
                </small>
                <br>
                License : <button type="button" class="btn btn-outline-dark" onClick="cpykey()">Copy Key</button>
                <br>
                <strong class="key-sensi" id="key-sensi"><?= session()->getFlashdata('gendata') ?></strong>
            </div>
            <script>
                function cpykey() { // get the container 
                    const element = document.querySelector('#key-sensi');
                    // Create a fake textarea and set the contents to the text 
                    // you want to copy 
                    const storage = document.createElement('textarea');
                    storage.value = element.innerHTML;
                    element.appendChild(storage);
                    // Copy the text in the fake textarea and remove the textarea 
                    storage.select();
                    storage.setSelectionRange(0, 99999);
                    document.execCommand('copy');
                    element.removeChild(storage);
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Key berhasil disalin',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                }
            </script>

        <?php endif; ?>
        <div class="card">
            <div class="card-header p3 bg-primary text-white">
                <div class="row">
                    <div class="col pt-1 fw-bold">
                        Create Trial Key
                    </div>
                    <div class="col text-end">
                        <a class="btn btn-sm btn-outline-light" href="<?= site_url('keys') ?>"><i class="bi bi-list"></i></a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?= form_open() ?>

                <div class="row">
                    <div class="form-group col-lg-12 mb-3">
                        <label for="game" class="form-label">Games</label>
                        <?= form_dropdown(['class' => 'form-select', 'name' => 'game', 'id' => 'game'], $game, old('game') ?: '') ?>
                        <?php if ($validation->hasError('game')) : ?>
                            <small id="help-game" class="text-danger"><?= $validation->getError('game') ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group col-lg-6 mb-3">
                        <label for="max_devices" class="form-label">Max Devices</label>
                        <input type="number" name="max_devices" id="max_devices" class="form-control" placeholder="1" value="<?= old('max_devices') ?: 10 ?>">
                        <?php if ($validation->hasError('game')) : ?>
                            <small id="help-max_devices" class="text-danger"><?= $validation->getError('max_devices') ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group col-lg-6 mb-3">
                        <label for="duration" class="form-label">Duration</label>
                        <?= form_dropdown(['class' => 'form-select', 'name' => 'duration', 'id' => 'duration'], $duration, old('duration') ?: '') ?>
                        <?php if ($validation->hasError('duration')) : ?>
                            <small id="help-duration" class="text-danger"><?= $validation->getError('duration') ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-outline-dark">Generate</button>
                </div>
                <?= form_close() ?>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function() {
        var price = JSON.parse('<?= $price ?>');
        getPrice(price);
        // When selected
        $("#max_devices, #duration, #bulk_key, #game").change(function() {
            getPrice(price);
        });
        // try to get price
        function getPrice(price) {
            var price = price;
            var device = $("#max_devices").val();
            var durate = $("#duration").val();
            var bulk = $("#bulk_key").val();
            var gprice = price[durate];
        }
    });
</script>
<?= $this->endSection() ?>