<?php if (session()->getFlashdata('msgDanger')) : ?>
    <div class="alert alert-danger fade show" role="alert">
        <?= session()->getFlashdata('msgDanger') ?>
    </div>
<?php elseif (session()->getFlashdata('msgSuccess')) : ?>
    <div class="alert alert-success fade show" role="alert">
        <?= session()->getFlashdata('msgSuccess') ?>
    </div>
<?php elseif (session()->getFlashdata('msgWarning')) : ?>
    <div class="alert alert-warning fade show" role="alert">
        <?= session()->getFlashdata('msgWarning') ?>
    </div>
<?php else : ?>
    <?php if (session()->has('userid')) : ?>
        <?php if (isset($messages)) : ?>
            <div class="alert alert-<?= $messages[1] ?> fade show" role="alert">
                <?= $messages[0] ?>
            </div>
        <?php else : ?>
            <div class="alert alert-secondary fade show" role="alert">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-6">
                        Welcome, <strong><?= getName($user) ?></strong>
                    </div>
                    <div class="col-6 d-none d-md-block text-end">
                        <?php 
                        if($panel == null){
                         ?><a href="/activation" class="btn btn-sm btn-primary">Activation Panel</a>   
                        <?php } else {?>
                            <strong class="text-primary">Panel Duration : <?= $panel ?></strong>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="alert alert-info fade show d-block d-md-none" role="alert">
            <?php 
                        if($panel == null){
                         ?><a href="/activation" class="btn btn-sm btn-primary">Activation Panel</a>   
                        <?php } else {?>
                            <strong>Panel Duration <br> <?= $panel ?></strong>
                        <?php } ?>
            </div>
        <?php endif; ?>
    <?php else : ?>
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
            Welcome Stranger
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>